<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Product\ContainerWithCapacityService\ContainerWithCapacityService;
use App\Http\Requests\Product\ContainerWithCapacityFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContainerWithCapacityController extends Controller
{
    private $containerWithCapacityService;

    public function __construct(ContainerWithCapacityService $containerWithCapacityService)
    {
        $this->containerWithCapacityService = $containerWithCapacityService;
    }

    public function show($id)
    {
        $data = $this->containerWithCapacityService->show($id);
        if ($data) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}