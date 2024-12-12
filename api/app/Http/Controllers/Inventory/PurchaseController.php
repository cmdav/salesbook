<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseFormRequest;
use App\Services\Inventory\PurchaseService\PurchaseService;
use Illuminate\Support\Facades\Route;
use App\Models\Purchase;
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
        // Ensure 'type' is present and valid
        $validatedType = $request->validate([
            'type' => 'required|in:cost_price,selling_price,quantity',
            'is_actual' => 'required',
        ]);

        // Determine validation rules based on the request type
        $rules = [
            'type' => 'required|in:cost_price,selling_price,quantity',
            'product_type_id' => 'required|exists:product_types,id',

        ];

        if ($request['type'] === 'cost_price') {
            $rules['cost_price'] = 'required|numeric';
            $rules['purchase_unit_id'] = 'required|uuid';
        } elseif ($request['type'] === 'selling_price') {
            $rules['selling_price'] = 'required|numeric';
            $rules['selling_unit_id'] = 'required|uuid';
        } elseif ($request['type'] === 'quantity') {
            $rules['purchase_unit_id'] = 'required|uuid';
            $rules['quantity'] = 'required|numeric';
        }

        // Validate the request based on the dynamically constructed rules
        $validatedData = $request->validate($rules);

        // Multiply specific values by 2 if they are provided
        if (isset($validatedData['cost_price'])) {
            $validatedData['cost_price'] *= 2;
        }

        if (isset($validatedData['selling_price'])) {
            $validatedData['selling_price'] *= 2;
        }

        if (isset($validatedData['quantity'])) {
            $validatedData['quantity'] *= 2;
        }

        // Update the purchase using the purchase service
        // $purchase = $this->purchaseService->updatePurchase($id, $validatedData);

        // Return the updated purchase as a JSON response
        return response()->json($validatedData);
    }


}
