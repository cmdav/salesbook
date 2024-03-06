<?php

namespace App\Http\Controllers\Supply;
use App\Http\Controllers\Controller;
use App\Services\Supply\ProductRequestsService\ProductRequestsService;
use Illuminate\Http\Request;



class ProductRequestsController extends Controller
{
    protected $productRequestsService;
     

    public function __construct(ProductRequestsService $productRequestsService)
    {
       
        $this->productRequestsService = $productRequestsService;
       
    }
    public function index(){
      
       
        return $this->productRequestsService->getAllProductRequest();

    }
    // public function show($supplierId){

       
    //     return $this->productRequestsService->productSuppliedToCompany($supplierId);

    // }
    public function store(Request $request){
        
       
        return $this->productRequestsService->createRequest($request->all());

    }
   

   
}
