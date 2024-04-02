<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Security\PageServices\PageService;
use Illuminate\Http\JsonResponse;



class AllPageController extends Controller
{
    
    protected PageService $PageService;

    public function __invoke(PageService $PageService)
    {
        
        $this->PageService = $PageService;
        if($Page =$this->PageService->names()){
            return response()->json($Page);
        }
        return response()->json(['message'=>'Page not found'], 404);
        
         

    }

   
  
   
}
