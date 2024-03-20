<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\CsvService\CsvService;



class CsvController extends Controller
{
      protected $csvService;

    public function __invoke(CsvService $csvService)
    {
      
       $this->csvService = $csvService;
      
       return $this->csvService->index();
    }
   
   
}
