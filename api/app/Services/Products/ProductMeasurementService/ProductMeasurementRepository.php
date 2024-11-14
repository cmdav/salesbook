<?php

namespace App\Services\Products\ProductMeasurementService;

use App\Models\ProductMeasurement;

use Exception;

class ProductMeasurementRepository
{
    public function index()
    {
        $model =  ProductMeasurement::paginate(20);
        if($model) {
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function show($id)
    {
        $model = ProductMeasurement::where('id', $id)->first();
        if($model) {
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function store($data)
    {
        try {
            $model = ProductMeasurement::updateOrCreate(
                [
                    'product_type_id' => $data['product_type_id'],
                    'purchasing_unit_id' => $data['purchasing_unit_id']
                ], // Conditions to check for existence
                $data // Data to update or create
            );


            return response()->json([ 'success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            \Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }

    }

    public function update($data, $id)
    {
        try {
            $model = ProductMeasurement::where('id', $id)->first();
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
        try {
            $model = ProductMeasurement::findOrFail($id);
            $model->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully',
                'data' => $model
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'data' => null
            ], 404);
        }
    }
}
