<?php

namespace App\Services\Inventory\PriceService;

use App\Models\Price;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Services\CalculatePurchaseUnit;

class PriceRepository
{
    protected $processPurchaseUnit;


    public function __construct(CalculatePurchaseUnit $calculatePurchaseUnit)
    {

        $this->processPurchaseUnit = $calculatePurchaseUnit;

    }
    public function index()
    {
        $Price = $this->queryCommon()->paginate(20);

        return $this->transformAndReturn($Price);
    }
    public function getAllPriceByProductType($id)
    {
        return Price::select('id', 'cost_price')->where('product_type_id', $id)->get();


    }
    // Used in the purchase page when a product is selected
    public function getLatestSupplierPrice($product_type_id, $supplier_id, $purchase_unit_id, $request)
    {
        $branchId = auth()->user()->branch_id;

        // Build the base query
        $query = Price::select(
            'id',
            'product_type_id',
            'selling_price',
            'cost_price',
            'batch_no',
            'price_id',
            'is_cost_price_est',
            'is_selling_price_est',
            'purchase_unit_id'
        )
        ->with('productType.productMeasurement.PurchaseUnit')
        ->where([
            ['product_type_id', $product_type_id],
            ['supplier_id', $supplier_id],
            ['purchase_unit_id', $purchase_unit_id],
            ['status', 1],
            ['branch_id', $branchId],
        ])
        ->latest('created_at');

        // Fetch prices
        $prices = $query->get();

        // Process and map prices
        $sellingUnitData = $prices->map(function ($price) use ($branchId, $request, $query, $purchase_unit_id) {
            $capacityQtyAvailable = null;
            $quantity = 0;

            // Fetch capacity_qty_available if mode is 'estimate'
            if ($request->mode === 'estimate') {
                $capacityQtyAvailable = Store::where([
                    ['product_type_id', $price->product_type_id],
                    ['purchase_unit_id', $price->purchase_unit_id],
                    ['batch_no', $price->batch_no],
                    ['branch_id', $branchId],
                    ['status', 1],
                ])->value('capacity_qty_available');
            }

            // Initialize cost_price and selling_price
            $costPrice = $price->cost_price;
            $sellingPrice = $price->selling_price;

            // Fetch related price if cost_price or selling_price is null
            if (is_null($costPrice) || is_null($sellingPrice)) {
                $relatedPrice = $query->where('id', $price->price_id)->first();
                $costPrice = $relatedPrice?->cost_price;
                $sellingPrice = $sellingPrice ?? $relatedPrice?->selling_price;
            }
            if ($request->mode === 'estimate') {
                $no_of_smallestUnit_in_each_unit = $this->processPurchaseUnit->calculatePurchaseUnits($price->productType->productMeasurement);
                $quantity = $this->processPurchaseUnit->calculateQuantityInAPurchaseUnit($capacityQtyAvailable, $purchase_unit_id, $no_of_smallestUnit_in_each_unit);

            }
            // Return the processed price data
            return [
                'cost_price' => $costPrice,
                'is_cost_price_est' => $price->is_cost_price_est,
                'selling_price' => $sellingPrice,
                'is_selling_price_est' => $price->is_selling_price_est,
                'price_id' => $price->id,
                'quantity' => $quantity,

            ];
        });

        // Return the response
        return response()->json($sellingUnitData, 200);
    }



    public function getLatestPriceByProductType($id)
    {

        $price = Price::select('id', 'selling_price')->where('product_type_id', $id)->where('status', 1)->first();

        // If there is no such price, then just get the latest price regardless of the status.
        if (is_null($price)) {
            $price = Price::select('id', 'selling_price')->where('product_type_id', $id)->latest('created_at') ->first();
        }

        return $price;
    }


    public function getPriceByProductType($id)
    {
        $Price = $this->queryCommon()->where('product_type_id', $id)->paginate(20);

        return $this->transformAndReturn($Price);
    }

    private function queryCommon()
    {
        return Price::with(
            'productType:id,product_type_name,product_type_image,product_type_description',
            'currency:id,currency_name,currency_symbol',
            // 'supplier:id,first_name,last_name,phone_number'
        )->orderBy('status', 'desc');
    }

    private function transformAndReturn($Price)
    {
        $Price->getCollection()->transform(function ($Price) {
            return $this->transformProduct($Price);
        });

        return $Price;
    }

    private function transformProduct($price)
    {

        return [
            'id' => $price->id,
            'product_type_id' => optional($price->productType)->id,
            'product_type_id' => optional($price->productType)->product_type_name,
            'product_type_image' => optional($price->productType)->product_type_image,
            'product_type_name' => optional($price->productType)->product_type_name,
            'product_type_description' => optional($price->productType)->product_type_description,

            'cost_price' => $price->cost_price,
            'auto_generated_selling_price' => $price->auto_generated_selling_price,
            'selling_price' => $price->selling_price,
            //'currency'=>optional($price->currency)->currency_name."(".optional($price->currency)->currency_symbol .")",
            //'discount'=>$price->discount,
            //'status'=>$price->status,
            // 'supplier_name'=>optional($price->supplier)->first_name."(".optional($price->supplier)->last_name .")",
            // 'supplier_phone_number'=>optional($price->supplier)->phone_number,
        ];

    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $price = Price::create($data);

            if ($data['status'] == 1) {
                Price::where('product_type_id', $data['product_type_id'])
                     ->where('id', '!=', $price->id)
                     ->update(['status' => 0]);
            }

            DB::commit();

            return $price;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function findById($id)
    {
        return Price::find($id);
    }

    public function update($id, array $data)
    {
        $Price = $this->findById($id);

        if ($Price) {

            $Price->update($data);
        }
        return $Price;
    }

    public function delete($id)
    {
        $Price = $this->findById($id);
        if ($Price) {

            return $Price->delete();
        }
        return null;
    }
}
