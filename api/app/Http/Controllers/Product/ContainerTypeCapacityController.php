<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Product\ContainerTypeCapacityService\ContainerTypeCapacityService;
use App\Http\Requests\Product\ContainerTypeCapacityFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContainerTypeCapacityController extends Controller
{
    private $containerTypeCapacityService;

    public function __construct(ContainerTypeCapacityService $containerTypeCapacityService)
    {
        $this->containerTypeCapacityService = $containerTypeCapacityService;
    }

    public function index()
    {
        return $this->containerTypeCapacityService->index();
    }

    public function show($id)
    {
        return $this->containerTypeCapacityService->show($id);
    }

    public function store(ContainerTypeCapacityFormRequest $request)
    {
        return $this->containerTypeCapacityService->store($request->all());
    }

    public function update(ContainerTypeCapacityFormRequest $request, $id)
    {
        return $this->containerTypeCapacityService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->containerTypeCapacityService->destroy($id);
    }
}