<?php

namespace App\Services\PaymentMethod\ListPaymentMethodService;

use App\Services\Security\PaymentDetailService\PaymentDetailRepository;

class ListPaymentMethodService
{
    protected $paymentDetailRepository;

    public function __construct(PaymentDetailRepository $paymentDetailRepository)
    {
        $this->paymentDetailRepository = $paymentDetailRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->paymentDetailRepository->getlistPaymentMethod($data, $id);
    }
}
