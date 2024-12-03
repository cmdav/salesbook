<?php

namespace App\Services\SellingUnit\PurchaseUnitService;

use App\Models\PurchaseUnit;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Services\Security\LogService\LogRepository;

class PurchaseUnitRepository
{
    protected $logRepository;
    protected $username;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
        $username = $this->logRepository->getUsername();
    }

    public function index()
    {
        $this->logRepository->logEvent(
            'purchase_units',
            'view',
            null,
            'PurchaseUnit',
            "$this->username viewed all purchase units"
        );

        // Fetch the paginated data using the reusable query method
        $purchaseUnits = $this->getPurchaseUnitsQuery()->paginate(6);

        // Transform the paginated data
        $purchaseUnits->getCollection()->transform(function ($purchaseUnit) {
            return $this->transformPurchaseUnit($purchaseUnit);
        });

        return $purchaseUnits;
    }

    public function show($id)
    {
        $this->logRepository->logEvent(
            'purchase_units',
            'view',
            $id,
            'PurchaseUnit',
            "$this->username viewed purchase unit with ID $id"
        );

        $model = PurchaseUnit::where('id', $id)->first();
        if ($model) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function store($data)
    {
        try {
            $model = PurchaseUnit::create($data);

            $this->logRepository->logEvent(
                'purchase_units',
                'create',
                $model->id,
                'PurchaseUnit',
                "$this->username created a new purchase unit: {$model->purchase_unit_name}",
                $data
            );

            return response()->json(['success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            Log::error('Error creating Purchase Unit: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Insertion error'], 500);
        }
    }

    public function update($data, $id)
    {
        $model = PurchaseUnit::where('id', $id)->first();
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        try {
            $model->update($data);

            $this->logRepository->logEvent(
                'purchase_units',
                'update',
                $id,
                'PurchaseUnit',
                "$this->username updated purchase unit with ID $id",
                $data
            );

            return response()->json(['success' => true, 'message' => 'Update successful', 'data' => $model], 200);
        } catch (Exception $e) {
            Log::error('Error updating Purchase Unit: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Update error'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $model = PurchaseUnit::findOrFail($id);

            $this->logRepository->logEvent(
                'purchase_units',
                'delete',
                $id,
                'PurchaseUnit',
                "$this->username deleted purchase unit with ID $id"
            );

            $model->delete();

            return response()->json(['success' => true, 'message' => 'Purchase Unit deleted successfully'], 200);
        } catch (Exception $e) {
            Log::error('Error deleting Purchase Unit: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Deletion error'], 500);
        }
    }
    public function listPurchaseUnit()
    {
        $purchaseUnits = PurchaseUnit::select("id", "purchase_unit_name")->get();

        $data = $purchaseUnits->map(function ($purchaseUnit) {
            return [
                'id' => $purchaseUnit->id,
                'purchase_unit_name' => $purchaseUnit->purchase_unit_name,

            ];
        });

        return $data;
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
