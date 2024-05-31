<?php

namespace App\Services\Security\SubscriptionStatusService;

use App\Services\Security\SubscriptionStatusService\SubscriptionStatusRepository;

class SubscriptionStatusService
{
    protected $subscriptionStatusRepository;

    public function __construct(SubscriptionStatusRepository $subscriptionStatusRepository)
    {
        $this->subscriptionStatusRepository = $subscriptionStatusRepository;
    }

    public function index()
    {
        return $this->subscriptionStatusRepository->index();
    }

    public function show($id)
    {
        return $this->subscriptionStatusRepository->show($id);
    }

    public function store($data)
    {
        return $this->subscriptionStatusRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->subscriptionStatusRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->subscriptionStatusRepository->destroy($id);
    }
}
