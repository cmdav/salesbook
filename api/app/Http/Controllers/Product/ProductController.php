<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFormRequest;
use App\Services\Products\ProductService\ProductService;
use App\Services\Products\ProductTypeService\ProductTypeService;
use Illuminate\Http\Request;
use App\Services\FileUploadService;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
     protected $productService;
     protected $fileUploadService;
     protected $productTypeService;

    public function __construct(ProductService $productService, FileUploadService $fileUploadService, ProductTypeService $productTypeService)
    {
        $this->productService = $productService;
        $this->productTypeService = $productTypeService;
        $this->fileUploadService = $fileUploadService;
    }
    public function index()
    {
        $product = $this->productService->getAllProduct();
        return response()->json($product);
    }

    // public function store(ProductFormRequest $request)
    // {
    //     $data = $request->all();
    //     // $image = $request->file('product_image');
    //     // $text = (new TesseractOCR($image->getPathname()))
    //     //     ->lang('eng') // Specify the language if necessary
    //     //     ->run();
    //     // return response()->json(['text' => $text]);

    //     if ($request->hasFile('product_image')) {
    //         $data['product_image'] = $this->fileUploadService->uploadImage($request->file('product_image'),'products');
    //     }
    //     $product = $this->productService->createProduct($data);
    //     return response()->json($product, 201);
    // }
    public function store(ProductFormRequest $request)
{
    DB::beginTransaction(); // Start the transaction

    try {
        $data = $request->all();

        if ($request->hasFile('product_image')) {
            $data['product_image'] = $this->fileUploadService->uploadImage($request->file('product_image'), 'products');
        }

        $product = $this->productService->createProduct($data);
       
        // Now, use the created product's details to create a related product type
        $productTypeData = [
            'product_id' => $product->id,
            'product_type_name' => $product->product_name,
            'product_type_image' => $product->product_image,
            'product_type_description' => $product->product_description,
            'organization_id' => null, // Set this accordingly
            'supplier_id' => null, // Set this accordingly
            'created_by' => $product->created_by, // Assuming you have this field set in your product creation
            'updated_by' => $product->updated_by, // Assuming you have this field set
        ];

        // Use your product type service or repository to create the product type
        $productType = $this->productTypeService->create($productTypeData);

        DB::commit(); // Commit the transaction

        return response()->json(['product' => $product, 'productType' => $productType], 201);
    } catch (\Exception $e) {
        DB::rollBack(); // Rollback the transaction on any error
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return response()->json($product);
    }

    public function update($id, Request $request)
    {
        $data = $request->all();
       
        if ($request->hasFile('product_image')) {
            $data['product_image'] = $this->fileUploadService->uploadImage($request->file('product_image'),'products');
        }
        $product = $this->productService->updateProduct($id, $data);
        return response()->json($product);
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return response()->json(null, 204);
    }
}
