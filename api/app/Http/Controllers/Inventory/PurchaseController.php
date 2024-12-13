<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseFormRequest;
use App\Services\Inventory\PurchaseService\PurchaseService;
use Illuminate\Support\Facades\Route;
use App\Models\Purchase;
use App\Models\Price;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }
    public function index(Request $request)
    {
        $routeName = Route::currentRouteName();

        $purchase = $this->purchaseService->getAllPurchase($request->all(), $routeName);
        return response()->json($purchase);
    }

    public function store(PurchaseFormRequest $request)
    {
        return $this->purchaseService->createPurchase($request->all());

    }

    public function show($id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id);
        return response()->json($purchase);
    }

    public function update($id, Request $request)
    {

        $purchase = $this->purchaseService->updatePurchase($id, $request->all());
        return response()->json($purchase);
    }

    public function destroy($id)
    {
        return $this->purchaseService->deletePurchase($id);

    }
    public function updateEstimatedValue($id, Request $request)
    {
        // Validate the base request
        $validatedType = $request->validate([
            'type' => 'required|in:cost_price,selling_price,quantity',
            'is_actual' => 'required',
            'product_type_id' => 'required|exists:product_types,id',
            'purchase_unit_id' => 'required|uuid',
        ]);

        if ($request->type === 'cost_price') {
            return $this->updateCostPrice($request);
        } elseif ($request->type === 'selling_price') {
            return $this->updateSellingPrice($request);
        } elseif ($request->type === 'quantity') {
            return $this->getQuantity($request);
        }

        return response()->json(['message' => 'Invalid request type'], 400);
    }

    private function updateCostPrice(Request $request)
    {
        $rules = [
            'cost_price' => 'required|numeric',
        ];
        $validatedData = $request->validate($rules);

        // Retrieve the latest price record
        $latestPrice = Price::where('product_type_id', $request->product_type_id)
            ->where('purchase_unit_id', $request->purchase_unit_id)
            ->latest('id')
            ->first();

        if ($latestPrice && $latestPrice->is_cost_price_est === 0) {
            return response()->json(['message' => 'Cost price is already updated'], 400);
        }

        if ($latestPrice) {
            $newPriceData = $latestPrice->replicate()->toArray();
            $newPriceData['cost_price'] = $validatedData['cost_price'];
            $newPriceData['is_cost_price_est'] = $request->is_actual ? 0 : 1;

            Price::create($newPriceData);

            // Update the product_types table
            $this->decrementProductTypeEstimation($request->product_type_id);

            return response()->json(['message' => 'Cost price updated successfully']);
        }

        return response()->json(['message' => 'No price record found'], 404);
    }

    private function updateSellingPrice(Request $request)
    {
        $rules = [
            'selling_price' => 'required|numeric',
        ];
        $validatedData = $request->validate($rules);

        // Retrieve the latest price record
        $latestPrice = Price::where('product_type_id', $request->product_type_id)
            ->where('purchase_unit_id', $request->purchase_unit_id)
            ->latest('id')
            ->first();

        if ($latestPrice && $latestPrice->is_selling_price_est === 0) {
            return response()->json(['message' => 'Selling price is already updated'], 400);
        }

        if ($latestPrice) {
            $newPriceData = $latestPrice->replicate()->toArray();
            $newPriceData['selling_price'] = $validatedData['selling_price'];
            $newPriceData['is_selling_price_est'] = $request->is_actual ? 0 : 1;

            Price::create($newPriceData);

            // Update the product_types table
            $this->decrementProductTypeEstimation($request->product_type_id);

            return response()->json(['message' => 'Selling price updated successfully']);
        }

        return response()->json(['message' => 'No price record found'], 404);
    }

    private function getQuantity(Request $request)
    {
        $rules = [
            'quantity' => 'required|numeric',
        ];
        $validatedData = $request->validate($rules);

        return response()->json(['quantity' => $validatedData['quantity']]);
    }

    private function decrementProductTypeEstimation($productTypeId)
    {
        // Decrement the is_estimated field in the product_types table
        $productType = ProductType::find($productTypeId);

        if ($productType && $productType->is_estimated > 0) {
            $productType->decrement('is_estimated');
        }
    }



}
