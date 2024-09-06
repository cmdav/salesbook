<?php

namespace App\Services\SellingUnit\SellingUnitService;

use App\Models\SellingUnit;

use Exception;

class SellingUnitRepository
{
    public function index()
    {
        $model =  SellingUnit::paginate(20);
        if($model) {
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function show($id)
    {
        $model = SellingUnit::where('id', $id)->first();
        if($model) {
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function store($data)
    {
        try {
            $model =  SellingUnit::create($data);
            return response()->json([ 'success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }

    }

    public function update($data, $id)
    {
        try {
            $model = SellingUnit::where('id', $id)->first();
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
            // Find the SellingUnit or throw a 404 error if not found
            $model = SellingUnit::findOrFail($id);

            // Attempt to delete the SellingUnit
            $model->delete();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Selling Unit deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::channel('deletion_errors')->error('Error deleting Selling Unit: ' . $e->getMessage());

            // Return error response if deletion fails, particularly when it's in use
            return response()->json([
                'success' => false,
                'message' => 'This Selling Unit is already in use and cannot be deleted',
            ], 500);
        }
    }

}
