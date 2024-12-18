<?php

namespace App\Services\Inventory\PriceService;

use App\Models\PriceNotification;
use App\Models\Price;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Services\Security\LogService\LogRepository;
use App\Services\CalculatePurchaseUnit;

class PriceNotificationRepository
{
    protected $logRepository;
    protected $username;
    protected $processPurchaseUnit;

    public function __construct(LogRepository $logRepository, CalculatePurchaseUnit $calculatePurchaseUnit)
    {
        $this->logRepository = $logRepository;
        $this->username = $this->logRepository->getUsername();
        $this->processPurchaseUnit = $calculatePurchaseUnit;
    }

    public function index($request)
    {
        $this->logRepository->logEvent(
            'price_notifications',
            'view',
            null,
            'PriceNotification',
            "$this->username viewed all price notifications"
        );

        $query = PriceNotification::select('id', 'product_type_id', 'supplier_id', 'cost_price', 'selling_price', 'status')
                                  ->with(
                                      'productTypes:id,product_type_name,product_type_image,product_type_description',
                                      'supplier:id,first_name,last_name,phone_number',
                                      'branches:id,name'
                                  );


        $priceNotification = $query->latest()->paginate(20);

        $priceNotification->getCollection()->transform(function ($Price) {
            return $this->transformProduct($Price);
        });


        return  $priceNotification;
        // }


    }

    private function transformProduct($price)
    {

        return [
            'id' => $price->id,
            'product_type_name' => optional($price->productTypes)->product_type_name,
            'product_type_image' => optional($price->productTypes)->product_type_image,
            //'branch_name' => optional($store->branches)->name,
            'product_type_description' => optional($price->productTypes)->product_type_description,
            'cost_price' => $price->cost_price,
            'selling_price' => $price->selling_price,
            'status' => $price->status,
            'supplier_detail' =>  optional($price->supplier)->first_name." ".optional($price->supplier)->last_name." ".  optional($price->supplier)->contact_person,
            'supplier_phone_number' => optional($price->supplier)->phone_number

        ];

    }

    public function create(array $data)
    {
        return PriceNotification::UpdateOrCreate(
            [
                'supplier_id' => $data['supplier_id'],
            'product_type_id' => $data['product_type_id']
            ],
            $data
        );

        $this->logRepository->logEvent(
            'price_notifications',
            'create',
            $priceNotification->id,
            'PriceNotification',
            "$this->username created a price notification with ID {$priceNotification->id}",
            $data
        );

    }


    public function show($id)
    {
        return PriceNotification::find($id);
    }

    public function update($id, array $data)
    {
        $priceNotification = PriceNotification::find($id);

        if ($priceNotification) {
            $this->logRepository->logEvent(
                'price_notifications',
                'update',
                $id,
                'PriceNotification',
                "$this->username updated the price notification with ID $id",
                $data
            );

            if ($data['status'] == 'accepted') {
                // Fetch all stores and calculate unit values
                $productType = \App\Models\ProductType::select("id", "product_type_name")
                    ->with(['productMeasurement' => function ($query) {
                        $query->select('id', 'product_type_id', 'purchasing_unit_id');
                    }])
                    ->where('id', $priceNotification->product_type_id)
                    ->first();

                // Calculate units and max unit
                $units = $this->processPurchaseUnit->calculatePurchaseUnits($productType->productMeasurement);
                $maxUnit = collect($units)->sortByDesc('value')->first();
                $maxValue = $maxUnit['value'];
                $maxPurchaseUnitId = $maxUnit['purchase_unit_id'];

                $baseCostPrice = $priceNotification->cost_price;
                $baseSellingPrice = $data['selling_price'];

                // Process each product measurement
                foreach ($productType->productMeasurement as $measurement) {
                    $purchaseUnitId = $measurement->purchasing_unit_id;

                    // Find the value of the current unit
                    $unitValue = collect($units)->firstWhere('purchase_unit_id', $purchaseUnitId)['value'] ?? 1;

                    // Fetch the old price record
                    $oldPrice = Price::where('supplier_id', $priceNotification->supplier_id)
                        ->where('product_type_id', $priceNotification->product_type_id)
                        ->where('purchase_unit_id', $purchaseUnitId)
                        ->first();

                    if ($oldPrice) {
                        // Deactivate the old price record
                        $oldPrice->update(['status' => 0, 'is_new' => 0]);

                        // Determine new prices
                        if ($purchaseUnitId == $maxPurchaseUnitId) {
                            $newCostPrice = $baseCostPrice;
                            $newSellingPrice = $baseSellingPrice;
                        } else {
                            // Scale prices based on unit value ratio
                            $ratio = $unitValue / $maxValue;
                            $newCostPrice = round($baseCostPrice * $ratio, 2);
                            $newSellingPrice = round($baseSellingPrice * $ratio, 2);
                        }

                        // Insert new price for this purchase unit
                        Price::create([
                            'supplier_id' => $priceNotification->supplier_id,
                            'product_type_id' => $priceNotification->product_type_id,
                            'purchase_unit_id' => $purchaseUnitId,
                            'status' => 1,
                            'cost_price' => $newCostPrice,
                            'selling_price' => $newSellingPrice,
                            'new_cost_price' => $newCostPrice,
                            'new_selling_price' => $newSellingPrice,
                            'is_new' => 1,
                            'price_id' => $oldPrice->id, // Pass old price ID
                            'batch_no' => $oldPrice->batch_no, // Pass old batch number
                            'currency_id' => $data['currency_id'] ?? null,
                            'organization_id' => $data['organization_id'] ?? null,
                            'created_by' => $data['created_by'] ?? auth()->id(),
                            'updated_by' => $data['updated_by'] ?? auth()->id(),
                        ]);
                    }
                }
            }
        }

        return $priceNotification;
    }




    public function delete($id)
    {
        $Price = PriceNotification::find($id);
        if ($Price) {

            return $Price->delete();
        }

    }
}
