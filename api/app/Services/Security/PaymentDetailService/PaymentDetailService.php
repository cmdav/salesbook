<?php

namespace App\Services\Security\PaymentDetailService;

use App\Services\Security\PaymentDetailService\PaymentDetailRepository;

class PaymentDetailService
{
    protected $paymentDetailRepository;

    public function __construct(PaymentDetailRepository $paymentDetailRepository)
    {
        $this->paymentDetailRepository = $paymentDetailRepository;
    }

    public function index()
    {
        return $this->paymentDetailRepository->index();
    }

    public function show($id)
    {
        return $this->paymentDetailRepository->show($id);
    }

    public function store($data)
    {
        return $this->paymentDetailRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->paymentDetailRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->paymentDetailRepository->destroy($id);
    }
}
