<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTypeFormRequest;
use App\Services\Products\ProductTypeService\ProductTypeService;
use Illuminate\Http\Request;
use App\Services\FileUploadService;


class ProductTypeController extends Controller
{
     protected $productTypeService;
     protected $fileUploadService;

    public function __construct(ProductTypeService $productTypeService, FileUploadService $fileUploadService)
    {
        $this->productTypeService = $productTypeService;
        $this->fileUploadService = $fileUploadService;
    }
    public function index()
    {
        
        $productType = $this->productTypeService->index();
        return response()->json($productType);
    }

    public function store(ProductTypeFormRequest $request)
    {
        $data = $request->all();
        // $image = $request->file('product_image');
        // $text = (new TesseractOCR($image->getPathname()))
        //     ->lang('eng') // Specify the language if necessary
        //     ->run();
        // return response()->json(['text' => $text]);

        if ($request->hasFile('product_image')) {
            $data['product_image'] = $this->fileUploadService->uploadImage($request->file('product_image'),'products');
        }
        $productType = $this->productTypeService->create($data);
        return response()->json($productType, 201);
    }

    public function show($id)
    {
        $productType = $this->productTypeService->getProductById($id);
        return response()->json($productType);
    }

    public function update($id, Request $request)
    {
       
        $productType = $this->productTypeService->update($id, $request->all());
        return response()->json($productType);
    }

    public function destroy($id)
    {
        $this->productTypeService->deleteProduct($id);
        return response()->json(null, 204);
    }
}
