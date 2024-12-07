<?php

namespace App\Services\Inventory\PurchaseService;

use App\Models\Purchase;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Price;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Services\Security\LogService\LogRepository;

class PurchaseRepository
{
    protected $logRepository;
    protected $username;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
        $this->username = $this->logRepository->getUsername();
    }

    private function query($branchId)
    {
        $query = Purchase::with([
            'suppliers:id,first_name,last_name',
            //'currency',
            'productType:id,product_type_name,product_type_image,product_type_description',
            'PurchaseUnit',
            'productType.subCategory:id,sub_category_name',
            'branches:id,name',
            'productType.suppliers:id,first_name,last_name,phone_number',
            'productType.activePrice' => function ($query) {
                $query->select('id', 'cost_price', 'selling_price', 'product_type_id');
            }
        ]);

        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        return $query->latest();
    }

    public function index($request)
    {
        $this->logRepository->logEvent(
            'purchases',
            'view',
            null,
            'Purchase',
            "$this->username viewed all purchases"
        );

        $branchId = 'all';
        if (isset($request['branch_id']) && auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (!in_array(auth()->user()->role->role_name, ['Admin', 'Super Admin'])) {
            $branchId = auth()->user()->branch_id;
        }

        $purchases = $this->query($branchId)->paginate(20);

        // Transform the purchases data
        $purchases->getCollection()->transform(function ($purchase) {
            return $this->transformProduct($purchase);
        });

        return $purchases;
    }

    public function searchPurchase($searchCriteria, $request)
    {
        $this->logRepository->logEvent(
            'purchases',
            'search',
            null,
            'Purchase',
            "$this->username searched for purchases with criteria: $searchCriteria"
        );

        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (!in_array(auth()->user()->role->role_name, ['Admin', 'Super Admin'])) {
            $branchId = auth()->user()->branch_id;
        }
        $Purchase = $this->query($branchId)
        ->where(function ($query) use ($searchCriteria) {
            $query->whereHas('productType', function ($q) use ($searchCriteria) {
                $q->where('product_type_name', 'like', '%' . $searchCriteria . '%');
            });
        })->get();

        $Purchase->transform(function ($Purchase) {

            return $this->transformProduct($Purchase);
        });
        return $Purchase;


    }
    private function transformProduct($purchase)
    {
        // Get cost price and selling price
        $cost_price = $purchase->price ? $purchase->price->cost_price : 0;
        $formatted_cost_price = number_format($cost_price, 2, '.', ',');

        $selling_price = $purchase->price ? $purchase->price->selling_price : 0;
        $formatted_selling_price = number_format($selling_price, 2, '.', ',');

        // Get the product type and product measurements
        $productType = $purchase->productType;

        return [
            'id' => $purchase->id,
            'product_type_name' => optional($purchase->productType)->product_type_name,
            'product_type_image' => optional($purchase->productType)->product_type_image,
            'product_type_description' => optional($purchase->productType)->product_type_description,

            // Use the new logic for purchase_unit_name and unit related info
            'purchase_unit_name' => [optional($purchase->purchaseUnit)->purchase_unit_name],
           // 'unit' => 4,

            'capacity_qty' => $purchase->capacity_qty,
            'branch_name' => optional($purchase->branches)->name,
            'batch_no' => $purchase->batch_no,
            'quantity' => $purchase->quantity,
            'expiry_date' => $purchase->expiry_date,
            'cost_price' => $formatted_cost_price,
            'selling_price' => $formatted_selling_price,
            'supplier' => optional($purchase->suppliers)->first_name . " " . optional($purchase->suppliers)->last_name,
            'created_by' => optional($purchase->creator)->first_name
                ? optional($purchase->creator)->first_name . " " . optional($purchase->creator)->last_name
                : optional($purchase->creator->organization)->organization_name,

            'updated_by' => optional($purchase->updater)->first_name
                ? optional($purchase->updater)->first_name . " " . optional($purchase->updater)->last_name
                : optional($purchase->updater->organization)->organization_name,
        ];
    }
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $purchases = [];

            foreach ($data['purchases'] as $purchaseData) {
                // Now process each purchase_unit_data, as each one needs its own Purchase record
                foreach ($purchaseData['purchase_unit_data'] as $unitData) {

                    // Create the Purchase instance for each purchase_unit_data
                    $purchase = new Purchase();
                    $purchase->product_type_id = $purchaseData['product_type_id'];
                    $purchase->supplier_id = $purchaseData['supplier_id'];
                    $purchase->batch_no = $purchaseData['batch_no'];
                    $purchase->purchase_unit_id = $unitData['purchase_unit_id'];
                    $purchase->product_identifier = $purchaseData['product_identifier'];
                    $purchase->expiry_date = $purchaseData['expiry_date'] ?? null;
                    $purchase->capacity_qty = $unitData['capacity_qty'] ?? 0; // Set the capacity_qty for each purchase unit

                    // Create the Price instance for each purchase_unit_data
                    $price = new Price();
                    $price->product_type_id = $purchaseData['product_type_id'];
                    $price->supplier_id = $purchaseData['supplier_id'];
                    $price->batch_no = $purchaseData['batch_no'];
                    $price->purchase_unit_id = $unitData['purchase_unit_id'];
                    $price->status = 1;

                    // If price_id exists, use it; otherwise, use cost_price and selling_price
                    if (!empty($unitData['price_id'])) {
                        $price->price_id = $unitData['price_id']; // Reference to existing price
                    } else {
                        $price->cost_price = $unitData['cost_price'] ?? null;
                        $price->selling_price = $unitData['selling_price'] ?? null;
                    }

                    $price->save(); // Save the price instance

                    $purchase->price_id = $price->id;
                    $purchase->save();

                    // Retrieve the purchase unit from the purchase_unit
                    $purchaseUnit = \App\Models\PurchaseUnit::with(['subPurchaseUnits:id,purchase_unit_name,unit,parent_purchase_unit_id'])
                        ->select('id', 'purchase_unit_name', 'unit')  // Select only the required fields
                        ->find($unitData['purchase_unit_id']);

                    if ($purchaseUnit) {
                        // Get the total number of smallest units for this purchase unit (recursively)
                        $totalUnit = $this->getTotalUnits($purchaseUnit);

                        // Calculate the total quantity by multiplying total units with capacity_qty
                        $totalQuantity = $purchase->capacity_qty * $totalUnit;


                        // Update or create the store entry
                        $store = \App\Models\Store::where('product_type_id', $purchaseData['product_type_id'])
                            ->where('batch_no', $purchaseData['batch_no'])
                            ->where('purchase_unit_id', $unitData['purchase_unit_id'])
                            ->where('branch_id', auth()->user()->branch_id)
                            ->first();


                        if (!$store) {
                            // Create a new Store instance if it doesn't exist
                            $store = new \App\Models\Store();
                            $store->product_type_id = $purchaseData['product_type_id'];
                            $store->batch_no = $purchaseData['batch_no'];
                            $store->purchase_unit_id = $unitData['purchase_unit_id'];
                            $store->branch_id = auth()->user()->branch_id;
                            $store->capacity_qty_available = 0;  // Initialize the available quantity
                        }

                        // Update the store capacity with the adjusted quantity
                        $store->capacity_qty_available += $totalQuantity;
                        $store->save();
                    }

                    $purchases[] = $purchase;
                }
            }

            $this->logRepository->logEvent(
                'purchases',
                'create',
                null,
                'Purchase',
                "$this->username created purchases",
                $data
            );

            DB::commit();
            return response()->json(['data' => $purchases, 'message' => 'Purchase record was added successfully'], 201);

        } catch (\Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating purchase: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Failed to create purchases'], 500);
        }
    }

    /**
     * Recursive function to calculate the total unit by traversing through the hierarchy of purchase units
     *
     * @param \App\Models\PurchaseUnit $purchaseUnit
     * @return int
     */
    private function getTotalUnits($purchaseUnit)
    {
        // Base case: If no subPurchaseUnits, return the unit itself
        if ($purchaseUnit->subPurchaseUnits->isEmpty()) {
            return $purchaseUnit->unit;
        }

        // Recursive case: Multiply the current unit with all the subPurchaseUnits' units
        $total = $purchaseUnit->unit;

        foreach ($purchaseUnit->subPurchaseUnits as $subUnit) {
            $total *= $this->getTotalUnits($subUnit); // Recurse to get sub-units total
        }

        return $total;
    }






    public function findById($id)
    {
        return Purchase::find($id);
    }

    public function delete($id)
    {
        $purchase = Purchase::find($id);
        try {
            if ($purchase) {
                // Delete matching entries in the Store table
                Store::where('product_type_id', $purchase->product_type_id)
                    ->where('batch_no', $purchase->batch_no)
                    ->delete();

                $purchase->delete();
                $this->logRepository->logEvent(
                    'purchases',
                    'delete',
                    $id,
                    'Purchase',
                    "$this->username deleted purchase with ID $id"
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Deletion successful',
                ], 200);
            }

        } catch (QueryException $e) {
            // This will catch SQL constraint violations or other query-related errors
            Log::channel('insertion_errors')->error('Error deleting purchase: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This Purchase is already in use',
            ], 500);

        }
    }





}
