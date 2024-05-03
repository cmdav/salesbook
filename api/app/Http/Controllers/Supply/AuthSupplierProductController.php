<?php

namespace App\Http\Controllers\Supply;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierProductFormRequest;
use App\Services\Supply\SupplierProductService\SupplierProductService;
use Illuminate\Http\Request;
use App\Services\FileUploadService;

class AuthSupplierProductController extends Controller
{
      protected $supplierProductService;
      

    public function __construct(SupplierProductService $supplierProductService)
    {
        $this->supplierProductService = $supplierProductService;
       
    }
    public function index()
    {
        $supplierProduct = $this->supplierProductService->getAuthSupplierProduct();
        return response()->json($supplierProduct);
    }



    // public function show($id)
    // {
    //     $supplierProduct = $this->supplierProductService->getSupplierProductById($id);
    //     return response()->json($supplierProduct);
    // }

    // public function update($id, Request $request)
    // {
       
    //     $supplierProduct = $this->supplierProductService->updateSupplierProduct($id, $request->all());
    //     return response()->json($supplierProduct);
    // }

    // public function destroy($id)
    // {
    //     $this->supplierProductService->deleteSupplierProduct($id);
    //     return response()->json(null, 204);
    // }
}

