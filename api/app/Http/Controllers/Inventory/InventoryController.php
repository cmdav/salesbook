<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryFormRequest;
use App\Services\Inventory\InventoryService\InventoryService;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
       $this->inventoryService = $inventoryService;
    }
    public function index()
    {
        $inventory =$this->inventoryService->getAllInventory();
        return response()->json($inventory);
    }

    public function store(InventoryFormRequest $request)
    {
        $inventory =$this->inventoryService->createInventory($request->all());
        return response()->json($inventory, 201);
    }

    public function show($id)
    {
        $inventory =$this->inventoryService->getInventoryById($id);
        return response()->json($inventory);
    }

    public function update($id, Request $request)
    {
       
        $Inventory =$this->inventoryService->updateInventory($id, $request->all());
        return response()->json($Inventory);
    }

    public function destroy($id)
    {
       $this->inventoryService->deleteInventory($id);
        return response()->json(null, 204);
    }
}
