<?php

namespace App\Services\Products\TotalSaleReportService;

use App\Services\Inventory\SaleService\SaleRepository;

class TotalSaleReportService
{
    protected $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->saleRepository->gettotalSaleReport($data, $id);
    }
}
