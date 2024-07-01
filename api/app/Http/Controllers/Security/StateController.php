<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use App\Services\Security\StateService\StateService;
use App\Http\Requests\Security\StateFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StateController extends Controller
{
    private $stateService;

    public function __construct(StateService $stateService)
    {
        $this->stateService = $stateService;
    }

    public function index()
    {
        return $this->stateService->index();
    }

    public function show($id)
    {
        return $this->stateService->show($id);
    }

    public function store(StateFormRequest $request)
    {
        return $this->stateService->store($request->all());
    }

    public function update(StateFormRequest $request, $id)
    {
        return $this->stateService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->stateService->destroy($id);
    }
}