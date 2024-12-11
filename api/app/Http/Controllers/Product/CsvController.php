<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use App\Imports\CurrencyImport;
use App\Imports\ProductCategoryImport;
use App\Imports\ProductSubCategoryImport;
use App\Imports\ProductImport;
// use App\Imports\SaleImport;
// use App\Imports\PurchaseImport;
// use App\Imports\PriceImport;
// use App\Imports\PurchaseUnitImport;
use App\Services\BatchNumberService;
use App\Services\Inventory\PurchaseService\PurchaseRepository;
use App\Services\CalculatePurchaseUnit;

class CsvController extends Controller
{
    protected $importClasses = [
        'Currency' => CurrencyImport::class,
        'ProductCategory' => ProductCategoryImport::class,
        'ProductSubCategory' => ProductSubCategoryImport::class,
        'Product' => ProductImport::class,
        // 'Sale' => SaleImport::class,
        // 'Purchase' => PurchaseImport::class,
        // 'Price' => PriceImport::class,
        // 'PurchaseUnit' => PurchaseUnitImport::class,
    ];

    public function __invoke(
        Request $request,
        BatchNumberService $batchNumberService,
        PurchaseRepository $purchaseRepository,
        CalculatePurchaseUnit $calculatePurchaseUnit
    ) {
        // Validate the request
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'type' => ['required', Rule::in(array_keys($this->importClasses))],
        ]);

        try {
            // Create an instance of the appropriate import class, injecting necessary dependencies
            if ($request->type === 'Product') {
                $importClass = new ProductImport($batchNumberService, $purchaseRepository);
            } else {
                $importClass = new $this->importClasses[$request->type]();
            }

            // Import the file
            Excel::import($importClass, $request->file('file'));

            // Retrieve the actual responses after the import
            $responses = method_exists($importClass, 'getResponses') ? $importClass->getResponses() : [];

            if (empty($responses)) {
                return response()->json([
                    'message' => 'File uploaded successfully',
                    'success' => true,
                ], 201);
            }

            if ($responses['success']) {
                return response()->json([
                    'message' => 'File uploaded successfully',
                    'success' => true,
                ], 200);
            } else {
                return response()->json([
                    'message' => $responses['message'],
                    'success' => false,
                ], 500);
            }
        } catch (\Throwable $e) {
            // Catch any exceptions or errors during the import process
            return response()->json([
                'message' => 'Error in file upload' . $e->getMessage(),
                'success' => false,

            ], 500);
        }
    }

}
