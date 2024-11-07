<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Services\Security\LogService\LogService;
use App\Http\Requests\Security\LogFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    private $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        return $this->logService->index($request);
    }

    public function show($id)
    {
        return $this->logService->show($id);
    }

    public function store(LogFormRequest $request)
    {
        return $this->logService->store($request->all());
    }

    public function update(LogFormRequest $request, $id)
    {
        return $this->logService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->logService->destroy($id);
    }
}
