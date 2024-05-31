<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use App\Services\Security\SubscriptionService\SubscriptionService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Security\SubscriptionFormRequest;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function index()
    {
        return $this->subscriptionService->index();
    }

    public function show($id)
    {
        return $this->subscriptionService->show($id);
    }

    public function store(SubscriptionFormRequest $request)
    {
        return $this->subscriptionService->store($request->all());
    }

    public function update(SubscriptionFormRequest $request, $id)
    {
        return $this->subscriptionService->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->subscriptionService->destroy($id);
    }
}