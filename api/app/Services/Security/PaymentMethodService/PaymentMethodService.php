<?php

namespace App\Services\Security\PaymentMethodService;

use App\Services\Security\PaymentMethodService\PaymentMethodRepository;

class PaymentMethodService
{
    protected $paymentMethodRepository;

    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function index()
    {
        return $this->paymentMethodRepository->index();
    }

    public function show($id)
    {
        return $this->paymentMethodRepository->show($id);
    }

    public function store($data)
    {
        return $this->paymentMethodRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->paymentMethodRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->paymentMethodRepository->destroy($id);
    }
}
