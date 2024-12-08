<?php

namespace App\Http\Controllers\SellingUnit;

use App\Http\Controllers\Controller;
use App\Services\SellingUnit\PurchaseUnitService\PurchaseUnitService;
use App\Http\Requests\SellingUnit\PurchaseUnitFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PurchaseUnitController extends Controller
{
    private $purchaseUnitService;

    public function __construct(PurchaseUnitService $purchaseUnitService)
    {
        $this->purchaseUnitService = $purchaseUnitService;
    }

    public function index()
    {
        return $this->purchaseUnitService->index();
    }

    public function show($id)
    {
        return $this->purchaseUnitService->show($id);
    }


    public function store(PurchaseUnitFormRequest $request)
    {
        return $this->purchaseUnitService->store($request->all());
    }

    public function update(Request $request, $id)
    {
        $rule = [
            'purchase_unit_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('purchase_units')
                    ->where('parent_purchase_unit_id', $request->input('parent_purchase_unit_id')) // Access input from the request
                    ->ignore($id), // Ignore the current record by its ID
            ],
            'measurement_group_id' => [
                'required',
                'uuid',
            ],
            'parent_purchase_unit_id' => [
                'nullable', // The field can be missing or null
                'uuid',
                'exists:purchase_units,id', // Ensure the provided parent ID exists in the purchase_units table
            ],
            'unit' => 'required|integer|min:0', // Ensure the unit is an integer and defaults to 0 if not provided
        ];

        $request->validate($rule);

        // Call the service to update the purchase unit with the validated data
        return $this->purchaseUnitService->update($request->all(), $id);
    }


    public function destroy($id)
    {
        return $this->purchaseUnitService->destroy($id);
    }
}
