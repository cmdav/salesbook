<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFormRequest;
use App\Services\Products\ProductService\ProductService;
use Illuminate\Http\Request;
use App\Services\FileUploadService;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ProductController extends Controller
{
     protected $productService;
     protected $fileUploadService;

    public function __construct(ProductService $productService, FileUploadService $fileUploadService)
    {
        $this->productService = $productService;
        $this->fileUploadService = $fileUploadService;
    }
    public function index()
    {
        $product = $this->productService->getAllProduct();
        return response()->json($product);
    }

    public function store(ProductFormRequest $request)
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
        $product = $this->productService->createProduct($data);
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return response()->json($product);
    }

    public function update($id, Request $request)
    {
       
        $product = $this->productService->updateProduct($id, $request->all());
        return response()->json($product);
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return response()->json(null, 204);
    }
}
