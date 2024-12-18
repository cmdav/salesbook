<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseFormRequest;
use App\Services\Inventory\PurchaseService\PurchaseService;
use Illuminate\Support\Facades\Route;
use App\Models\Purchase;
use App\Models\Price;
use App\Models\ProductType;
use App\Models\Store;
use App\Services\CalculatePurchaseUnit;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected $purchaseService;
    protected $processPurchaseUnit;

    public function __construct(PurchaseService $purchaseService, CalculatePurchaseUnit $calculatePurchaseUnit)
    {
        $this->purchaseService = $purchaseService;
        $this->processPurchaseUnit = $calculatePurchaseUnit;
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
            ->where('batch_no', 'estimated')
            ->latest('id')
            ->first();

        if ($latestPrice && $latestPrice->is_cost_price_est === 0) {
            return response()->json(['message' => 'Cost price is already updated'], 400);
        }

        if ($latestPrice) {
            $newPriceData = $latestPrice->replicate()->toArray();

            // Update cost price and estimation status
            $newPriceData['cost_price'] = $validatedData['cost_price'];
            $newPriceData['is_cost_price_est'] = $request->is_actual ? 0 : 1;

            // Ensure the status field has a valid integer value
            $newPriceData['status'] = 1; // Set status to 'Active' (assuming 1 means active)

            // Remove any fields that should not be replicated or cause issues
            unset($newPriceData['id'], $newPriceData['created_at'], $newPriceData['updated_at']);

            // Create the new price record
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
            ->where('batch_no', 'estimated')
            ->latest('id')
            ->first();

        if ($latestPrice && $latestPrice->is_selling_price_est === 0) {
            return response()->json(['message' => 'Selling price is already updated'], 400);
        }

        if ($latestPrice) {
            $newPriceData = $latestPrice->replicate()->toArray();

            // Update selling price and estimation status
            $newPriceData['selling_price'] = $validatedData['selling_price'];
            $newPriceData['is_selling_price_est'] = $request->is_actual ? 0 : 1;

            // Ensure the status field has a valid integer value
            $newPriceData['status'] = 1; // Set status to 'Active' (assuming 1 means active)

            // Remove any fields that should not be replicated or cause issues
            unset($newPriceData['id'], $newPriceData['created_at'], $newPriceData['updated_at']);

            // Create the new price record
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
        $purchaseUnitId = $request['purchase_unit_id'];
        $quantity = $validatedData['quantity'];

        // Retrieve the product type with related product measurements
        $productType = ProductType::with(['productMeasurement', 'productMeasurement.PurchaseUnit'])
            ->where('id', $request['product_type_id'])
            ->first();

        if (!$productType) {
            return response()->json(['error' => 'Product type not found.'], 404);
        }

        // Calculate the number of smallest units in each purchase unit
        $no_of_smallestUnit_in_each_unit = $this->processPurchaseUnit->calculatePurchaseUnits($productType->productMeasurement);

        // Find the matching purchase unit value
        $unitValue = null;
        foreach ($no_of_smallestUnit_in_each_unit as $unit) {
            if ($unit['purchase_unit_id'] === $purchaseUnitId) {
                $unitValue = $unit['value'];
                break;
            }
        }

        if ($unitValue === null) {
            return response()->json(['error' => 'Invalid purchase unit ID.'], 400);
        }

        // Multiply the unit value by the requested quantity
        $updatedQuantity = $unitValue * $quantity;

        // Update the store capacity_qty_available

        $updated = Store::where('product_type_id', $request['product_type_id'])
    ->where('purchase_unit_id', $purchaseUnitId)
    ->where('batch_no', 'estimated')
    ->increment('capacity_qty_available', $updatedQuantity);

        if ($updated) {
            return response()->json(['quantity' => $validatedData['quantity']]);
        } else {
            return response()->json(['error' => 'Failed to update store capacity.'], 500);
        }
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
