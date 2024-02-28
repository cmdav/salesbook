<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormRequest;
use App\Services\Inventory\StoreService\StoreService;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
     protected $storeService;

    public function __construct(StoreService $storeService)
    {
       $this->storeService = $storeService;
    }
    public function index()
    {
        $store =$this->storeService->getAllStore();
        return response()->json($store);
    }

    public function store(StoreFormRequest $request)
    {
        $store =$this->storeService->createStore($request->all());
        return response()->json($store, 201);
    }

    public function show($id)
    {
        $store =$this->storeService->getStoreById($id);
        return response()->json($store);
    }

    public function update($id, Request $request)
    {
       
        $store =$this->storeService->updateStore($id, $request->all());
        return response()->json($store);
    }

    public function destroy($id)
    {
       $this->storeService->deleteStore($id);
        return response()->json(null, 204);
    }
}
