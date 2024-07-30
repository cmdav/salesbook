<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Product\ContainerTypeService\ContainerTypeService;
use App\Http\Requests\Product\ContainerTypeFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContainerTypeController extends Controller
{
    private $containerTypeService;

    public function __construct(ContainerTypeService $containerTypeService)
    {
        $this->containerTypeService = $containerTypeService;
    }

    public function index()
    {
        
        return $this->containerTypeService->index();
    }

    public function show($id)
    {
        return $this->containerTypeService->show($id);
    }

    public function store(ContainerTypeFormRequest $request)
    {
        return $this->containerTypeService->store($request->all());
    }

    public function update(ContainerTypeFormRequest $request, $id)
    {
        return $this->containerTypeService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->containerTypeService->destroy($id);
    }
}