<?php

namespace App\Services\Security\SubscriptionService;

use App\Services\Security\SubscriptionService\SubscriptionRepository;

class SubscriptionService
{
    protected $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function index()
    {
        return $this->subscriptionRepository->index();
    }

    public function show($id)
    {
        return $this->subscriptionRepository->show($id);
    }

    public function store($data)
    {
        return $this->subscriptionRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->subscriptionRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->subscriptionRepository->destroy($id);
    }
}
