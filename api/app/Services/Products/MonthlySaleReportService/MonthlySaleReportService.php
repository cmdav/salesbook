<?php

namespace App\Services\Products\MonthlySaleReportService;

use App\Services\Inventory\SaleService\SaleRepository;

class MonthlySaleReportService
{
    protected $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->saleRepository->getmonthlySaleReport($data, $id);
    }
}
