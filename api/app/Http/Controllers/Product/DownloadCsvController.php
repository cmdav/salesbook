<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class DownloadCsvController extends Controller
{
    // List of accepted filenames
    private $acceptedFilenames = [
        'currency',
        'product_category',
        'product_sub_category',
        'purchase_unit',
    ];

    public function __invoke(Request $request, $fileName)
    {
        // Validate the fileName from the route parameter
        if (!in_array($fileName, $this->acceptedFilenames)) {
            return response()->json(['message' => 'Invalid file name.'], 422);
        }

        // Define the path to the csv folder in the root directory
        $filePath = base_path("csv/{$fileName}.csv");

        // Check if the file exists
        if (!File::exists($filePath)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        // Return the file as a download response
        return response()->download($filePath);
    }
}
