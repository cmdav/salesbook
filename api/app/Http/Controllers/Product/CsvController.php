<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\CsvService\CsvService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use App\Imports\CurrencyImport;
use App\Imports\MeasurementImport;
use App\Imports\ProductCategoryImport;
use App\Imports\ProductSubCategoryImport;
use App\Imports\ProductImport;
use App\Imports\ProductTypeImport;
use App\Imports\SaleImport;
use App\Imports\PurchaseImport;
use App\Imports\PriceImport;





class CsvController extends Controller
{
      protected $csvService;
      protected $importClasses = [
         'Currency' => CurrencyImport::class,
         'Measurement' => MeasurementImport::class,
         'ProductCategory' => ProductCategoryImport::class,
         'ProductSubCategory'=> ProductSubCategoryImport::class,
         'Product'=> ProductImport::class,
         'ProductType'=> ProductTypeImport::class,
         'Sale'=> SaleImport::class,
         'Purchase'=> PurchaseImport::class,
         'Price'=> PriceImport::class,
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
