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
       
        $store = Store::with('supplier_product:id,product_name,product_image,product_description')
        ->select('stores.supplier_product_id')
        ->addSelect([
            'quantity_remain' => Inventory::selectRaw('SUM(quantity_available)')
                ->whereColumn('supplier_product_id', 'stores.supplier_product_id')
                ->limit(1), // Subquery for remaining quantity in inventories
            'pending_request' => SupplierRequest::selectRaw('SUM(quantity)')
                ->where('status', 0)
                ->whereColumn('supplier_product_id', 'stores.supplier_product_id')
                ->limit(1), // Subquery for pending requests
            'completed_request' => SupplierRequest::selectRaw('SUM(quantity)')
                ->where('status', 1)
                ->whereColumn('supplier_product_id', 'stores.supplier_product_id')
                ->limit(1), // Subquery for completed requests
            'total_sales' => Sale::selectRaw('SUM(sales.quantity * sales.price)')
                ->join('stores as s', 's.id', '=', 'sales.store_id')
                ->whereColumn('s.supplier_product_id', 'stores.supplier_product_id')
                ->limit(1) // Subquery for total sales
        ])
        ->where('store_owner', auth()->user()->id)
        ->groupBy('stores.supplier_product_id')
        ->paginate(20);

        $store->getCollection()->transform(function($store){

            return $this->transformProduct($store);
        });


    return $store;

        //return Store::latest()->paginate(20);

    }
    private function transformProduct($supplyToCompany){

        return [
            'product_name'=>optional($supplyToCompany->supplier_product)->product_name,
            'product_image'=>optional($supplyToCompany->supplier_product)->product_image,
            'product_description'=>optional($supplyToCompany->supplier_product)->product_description,
            'quantity_remaining'=>$supplyToCompany->quantity_remain,
            'pending_request'=>$supplyToCompany->pending_request,
            'completed_request'=>$supplyToCompany->completed_request,
            'total_sales'=>$supplyToCompany->total_sales,
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
