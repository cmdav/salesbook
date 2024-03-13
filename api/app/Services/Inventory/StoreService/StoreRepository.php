<?php

namespace App\Services\Inventory\StoreService;

use App\Models\Store;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class StoreRepository 
{
    public function index()
    {
        $store = Store::with('price','productType')->paginate(20);
        // $store = Store::with('supplier_product:id,product_name,product_image,product_description')
        // ->select('stores.supplier_product_id')
        // ->addSelect([
        //     'quantity_remain' => Inventory::selectRaw('SUM(quantity_available)')
        //         ->whereColumn('supplier_product_id', 'stores.supplier_product_id')
        //         ->limit(1), // Subquery for remaining quantity in inventories
        //     'pending_request' => SupplierRequest::selectRaw('SUM(quantity)')
        //         ->where('status', 0)
        //         ->whereColumn('supplier_product_id', 'stores.supplier_product_id')
        //         ->limit(1), // Subquery for pending requests
        //     'completed_request' => SupplierRequest::selectRaw('SUM(quantity)')
        //         ->where('status', 1)
        //         ->whereColumn('supplier_product_id', 'stores.supplier_product_id')
        //         ->limit(1), // Subquery for completed requests
        //     'total_sales' => Sale::selectRaw('SUM(sales.quantity * sales.price)')
        //         ->join('stores as s', 's.id', '=', 'sales.store_id')
        //         ->whereColumn('s.supplier_product_id', 'stores.supplier_product_id')
        //         ->limit(1) // Subquery for total sales
        // ])
        // ->where('store_owner', auth()->user()->id)
        // ->groupBy('stores.supplier_product_id')
        // ->paginate(3);

        $store->getCollection()->transform(function($store){

            return $this->transformProduct($store);
        });


        return $store;

        //return Store::latest()->paginate(3);

    }
    private function transformProduct($store) {
        return [
            'id' => $store->id,
            'product_type_id' => $store->product_type_id,
            'price_id' => $store->price_id,
            'store_owner' => $store->store_owner,
            'quantity_available' => $store->quantity_available,
            'store_type' => $store->store_type,
            'status' => $store->status,
            // 'created_by' => $store->created_by,
            // 'updated_by' => $store->updated_by,
            // 'created_at' => $store->created_at,
            // 'updated_at' => $store->updated_at,
            // Flatten price details
            'price_cost_price' => optional($store->price)->cost_price,
            'price_selling_price' => round(
                optional($store->price)->system_price > 0 ?
                optional($store->price)->cost_price + (optional($store->price)->cost_price * optional($store->price)->system_price) / 100 :
                optional($store->price)->selling_price
            ),
            
            'price_system_price' => optional($store->price)->system_price,

            'price_currency_id' => optional($store->price)->currency_id,
            'price_discount' => optional($store->price)->discount,
            'price_status' => optional($store->price)->status,
            // 'price_organization_id' => optional($store->price)->organization_id,
            // 'price_created_by' => optional($store->price)->created_by,
            // 'price_updated_by' => optional($store->price)->updated_by,
            // 'price_created_at' => optional($store->price)->created_at,
            // 'price_updated_at' => optional($store->price)->updated_at,
            // Flatten product type details
            'product_type_product_id' => optional($store->productType)->product_id,
            'product_type' => optional($store->productType)->product_type,
            // 'product_type_image' => optional($store->productType)->product_type_image,
            // 'product_type_description' => optional($store->productType)->product_type_description,
            // 'product_type_organization_id' => optional($store->productType)->organization_id,
            // 'product_type_supplier_id' => optional($store->productType)->supplier_id,
            // 'product_type_created_by' => optional($store->productType)->created_by,
            // 'product_type_updated_by' => optional($store->productType)->updated_by,
            // 'product_type_created_at' => optional($store->productType)->created_at,
            // 'product_type_updated_at' => optional($store->productType)->updated_at
        ];
    }
    






    
    public function create(array $data)
    {
       
        return Store::create($data);
    }

    public function findById($id)
    {
        return Store::find($id);
    }

    public function update($id, array $data)
    {
        $store = $this->findById($id);
      
        if ($store) {

            $store->update($data);
        }
        return $store;
    }

    public function delete($id)
    {
        $store = $this->findById($id);
        if ($store) {
            return $store->delete();
        }
        return null;
    }
}
