<?php

namespace App\Services\Products\MeasurementGroupService;

use App\Models\MeasurementGroup;

use Exception;

class MeasurementGroupRepository
{
    private function getMeasurementGroupsQuery()
    {
        return MeasurementGroup::select("id", "group_name")
            ->with([
                'purchaseUnits:id,measurement_group_id,purchase_unit_name',
                'purchaseUnits.sellingUnits:id,purchase_unit_id,selling_unit_name',
                'purchaseUnits.sellingUnits.sellingUnitCapacities:id,selling_unit_id,selling_unit_capacity'
            ]);
    }
    protected function transformMeasurementGroup($measurementGroup)
    {
        return [
            'id' => $measurementGroup->id,
            'group_name' => $measurementGroup->group_name,
            'purchase_units' => $measurementGroup->purchaseUnits->map(function ($purchaseUnit) {
                return [
                    'id' => $purchaseUnit->id,
                    'purchase_unit_name' => $purchaseUnit->purchase_unit_name,
                    'selling_units' => $purchaseUnit->sellingUnits->map(function ($sellingUnit) {
                        return [
                            'id' => $sellingUnit->id,
                            'selling_unit_name' => $sellingUnit->selling_unit_name,
                            'selling_unit_capacities' => $sellingUnit->sellingUnitCapacities->map(function ($capacity) {
                                return [
                                    'id' => $capacity->id,
                                    'selling_unit_capacity' => $capacity->selling_unit_capacity,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ];
    }

    public function index()
    {
        // Fetch the paginated data using the reusable query method
        $measurementGroups = $this->getMeasurementGroupsQuery()->paginate(6);

        // Transform the paginated data
        $measurementGroups->getCollection()->transform(function ($measurementGroup) {
            return $this->transformMeasurementGroup($measurementGroup);
        });

        return $measurementGroups;
    }
    // public function index()
    // {
    //     $model =  MeasurementGroup::paginate(20);
    //     if($model) {
    //         return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
    //     }
    //     return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    // }

    public function show($id)
    {
        $model = MeasurementGroup::where('id', $id)->first();
        if($model) {
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function store($data)
    {
        try {
            $model =  MeasurementGroup::create($data);
            return response()->json([ 'success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }

    }

    public function update($data, $id)
    {
        try {
            $model = MeasurementGroup::where('id', $id)->first();
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
        $model = MeasurementGroup::findOrFail($id);
        $model->delete();
        return $model;
    }
}
