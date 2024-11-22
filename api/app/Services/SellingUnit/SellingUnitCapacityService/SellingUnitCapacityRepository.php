<?php

namespace App\Services\SellingUnit\SellingUnitCapacityService;

use App\Models\SellingUnitCapacity;
use App\Services\Security\LogService\LogRepository;
use Exception;

class SellingUnitCapacityRepository
{
    protected $logRepository;
    protected $username;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
        $this->username = $this->logRepository->getUsername();
    }

    public function index()
    {
        $this->logRepository->logEvent(
            'selling_unit_capacities',
            'view',
            null,
            'SellingUnitCapacity',
            "$this->username viewed all selling unit capacities"
        );

        $model = SellingUnitCapacity::paginate(20);
        if ($model) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function show($id)
    {
        $this->logRepository->logEvent(
            'selling_unit_capacities',
            'view',
            $id,
            'SellingUnitCapacity',
            "$this->username viewed selling unit capacity with ID $id"
        );

        $model = SellingUnitCapacity::where('id', $id)->first();
        if ($model) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function store($data)
    {
        try {
            $model = SellingUnitCapacity::create($data);

            $this->logRepository->logEvent(
                'selling_unit_capacities',
                'create',
                $model->id,
                'SellingUnitCapacity',
                "$this->username created a new selling unit capacity: {$model->selling_unit_capacity}",
                $data
            );

            return response()->json(['success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Insertion error'], 500);
        }
    }

    public function update($data, $id)
    {
        $model = SellingUnitCapacity::where('id', $id)->first();
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        try {
            $model->update($data);

            $this->logRepository->logEvent(
                'selling_unit_capacities',
                'update',
                $id,
                'SellingUnitCapacity',
                "$this->username updated selling unit capacity with ID $id",
                $data
            );

            return response()->json(['success' => true, 'message' => 'Update successful', 'data' => $model], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Update error'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the model or throw an exception if not found
            $model = SellingUnitCapacity::findOrFail($id);

            // Attempt to delete the model
            $model->delete();

            // Log the event only after successful deletion
            $this->logRepository->logEvent(
                'selling_unit_capacities',
                'delete',
                $id,
                'SellingUnitCapacity',
                "$this->username deleted selling unit capacity with ID $id"
            );

            return response()->json([
                'success' => true,
                'message' => 'Selling Unit Capacity deleted successfully'
            ], 200);
        } catch (Exception $e) {
            // Handle the exception and return an error response
            return response()->json([
                'success' => false,
                'message' => 'Deletion error'
            ], 500);
        }
    }

}
