<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use App\Services\Security\BusinessBranchService\BusinessBranchService;
use App\Http\Requests\Security\BusinessBranchFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BusinessBranchController extends Controller
{
    private $businessBranchService;

    public function __construct(BusinessBranchService $businessBranchService)
    {
        $this->businessBranchService = $businessBranchService;
    }

    public function index()
    {
        $branch=$this->businessBranchService->index();
        return response()->json($branch);
    }

    public function show($id)
    {
        $branch=$this->businessBranchService->show($id);
        return response()->json($branch);
    }

    public function store(BusinessBranchFormRequest $request)
    {
        $product= $this->businessBranchService->store($request->all());
        return response()->json($product);
    }

    public function update(BusinessBranchFormRequest $request, $id)
    {
        $product= $this->businessBranchService->update($request->all(), $id);
        return response()->json($product);
    }

    public function destroy($id)
    {
        return $this->businessBranchService->destroy($id);
       
    }
    public function listing()
    {
        $branch= $this->businessBranchService->listing();
        return response()->json($branch);
    }
}