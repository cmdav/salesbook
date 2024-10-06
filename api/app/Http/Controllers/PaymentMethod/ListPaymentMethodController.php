<?php

namespace App\Http\Controllers\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Services\PaymentMethod\ListPaymentMethodService\ListPaymentMethodService;
use App\Http\Requests\PaymentMethod\ListPaymentMethodFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListPaymentMethodController extends Controller
{
    private $listPaymentMethodService;

    public function __construct(ListPaymentMethodService $listPaymentMethodService)
    {
        $this->listPaymentMethodService = $listPaymentMethodService;
    }

    public function index()
    {
        $data = $this->listPaymentMethodService->index();
         if (!$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}