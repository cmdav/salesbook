<?php

namespace App\Http\Controllers\Users;

use App\Services\UserService\UserService;
use App\Http\Controllers\Users\UserFormRequest;
use Illuminate\Http\Request;

class TestedControllerController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return $this->userService->index();
    }

    public function show($id)
    {
        return $this->userService->show($id);
    }

    public function update(UserFormRequest $request, $id)
    {
        return $this->userService->update($request->validated(), $id);
    }

    public function destroy($id)
    {
        return $this->userService->destroy($id);
    }
}