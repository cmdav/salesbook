<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\CsvService\CsvService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CurrencyImport;



class CsvController extends Controller
{
      protected $csvService;

    public function __invoke(CsvService $csvService, Request $request)
    {
      
       $this->csvService = $csvService;

           
      $request->validate([
         'file' => 'required|file|mimes:csv,txt,', 
       ]);
       
       Excel::import(new CurrencyImport, request()->file('file'));
 
       return "Success";
      
       //return $this->csvService->index();
    }
   
   
}
