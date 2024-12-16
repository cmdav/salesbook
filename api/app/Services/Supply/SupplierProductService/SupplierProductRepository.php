<?php

namespace App\Services\Supply\SupplierProductService;

use App\Models\SupplierProduct;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SupplierProductRepository
{
    public function index($request)
    {

        return SupplierProduct::latest()->paginate(20);

    }
    public function getAuthSupplierProduct()
    {
        $productTypes = SupplierProduct::select('id', 'product_type_id', 'supplier_id')
            ->with([
                'producttypes:id,product_type_name,product_type_image,sub_category_id,category_id',
                'producttypes.subCategory:id,sub_category_name',
                'producttypes.product_category:id,category_name',
                'producttypes.productMeasurement.purchaseUnit:id,purchase_unit_name',
                'producttypes.pricenotification' => function ($query) {
                    $query->select('id', 'product_type_id', 'cost_price', 'status')->where('supplier_id', auth()->user()->id);
                },
            ])
            ->where('supplier_id', auth()->user()->id)
            ->latest()
            ->paginate(20);

        // Transform collection
        $productTypes->getCollection()->transform(function ($productType) {
            return $this->transformProductType($productType);
        });

        return $productTypes;
    }

    protected function transformProductType($productType)
    {
        // Access the first measurement and its purchase unit
        $purchaseMeasurement = optional($productType->producttypes->productMeasurement)->first();
        $purchaseUnit = $purchaseMeasurement ? $purchaseMeasurement->purchaseUnit : null;

        return [
            'id' => $productType->id,
            'product_type_id' => $productType->product_type_id,
            'supplier_id' => $productType->supplier_id,
            'product_name' => optional($productType->producttypes)->product_type_name,
            'product_image' => optional($productType->producttypes)->product_type_image,
            'product_category_name' => optional($productType->producttypes->product_category)->category_name,
            'product_sub_category_name' => optional($productType->producttypes->subCategory)->sub_category_name,
            'measurement_name' => optional($purchaseUnit)->purchase_unit_name, // Access through productMeasurement
            'new_purchase_price' => optional(optional($productType->producttypes)->pricenotification)->cost_price,
            'new_purchase_price_status' => optional(optional($productType->producttypes)->pricenotification)->status,
        ];
    }


    public function listAllSupplierProduct()
    {

        return SupplierProduct::select('id', 'product_name', 'product_description')->latest()->get();
    }
    public function productSuppliedToCompany()
    {

        return SupplierProduct::with('storeItem')->get();


    }
    public function create(array $data)
    {

        return SupplierProduct::create($data);
    }

    public function findById($id)
    {
        return SupplierProduct::find($id);
    }

    public function update($id, array $data)
    {
        $supplierProduct = $this->findById($id);

        if ($supplierProduct) {

            $supplierProduct->update($data);
        }
        return $supplierProduct;
    }

    public function delete($id)
    {
        $supplierProduct = $this->findById($id);
        if ($supplierProduct) {
            return $supplierProduct->delete();
        }
        return null;
    }
}
