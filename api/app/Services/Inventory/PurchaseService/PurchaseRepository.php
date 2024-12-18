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
            'productType:id,product_type_name,product_type_image,product_type_description,is_estimated',
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

    public function index($request, $routeName)
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
        $purchase = $this->query($branchId);

        if($routeName == "estimated") {

            // $purchase->where(['is_actual', "!=", 0]);
            $purchase->where('is_actual', "!=", 0);
        } else {
            $purchase->where('is_actual', '=', 0);


            // $purchase->where(function ($query) {
            //     $query->whereHas('productType', function ($q) {
            //         $q->where('is_estimated', '=', 0);
            //     });
            // });

        }
        $purchases = $purchase->paginate(20);

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
        $price = $purchase->price;

        // If price_id exists and current cost_price or selling_price are null, fetch referenced price
        if ($price && $price->price_id && (is_null($price->cost_price) || is_null($price->selling_price))) {
            $referencePrice = Price::find($price->price_id);
            $cost_price = $referencePrice ? $referencePrice->cost_price : 0;
            $selling_price = $referencePrice ? $referencePrice->selling_price : 0;
        } else {
            $cost_price = $price ? $price->cost_price : 0;
            $selling_price = $price ? $price->selling_price : 0;
        }

        $formatted_cost_price = number_format($cost_price, 2, '.', ',');
        $formatted_selling_price = number_format($selling_price, 2, '.', ',');

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

                if (!empty($purchaseData['supplier_id'])) {
                    $existingRecord = \App\Models\SupplierProduct::where('product_type_id', $purchaseData['product_type_id'])
                        ->where('supplier_id', $purchaseData['supplier_id'])
                        ->first();
                    if (!$existingRecord) {
                        $supplierProduct = new \App\Models\SupplierProduct();
                        $supplierProduct->product_type_id = $purchaseData['product_type_id'];
                        $supplierProduct->supplier_id = $purchaseData['supplier_id'];
                        $supplierProduct->save();
                    }
                }

                foreach ($purchaseData['purchase_unit_data'] as $unitData) {

                    $purchase = new Purchase();
                    $purchase->product_type_id = $purchaseData['product_type_id'];
                    $purchase->supplier_id = $purchaseData['supplier_id'];
                    $purchase->batch_no = $purchaseData['batch_no'];
                    $purchase->is_actual = isset($purchaseData['is_actual']) ? $purchaseData['is_actual'] : 0;
                    $purchase->purchase_unit_id = $unitData['purchase_unit_id'];
                    $purchase->product_identifier = $purchaseData['product_identifier'];
                    $purchase->expiry_date = $purchaseData['expiry_date'] ?? null;
                    $purchase->capacity_qty = $unitData['capacity_qty'] ?? 0;


                    $price = new Price();
                    $price->product_type_id = $purchaseData['product_type_id'];
                    $price->supplier_id = $purchaseData['supplier_id'];
                    $price->batch_no = $purchaseData['batch_no'];
                    $price->purchase_unit_id = $unitData['purchase_unit_id'];
                    $price->status = 1;
                    if(isset($purchaseData['is_price_est'])) {
                        $price->is_cost_price_est = 1;
                        $price->is_selling_price_est = 1;
                    }

                    if (!empty($unitData['price_id'])) {
                        $price->price_id = $unitData['price_id'];
                    } else {
                        $price->cost_price = $unitData['cost_price'] ?? null;
                        $price->selling_price = $unitData['selling_price'] ?? null;
                    }

                    $price->save();
                    $purchase->price_id = $price->id;
                    $purchase->save();

                    $purchaseUnit = \App\Models\PurchaseUnit::with(['subPurchaseUnits:id,purchase_unit_name,unit,parent_purchase_unit_id'])
                        ->select('id', 'purchase_unit_name', 'unit')
                        ->find($unitData['purchase_unit_id']);



                    if ($purchaseUnit) {
                        // Calculate the smallest unit capacity for this purchase unit
                        $totalSmallestUnits = $this->calculateSmallestUnits($purchaseUnit, $purchase->capacity_qty);

                        // Update or create the store entry
                        $store = \App\Models\Store::where('product_type_id', $purchaseData['product_type_id'])
                            ->where('batch_no', $purchaseData['batch_no'])
                            ->where('purchase_unit_id', $unitData['purchase_unit_id'])
                            ->where('branch_id', auth()->user()->branch_id)
                            ->first();

                        if (!$store) {
                            $store = new \App\Models\Store();
                            $store->product_type_id = $purchaseData['product_type_id'];
                            $store->batch_no = $purchaseData['batch_no'];
                            $store->purchase_unit_id = $unitData['purchase_unit_id'];
                            $store->branch_id = auth()->user()->branch_id;
                            $store->capacity_qty_available = 0;
                        }

                        // Add the calculated smallest unit quantity to the store
                        $store->capacity_qty_available += $totalSmallestUnits;
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
     * Calculate the total number of smallest units for a purchase unit.
     *
     * @param \App\Models\PurchaseUnit $purchaseUnit
     * @return int
     */
    private function calculateSmallestUnits($purchaseUnit, $capacityQty)
    {
        // If there are no sub-units, return the unit and adjusted capacity
        if ($purchaseUnit->subPurchaseUnits->isEmpty()) {
            return $capacityQty;
        }

        // Calculate the total quantity in terms of the smallest unit for each sub-unit
        $totalSmallestUnits = 0;

        foreach ($purchaseUnit->subPurchaseUnits as $subUnit) {
            // Multiply the capacityQty with the sub-unit multiplier and recurse
            $totalSmallestUnits += $this->calculateSmallestUnits($subUnit, $capacityQty * $subUnit->unit);
        }

        return $totalSmallestUnits;
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
