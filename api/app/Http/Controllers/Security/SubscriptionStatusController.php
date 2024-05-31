<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use App\Services\Security\SubscriptionStatusService\SubscriptionStatusService;
use App\Http\Requests\Security\SubscriptionStatusFormRequest;
use Illuminate\Http\Request;

class SubscriptionStatusController extends Controller
{
    private $subscriptionStatusService;

    public function __construct(SubscriptionStatusService $subscriptionStatusService)
    {
        $this->subscriptionStatusService = $subscriptionStatusService;
    }

    public function index()
    {
        return $this->subscriptionStatusService->index();
    }

    public function show($id)
    {
        return $this->subscriptionStatusService->show($id);
    }

    public function store(SubscriptionStatusFormRequest $request)
    {
        return $this->subscriptionStatusService->store($request->all());
    }

    public function update(SubscriptionStatusFormRequest $request, $id)
    {
        return $this->subscriptionStatusService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->subscriptionStatusService->destroy($id);
    }
}