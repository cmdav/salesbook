<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use App\Services\Security\PaymentMethodService\PaymentMethodService;
use App\Http\Requests\Security\PaymentMethodFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    private $paymentMethodService;

    public function __construct(PaymentMethodService $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;
    }

    public function index()
    {
        return $this->paymentMethodService->index();
    }

    public function show($id)
    {
        return $this->paymentMethodService->show($id);
    }

    public function store(PaymentMethodFormRequest $request)
    {
        return $this->paymentMethodService->store($request->all());
    }

    public function update(PaymentMethodFormRequest $request, $id)
    {
        return $this->paymentMethodService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->paymentMethodService->destroy($id);
    }
}