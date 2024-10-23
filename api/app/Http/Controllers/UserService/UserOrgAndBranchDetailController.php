<?php

namespace App\Http\Controllers\UserService;

use App\Http\Controllers\Controller;
use App\Services\UserService\UserOrgAndBranchDetailService\UserOrgAndBranchDetailService;
use App\Http\Requests\UserService\UserOrgAndBranchDetailFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserOrgAndBranchDetailController extends Controller
{
    private $userOrgAndBranchDetailService;

    public function __construct(UserOrgAndBranchDetailService $userOrgAndBranchDetailService)
    {
        $this->userOrgAndBranchDetailService = $userOrgAndBranchDetailService;
    }

    public function index(Request $request)
    {
        $data = $this->userOrgAndBranchDetailService->index($request);
        return $data;

        if (count($data) > 0) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
