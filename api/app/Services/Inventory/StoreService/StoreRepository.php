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
    private function query($branchId){

        $query= Store::with('productType','branches:id,name');
        if ($branchId !== 'all') {
            // Apply the where clause if branch_id is not 'all' and the user is not admin
            $query->where('branch_id', $branchId);
        }
        return $query->latest();
    }
    public function index($request)
    {
        $branchId = 'all';
            if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin'){
                $branchId = $request['branch_id']; 
            }
            else if(auth()->user()->role->role_name != 'Admin'){
                $branchId = auth()->user()->branch_id; 
            }
        $store = $this->query($branchId)->paginate(20);

        $store->getCollection()->transform(function($store){

            return $this->transformProduct($store);
        });


        return $store;

        //return Store::latest()->paginate(3);

    }
    public function searchStore($searchCriteria, $request)
    {
        $branchId = 'all';
            if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin'){
                $branchId = $request['branch_id']; 
            }
            else if(auth()->user()->role->role_name != 'Admin'){
                $branchId = auth()->user()->branch_id; 
            }
        $store = $this->query($branchId)->where(function($query) use ($searchCriteria) {
            $query->whereHas('productType', function($q) use ($searchCriteria) {
                $q->where('product_type_name', 'like', '%' . $searchCriteria . '%');
            });
        })->get();

        $store->transform(function($store){

            return $this->transformProduct($store);
        });


        return $store;

        //return Store::latest()->paginate(3);

    }
    private function transformProduct($store) {
        return [
            'id' => $store->id,
            
            'product_type' => optional($store->productType)->product_type_name,
            'product_description' => optional($store->productType)->product_type_description,
            //'store_owner' => $store->store_owner,
            'batch_no' => $store->batch_no,
            'branch_name' => optional($store->branches)->name,
            'quantity_available' => $store->quantity_available,
            //'store_type' => $store->store_type,
            'status' => $store->quantity_available > 0 ? 'Available' : 'Not Available',
            // 'created_by' => $store->created_by,
            // 'updated_by' => $store->updated_by,
            // 'created_at' => $store->created_at,
            // 'updated_at' => $store->updated_at,
            // Flatten price details
           
            // 'price_organization_id' => optional($store->price)->organization_id,
            // 'price_created_by' => optional($store->price)->created_by,
            // 'price_updated_by' => optional($store->price)->updated_by,
            // 'price_created_at' => optional($store->price)->created_at,
            // 'price_updated_at' => optional($store->price)->updated_at,
            // Flatten product type details
            //'product_type_product_id' => optional($store->productType)->product_id,
         
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
