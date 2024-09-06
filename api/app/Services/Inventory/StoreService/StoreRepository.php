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
    private function query($branchId)
    {

        $query = Store::with('productType', 'branches:id,name');
        if ($branchId !== 'all') {
            // Apply the where clause if branch_id is not 'all' and the user is not admin
            $query->where('branch_id', $branchId);
        }
        return $query->latest();
    }
    public function index($request)
    {
        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif(auth()->user()->role->role_name != 'Admin') {
            $branchId = auth()->user()->branch_id;
        }
        $store = $this->query($branchId)->paginate(20);

        $store->getCollection()->transform(function ($store) {

            return $this->transformProduct($store);
        });


        return $store;

        //return Store::latest()->paginate(3);

    }
    public function searchStore($searchCriteria, $request)
    {
        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif(auth()->user()->role->role_name != 'Admin') {
            $branchId = auth()->user()->branch_id;
        }
        $store = $this->query($branchId)->where(function ($query) use ($searchCriteria) {
            $query->whereHas('productType', function ($q) use ($searchCriteria) {
                $q->where('product_type_name', 'like', '%' . $searchCriteria . '%');
            });
        })->get();

        $store->transform(function ($store) {

            return $this->transformProduct($store);
        });


        return $store;

        //return Store::latest()->paginate(3);

    }
    private function transformProduct($store)
    {
        return [
            'id' => $store->id,

            'product_type' => optional($store->productType)->product_type_name,
            'product_description' => optional($store->productType)->product_type_description,
            //'store_owner' => $store->store_owner,
            'batch_no' => $store->batch_no,
            'branch_name' => optional($store->branches)->name,
            'quantity_available' => $store->capacity_qty_available,
            //'store_type' => $store->store_type,
            'status' => $store->capacity_qty_available > 0 ? 'Available' : 'Not Available',

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
    public function getitemList($request)
    {
        // Fetch the branch ID as done in the index method
        $branchId = 'all';
        if (isset($request['branch_id']) && auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (auth()->user()->role->role_name != 'Admin') {
            $branchId = auth()->user()->branch_id;
        }

        // Fetch the start and end date from the request
        $startDate = isset($request['start_date']) ? $request['start_date'] : null;
        $endDate = isset($request['end_date']) ? $request['end_date'] : null;

        // Create the query and apply filters
        $storeQuery = $this->query($branchId);

        if ($startDate && $endDate) {
            // Apply date filters on 'created_at' or any other date column you want to filter on
            $storeQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Check if the 'all' parameter is present in the request
        if (isset($request['all']) && $request['all'] == true) {

            // Return all data for the given duration without pagination
            $store = $storeQuery->get();
            // Transform the entire collection
            $transformedStore = $store->map(function ($store) {
                return $this->transformProduct($store);
            });

            return $transformedStore;
        }

        // Paginate and transform the store data if 'all' is not present
        $store = $storeQuery->paginate(20);

        $store->getCollection()->transform(function ($store) {
            return $this->transformProduct($store);
        });

        return $store;
    }


}
