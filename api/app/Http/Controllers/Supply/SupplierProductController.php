<?php

namespace App\Http\Controllers\Supply;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierProductFormRequest;
use App\Services\Supply\SupplierProductService\SupplierProductService;
use Illuminate\Http\Request;
use App\Services\FileUploadService;

class SupplierProductController extends Controller
{
      protected $supplierProductService;
      protected $fileUploadService;


    public function __construct(SupplierProductService $supplierProductService, FileUploadService $fileUploadService)
    {
        $this->supplierProductService = $supplierProductService;
        $this->fileUploadService = $fileUploadService;
    }
    public function index(Request $request)
    {
        $supplierProduct = $this->supplierProductService->getAllSupplierProduct($request);
        return response()->json($supplierProduct);
    }

    public function store(SupplierProductFormRequest $request)
    {
      
        $data = $request->all();

        if ($request->hasFile('product_image')) {
            $data['product_image'] = $this->fileUploadService->uploadImage($request->file('product_image'),'supplier_products');
        }
        $product = $this->supplierProductService->createSupplierProduct($data);
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $supplierProduct = $this->supplierProductService->getSupplierProductById($id);
        return response()->json($supplierProduct);
    }

    public function update($id, Request $request)
    {
       
        $supplierProduct = $this->supplierProductService->updateSupplierProduct($id, $request->all());
        return response()->json($supplierProduct);
    }

    public function destroy($id)
    {
        $this->supplierProductService->deleteSupplierProduct($id);
        return response()->json(null, 204);
    }
}

