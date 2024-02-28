<?php

namespace App\Http\Controllers\Supply;
use App\Http\Controllers\Controller;
use App\Services\Supply\SupplierProductService\SupplierProductService;
use Illuminate\Http\Request;


class AllSupplierProductController extends Controller
{
      protected $supplierProductService;
     

    public function __invoke(SupplierProductService $supplierProductService)
    {
        $this->supplierProductService = $supplierProductService;
        return $this->supplierProductService->listAllSupplierProduct();
    }
 
}

