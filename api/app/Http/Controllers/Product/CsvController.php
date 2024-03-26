<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\CsvService\CsvService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CurrencyImport;
use App\Imports\MeasurementImport;
use Illuminate\Validation\Rule;




class CsvController extends Controller
{
      protected $csvService;
      protected $importClasses = [
         'Currency' => CurrencyImport::class,
         'Measurement' => MeasurementImport::class,
     ];

    public function __invoke(CsvService $csvService, Request $request)
    {
      
       $this->csvService = $csvService;

           
      $request->validate([
         'file' => 'required|file|mimes:csv,txt,', 
         'type' => ['required', Rule::in(array_keys($this->importClasses))],
       ]);
       
       $importClass = new $this->importClasses[$request->type];

       Excel::import($importClass, $request->file('file'));
 
       return response()->json(['message'=>'File uploaded successful'], 200);
      
      
    }
   
   
}
