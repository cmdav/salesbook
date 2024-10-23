<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\ListExpiredProductService\ListExpiredProductService;
use App\Http\Requests\Products\ListExpiredProductFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListExpiredProductController extends Controller
{
    private $listExpiredProductService;

    public function __construct(ListExpiredProductService $listExpiredProductService)
    {
        $this->listExpiredProductService = $listExpiredProductService;
    }

    public function index(Request $request)
    {
        $response = $this->listExpiredProductService->index($request->all());
        if ($response["isPdf"]) {
            return $response["data"];
        } else {
            if(count($response["data"]) > 0) {
                return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $response["data"]], 200);
            }
            return response()->json(['success' => false, 'message' => 'No record found'], 404);
        }
        // if (count($data['response']) > 0) {

        //     return response()->json(['success' => true, 'message' => $data['responseMsg'], 'data' => $data['response']], 200);
        // }
        // return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
