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

    protected function getMeasurementGroupsQuery()
    {
        return MeasurementGroup::select("id", "group_name")
            ->with([
                'purchaseUnits:id,measurement_group_id,purchase_unit_name,parent_purchase_unit_id,unit',
                'purchaseUnits.subPurchaseUnits:id,purchase_unit_name,parent_purchase_unit_id,unit',
            ]);
    }

    protected function getSubPurchaseUnits($purchaseUnit)
    {
        // Only process if there are sub-purchase units
        if ($purchaseUnit->subPurchaseUnits->isEmpty()) {
            return [];
        }

        // Process sub-purchase units recursively, without selling unit data
        return $purchaseUnit->subPurchaseUnits->map(function ($subPurchaseUnit) use ($purchaseUnit) {
            return [
                'id' => $subPurchaseUnit->id,
                'purchase_unit_name' => $subPurchaseUnit->purchase_unit_name,
                'unit' => $subPurchaseUnit->unit, // Added unit here

                // Recursively load sub-purchase units for this level
                'sub_purchase_units' => $this->getSubPurchaseUnits($subPurchaseUnit),
            ];
        });
    }

    protected function transformMeasurementGroup($measurementGroup)
    {
        // Group purchase units by parent-child relationships
        $purchaseUnitsByParent = $measurementGroup->purchaseUnits->groupBy(function ($purchaseUnit) {
            return $purchaseUnit->parent_purchase_unit_id ?? 'parent';
        });

        // Now build the structure by recursively nesting child units under their parents
        return [
            'id' => $measurementGroup->id,
            'group_name' => $measurementGroup->group_name,
            'purchase_units' => $this->buildPurchaseUnitHierarchy($purchaseUnitsByParent),
        ];
    }

    protected function buildPurchaseUnitHierarchy($purchaseUnitsByParent)
    {
        $parentUnits = $purchaseUnitsByParent->get('parent', collect());

        return $parentUnits->map(function ($parentUnit) use ($purchaseUnitsByParent) {
            // Get children of this parent unit
            $children = $purchaseUnitsByParent->get($parentUnit->id, collect());

            // Recursively build the structure for sub-purchase units and children, excluding selling unit data
            return [
                'id' => $parentUnit->id,
                'purchase_unit_name' => $parentUnit->purchase_unit_name,
                'unit' => $parentUnit->unit, // Include unit field here
                'sub_purchase_units' => $this->getSubPurchaseUnits($parentUnit), // Only sub-purchase units
            ];
        });
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

        // Transform the paginated data to return only the purchase unit and sub-purchase unit information
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
            $query->where('purchase_unit_name', 'LIKE', '%' . $search . '%');

        })
        ->paginate(6);

        // Transform the paginated data
        $measurementGroups->getCollection()->transform(function ($measurementGroup) {
            return $this->transformMeasurementGroup($measurementGroup);
        });

        return $measurementGroups;
    }
}
