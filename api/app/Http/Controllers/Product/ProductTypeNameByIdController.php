<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\Products\ProductTypeService\ProductTypeService;

class ProductTypeNameByIdController extends Controller
{
    protected $ProductTypeService;

    public function __invoke(ProductTypeService $ProductTypeService, $product_id)
    {
        $this->ProductTypeService = $ProductTypeService;

        return $this->ProductTypeService->getProductTypeByName($product_id);
    }


}
