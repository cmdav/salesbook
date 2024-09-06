<?php

namespace App\Services\SellingUnit\PurchaseUnitService;

use App\Models\PurchaseUnit;

use Exception;

class PurchaseUnitRepository
{
    // public function index()
    // {
    //     $model =  PurchaseUnit::paginate(20);
    //     if($model) {
    //         return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
    //     }
    //     return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    // }

    public function show($id)
    {
        $model = PurchaseUnit::where('id', $id)->first();
        if($model) {
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function store($data)
    {
        try {
            $model =  PurchaseUnit::create($data);
            return response()->json([ 'success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }

    }

    public function update($data, $id)
    {
        try {
            $model = PurchaseUnit::where('id', $id)->first();
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
            // Find the model or throw a 404 error if not found
            $model = PurchaseUnit::findOrFail($id);

            // Attempt to delete the model
            $model->delete();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Purchase Unit deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            // Log the error
            // Log::channel('deletion_errors')->error('Error deleting Purchase Unit: ' . $e->getMessage());

            // Return error response if deletion fails, particularly when it's in use
            return response()->json([
                'success' => false,
                'message' => 'This Purchase Unit is already in use and cannot be deleted',
            ], 500);
        }
    }

    public function listPurchaseUnit()
    {
        $purchaseUnits = PurchaseUnit::select("id", "purchase_unit_name")
            ->with([
                'sellingUnits:id,purchase_unit_id,selling_unit_name',
                'sellingUnits.sellingUnitCapacities:id,selling_unit_id,selling_unit_capacity'
            ])->get();

        $data = $purchaseUnits->map(function ($purchaseUnit) {
            return [
                'id' => $purchaseUnit->id,
                'purchase_unit_name' => $purchaseUnit->purchase_unit_name,
                'selling_units' => $purchaseUnit->sellingUnits->map(function ($sellingUnit) {
                    return [
                        'id' => $sellingUnit->id,
                        //'purchase_unit_id' => $sellingUnit->purchase_unit_id,
                        'selling_unit_name' => $sellingUnit->selling_unit_name,
                        'selling_unit_capacities' => $sellingUnit->sellingUnitCapacities->map(function ($capacity) {
                            return [
                                'id' => $capacity->id,
                               // 'selling_unit_id' => $capacity->selling_unit_id,
                                'selling_unit_capacity' => $capacity->selling_unit_capacity,
                            ];
                        }),
                    ];
                }),
            ];
        });

        return $data;
    }

    public function index()
    {
        // Fetch the paginated data using the reusable query method
        $purchaseUnits = $this->getPurchaseUnitsQuery()->paginate(5);

        // Transform the paginated data
        $purchaseUnits->getCollection()->transform(function ($purchaseUnit) {
            return $this->transformPurchaseUnit($purchaseUnit);
        });

        return $purchaseUnits;
    }

    public function getSearchPurchaseUnit($search)
    {
        // Fetch the filtered paginated data using the reusable query method
        $purchaseUnits = $this->getPurchaseUnitsQuery()
            ->where('purchase_unit_name', 'LIKE', '%' . $search . '%')
            ->orWhereHas('sellingUnits', function ($query) use ($search) {
                $query->where('selling_unit_name', 'LIKE', '%' . $search . '%');
            })
            ->paginate(20);

        // Transform the paginated data
        $purchaseUnits->getCollection()->transform(function ($purchaseUnit) {
            return $this->transformPurchaseUnit($purchaseUnit);
        });

        return $purchaseUnits;
    }

    private function getPurchaseUnitsQuery()
    {
        return PurchaseUnit::select("id", "purchase_unit_name")
            ->with([
                'sellingUnits:id,purchase_unit_id,selling_unit_name',
                'sellingUnits.sellingUnitCapacities:id,selling_unit_id,selling_unit_capacity'
            ]);
    }

    protected function transformPurchaseUnit($purchaseUnit)
    {
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
    }
}
