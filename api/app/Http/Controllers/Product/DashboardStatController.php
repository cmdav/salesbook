<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\DashboardStatService\DashboardStatService;



class DashboardStatController extends Controller
{
      protected $dashboardStatService;

    public function __invoke(DashboardStatService $dashboardStatService)
    {
      
       $this->dashboardStatService = $dashboardStatService;
      
       return $this->dashboardStatService->index();
    }
   
   
}
