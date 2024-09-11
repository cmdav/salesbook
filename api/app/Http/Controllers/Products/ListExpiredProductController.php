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
        $data = $this->listExpiredProductService->index($request->all());
        // return $data;

        if (count($data['response']) > 0) {
            return response()->json(['success' => true, 'message' => $data['responseMsg'], 'data' => $data['response']], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
