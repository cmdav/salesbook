<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\DashboardStatService\DashboardStatService;
use Illuminate\Http\Request;


class DashboardStatController extends Controller
{
      protected $dashboardStatService;

    public function __invoke(DashboardStatService $dashboardStatService, Request $request)
    {
      
     
       $this->dashboardStatService = $dashboardStatService;
      
       return $this->dashboardStatService->index($request->all());
    }
   
   
}
