<?php

namespace App\Services\Products\DashboardStatService;
use App\Services\Products\DashboardStatService\DashboardStatRepository;


class DashboardStatService 
{
    protected $dashboardStatRepository;

    public function __construct(DashboardStatRepository $dashboardStatRepository)
    {
		
        $this->dashboardStatRepository = $dashboardStatRepository;
    }
	public function index()
    {
      
        return $this->dashboardStatRepository->index();
    }
    
}
