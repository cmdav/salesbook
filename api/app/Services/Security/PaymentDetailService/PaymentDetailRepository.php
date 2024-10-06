<?php

namespace App\Services\Security\PaymentDetailService;

use App\Models\PaymentDetail;

use Exception;

class PaymentDetailRepository
{
    public function index()
    {
        $model =  PaymentDetail::with('payment_methods:id,payment_name')->paginate(20);
        if($model) {
            $transformedData = $model->getCollection()->transform(function ($paymentDetail) {
                return [
                    'id' => $paymentDetail->id,
                    'payment_method_id' => $paymentDetail->payment_method_id,
                    'account_name' => $paymentDetail->account_name,
                    'account_number' => $paymentDetail->account_number,
                    'payment_identifier' => $paymentDetail->payment_identifier,
                    'payment_name' => $paymentDetail->payment_methods->payment_name ?? null, // Add payment_name after payment_identifier
                    'created_at' => $paymentDetail->created_at,
                    'updated_at' => $paymentDetail->updated_at,
                ];
            });
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' =>  $transformedData], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function show($id)
    {
        $model = PaymentDetail::where('id', $id)->first();
        if($model) {
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function store($data)
    {
        try {
            $model =  PaymentDetail::create($data);
            return response()->json([ 'success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }

    }

    public function update($data, $id)
    {
        try {
            $model = PaymentDetail::where('id', $id)->first();
            if($model) {
                $model->update($data);
                return response()->json([ 'success' => true, 'message' => 'Update successful', 'data' => $model], 200);
            }

            return response()->json([ 'success' => false, 'message' => 'Record not found', 'data' => $model], 404);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }
    }

    public function destroy($id)
    {
        $model = PaymentDetail::findOrFail($id);
        $model->delete();
        return $model;
    }
    public function getlistPaymentMethod()
    {
        return PaymentDetail::select("id", "payment_identifier")->get();
    }
}
