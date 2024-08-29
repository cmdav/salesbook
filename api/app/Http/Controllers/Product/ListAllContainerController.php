<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\ListAllContainerService\ListAllContainerService;
use App\Http\Requests\Product\ListAllContainerFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListAllContainerController extends Controller
{
    private $listAllContainerService;

    public function __construct(ListAllContainerService $listAllContainerService)
    {
        $this->listAllContainerService = $listAllContainerService;
    }

    public function index()
    {
        $data = $this->listAllContainerService->index();
        if ($data) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
