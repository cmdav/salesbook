<?php

namespace App\Services\SellingUnit\SellingUnitService;

use App\Models\SellingUnit;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Services\Security\LogService\LogRepository;

class SellingUnitRepository
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
            'selling_units',
            'view',
            null,
            'SellingUnit',
            "$this->username viewed all selling units"
        );

        $model = SellingUnit::paginate(20);
        if ($model) {
            return response()->json([
                'success' => true,
                'message' => 'Record retrieved successfully',
                'data' => $model
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'No record found',
            'data' => $model
        ], 404);
    }

    public function show($id)
    {
        $this->logRepository->logEvent(
            'selling_units',
            'view',
            $id,
            'SellingUnit',
            "$this->username viewed selling unit with ID $id"
        );

        $model = SellingUnit::where('id', $id)->first();
        if ($model) {
            return response()->json([
                'success' => true,
                'message' => 'Record retrieved successfully',
                'data' => $model
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'No record found',
            'data' => $model
        ], 404);
    }

    public function store($data)
    {
        try {
            $model = SellingUnit::create($data);

            $this->logRepository->logEvent(
                'selling_units',
                'create',
                $model->id,
                'SellingUnit',
                "$this->username created a new selling unit: {$model->selling_unit_name}",
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Insertion successful',
                'data' => $model
            ], 200);
        } catch (Exception $e) {
            Log::error('Error creating Selling Unit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Insertion error'
            ], 500);
        }
    }

    public function update($data, $id)
    {
        $model = SellingUnit::where('id', $id)->first();
        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }

        try {
            $model->update($data);

            $this->logRepository->logEvent(
                'selling_units',
                'update',
                $id,
                'SellingUnit',
                "$this->username updated selling unit with ID $id",
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Update successful',
                'data' => $model
            ], 200);
        } catch (Exception $e) {
            Log::error('Error updating Selling Unit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update error'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $model = SellingUnit::findOrFail($id);

            $this->logRepository->logEvent(
                'selling_units',
                'delete',
                $id,
                'SellingUnit',
                "$this->username deleted selling unit with ID $id"
            );

            $model->delete();

            return response()->json([
                'success' => true,
                'message' => 'Selling Unit deleted successfully'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error deleting Selling Unit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This Selling Unit is already in use and cannot be deleted'
            ], 500);
        }
    }
}
