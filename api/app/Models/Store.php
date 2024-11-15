<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Store extends Model
{
    use  SetCreatedBy;
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'product_type_id',
       // 'store_owner',
        'batch_no',
        'capacity_qty_available',
        //'container_qty_available',
        //'store_type',
        'product_measurement_id',
        'selling_unit_id',
        'purchase_unit_id',
        'status',
        'created_by',
        'updated_by',
        'branch_id',
        'is_actual'
    ];


    public function supplier_product()
    {

        return $this->belongsTo(SupplierProduct::class, 'supplier_product_id', 'id');
    }
    public function price()
    {

        return $this->belongsTo(Price::class);
    }
    public function batch_price()
    {

        return $this->belongsTo(Price::class, 'batch_no', 'batch_no');
    }
    public function productType()
    {

        return $this->belongsTo(ProductType::class);
    }
    public function branches()
    {

        return $this->belongsTo(BusinessBranch::class, 'branch_id', 'id');
    }
    public function productMeasurement()
    {
        return $this->belongsTo(ProductMeasurement::class, 'product_measurement_id', 'id');
    }

}
