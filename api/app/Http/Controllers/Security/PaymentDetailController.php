<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use App\Services\Security\PaymentDetailService\PaymentDetailService;
use App\Http\Requests\Security\PaymentDetailFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentDetailController extends Controller
{
    private $paymentDetailService;

    public function __construct(PaymentDetailService $paymentDetailService)
    {
        $this->paymentDetailService = $paymentDetailService;
    }

    public function index()
    {
        return $this->paymentDetailService->index();
    }

    public function show($id)
    {
        return $this->paymentDetailService->show($id);
    }

    public function store(PaymentDetailFormRequest $request)
    {
        return $this->paymentDetailService->store($request->all());
    }

    public function update(PaymentDetailFormRequest $request, $id)
    {
        return $this->paymentDetailService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->paymentDetailService->destroy($id);
    }
}