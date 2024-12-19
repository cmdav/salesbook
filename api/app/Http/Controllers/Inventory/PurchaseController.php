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
use Illuminate\Support\Facades\DB;
use App\Services\BatchNumberService;
use App\Services\Inventory\PurchaseService\PurchaseRepository;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected $purchaseService;
    protected $processPurchaseUnit;
    protected $batchNumberService;
    protected $purchaseRepository;

    public function __construct(
        PurchaseService $purchaseService,
        CalculatePurchaseUnit $calculatePurchaseUnit,
        BatchNumberService $batchNumberService,
        PurchaseRepository $purchaseRepository
    ) {
        $this->purchaseService = $purchaseService;
        $this->processPurchaseUnit = $calculatePurchaseUnit;
        $this->batchNumberService = $batchNumberService;
        $this->purchaseRepository = $purchaseRepository;
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
            'purchase_unit_id' => 'required|uuid',
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
            'purchase_unit_id' => 'required|uuid',
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
            'product_type_id' => 'required|exists:product_types,id',
            'is_actual' => 'required|boolean',
            'purchase_unit_data' => 'required|array',
            'purchase_unit_data.*.purchase_unit_id' => 'required|uuid',
            'purchase_unit_data.*.quantity' => 'required|numeric|min:1',
        ];

        $validatedData = $request->validate($rules);

        $productType = ProductType::with(['productMeasurement', 'productMeasurement.PurchaseUnit'])
            ->where('id', $validatedData['product_type_id'])
            ->first();

        if (!$productType) {
            return response()->json(['error' => 'Product type not found.'], 404);
        }

        $no_of_smallestUnit_in_each_unit = $this->processPurchaseUnit->calculatePurchaseUnits($productType->productMeasurement);
        $batchNo = $this->batchNumberService->generateBatchNumber();

        DB::beginTransaction();

        try {
            foreach ($validatedData['purchase_unit_data'] as $unitData) {
                $purchaseUnitId = $unitData['purchase_unit_id'];
                $quantity = $unitData['quantity'];

                $unitValue = null;
                foreach ($no_of_smallestUnit_in_each_unit as $unit) {
                    if ($unit['purchase_unit_id'] === $purchaseUnitId) {
                        $unitValue = $unit['value'];
                        break;
                    }
                }

                if ($unitValue === null) {
                    throw new \Exception('Invalid purchase unit ID: ' . $purchaseUnitId);
                }

                if ($validatedData['is_actual'] == 1) {

                    $this->createActualPurchase($validatedData, $unitData, $quantity, $batchNo);
                } else {
                    $updatedQuantity = $unitValue * $quantity;

                    $updated = Store::where('product_type_id', $validatedData['product_type_id'])
                        ->where('purchase_unit_id', $purchaseUnitId)
                        ->where('batch_no', 'estimated')
                        ->update(['capacity_qty_available' => $updatedQuantity]);

                    if (!$updated) {
                        throw new \Exception('Failed to update store capacity for purchase_unit_id: ' . $purchaseUnitId);
                    }
                }
            }

            DB::commit();
            $this->decrementProductTypeEstimation($request->product_type_id);
            return response()->json(['message' => 'Quantities updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    private function createActualPurchase($validatedData, $unitData, $quantity, $batchNo)
    {
        // Fetch the supplier with the name 'No supplier'
        $supplier = \App\Models\User::where('first_name', 'No supplier')->firstOrFail();



        // Get the latest cost and selling price
        $price = Price::where('product_type_id', $validatedData['product_type_id'])
            ->where('purchase_unit_id', $unitData['purchase_unit_id'])
            ->where('supplier_id', $supplier->id)
            ->where('status', 1)
            ->latest('created_at')
            ->first();

        if (!$price) {
            throw new \Exception('No price found for purchase_unit_id: ' . $unitData['purchase_unit_id']);
        }

        // Prepare purchase data
        $purchaseData = [
            'product_type_id' => $validatedData['product_type_id'],
            'batch_no' => $batchNo,
            'is_price_est' => 0,
            'supplier_id' => $supplier->id,
            'is_actual' => 1,
            'product_identifier' => '',
            'expiry_date' => $validatedData['expiry_date'] ?? null,
            'purchase_unit_data' => [
                [
                    'purchase_unit_id' => $unitData['purchase_unit_id'],
                    'capacity_qty' => $quantity,
                    'cost_price' => $price->cost_price,
                    'selling_price' => $price->selling_price,
                ],
            ],
        ];

        // Call the create method in the PurchaseRepository
        $this->purchaseRepository->create(['purchases' => [$purchaseData]]);
    }

    private function decrementProductTypeEstimation($productTypeId)
    {
        // Decrement the is_estimated field in the product_types table
        $productType = ProductType::find($productTypeId);

        if ($productType && $productType->is_estimated > 0) {
            $productType->decrement('is_estimated');

            // If is_estimated becomes less than or equal to zero, update is_displayed
            if ($productType->is_estimated <= 0) {
                $this->updateDisplayStatus($productTypeId);
            }
        }
    }

    private function updateDisplayStatus($productTypeId)
    {
        // Update `is_displayed` to 0 in the `stores` table
        Store::where('product_type_id', $productTypeId)
            ->where('batch_no', 'estimated')
            ->update(['is_displayed' => 0]);

        // Update `is_displayed` to 0 in the `purchases` table
        Purchase::where('product_type_id', $productTypeId)
            ->where('batch_no', 'estimated')
            ->update(['is_displayed' => 0]);
    }




}
