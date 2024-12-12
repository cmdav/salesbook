<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\Products\ProductTypeService\ProductTypeService;
use Illuminate\Http\Request;

class AllProductTypeController extends Controller
{
    protected $ProductTypeService;

    public function __invoke(ProductTypeService $ProductTypeService, Request $request)
    {
        $this->ProductTypeService = $ProductTypeService;
        $validated = $request->validate([
          'mode' => ['required', 'in:actual,estimate']
     ]);


        return $this->ProductTypeService->onlyProductTypeName($request->mode);
    }


}
