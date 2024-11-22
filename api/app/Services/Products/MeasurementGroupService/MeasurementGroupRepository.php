<?php

namespace App\Services\Products\MeasurementGroupService;

use App\Models\MeasurementGroup;

use App\Services\Security\LogService\LogRepository;
use Exception;

class MeasurementGroupRepository
{
    protected $logRepository;
    protected $username;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
        $this->username = $this->logRepository->getUsername();
    }
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
        $this->logRepository->logEvent(
            'measurement_groups',
            'view',
            null,
            'MeasurementGroup',
            "{$this->username} viewed all measurement groups"
        );
        $measurementGroups = $this->getMeasurementGroupsQuery()->paginate(6);

        // Transform the paginated data
        $measurementGroups->getCollection()->transform(function ($measurementGroup) {
            return $this->transformMeasurementGroup($measurementGroup);
        });

        return $measurementGroups;
    }


    public function show($id)
    {
        $this->logRepository->logEvent(
            'measurement_groups',
            'view',
            $id,
            'MeasurementGroup',
            "{$this->username} viewed measurement group with ID {$id}"
        );
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
            $this->logRepository->logEvent(
                'measurement_groups',
                'create',
                $model->id,
                'MeasurementGroup',
                "{$this->username} created a new measurement group: {$model->group_name}",
                $data
            );
            return response()->json([ 'success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }

    }

    public function update($data, $id)
    {
        $model = MeasurementGroup::find($id);
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        try {
            $model->update($data);

            $this->logRepository->logEvent(
                'measurement_groups',
                'update',
                $id,
                'MeasurementGroup',
                "{$this->username} updated measurement group with ID {$id}",
                $data
            );

            return response()->json(['success' => true, 'message' => 'Update successful', 'data' => $model], 200);
        } catch (Exception $e) {
            Log::error('Error updating Measurement Group: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Update error'], 500);
        }
    }

    public function destroy($id)
    {
        $model = MeasurementGroup::findOrFail($id);
        $model->delete();
        return $model;
    }

    public function getsearchMeasurementGroup($search)
    {
        // Fetch the filtered paginated data using the reusable query method

        $measurementGroups = $this->getMeasurementGroupsQuery()
        ->where('group_name', 'LIKE', '%' . $search . '%')
        ->orWhereHas('purchaseUnits', function ($query) use ($search) {
            $query->where('purchase_unit_name', 'LIKE', '%' . $search . '%')
                ->orWhereHas('sellingUnits', function ($query) use ($search) {
                    $query->where('selling_unit_name', 'LIKE', '%' . $search . '%');
                });
        })
        ->paginate(6);

        // Transform the paginated data
        $measurementGroups->getCollection()->transform(function ($measurementGroup) {
            return $this->transformMeasurementGroup($measurementGroup);
        });

        return $measurementGroups;
    }
}
