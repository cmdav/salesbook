<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormRequest;
use App\Services\Inventory\StoreService\StoreService;
use App\Models\Store;
use Illuminate\Http\Request;

class SearchStoreController extends Controller
{
     protected $storeService;

    public function __construct(StoreService $storeService)
    {
       $this->storeService = $storeService;
    }
   
    public function show($id)
    {
        $store =$this->storeService->searchStore($id);
        return response()->json($store);
    }

    
}
