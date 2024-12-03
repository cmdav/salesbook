<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class PurchaseUnit extends Model
{
    use  SetCreatedBy;
    use HasUuids;
    use HasFactory;
    protected $fillable = [
        'id',
        'purchase_unit_name',
        'measurement_group_id',
        'parent_purchase_unit_id',
        'unit',
        'created_by',
        'updated_by'
    ];

    // public function sellingUnits()
    // {
    //     return $this->hasMany(SellingUnit::class, 'purchase_unit_id', 'id');
    // }
    public function measurementGroup()
    {
        return $this->belongsTo(MeasurementGroup::class, 'measurement_group_id', 'id');
    }
    // In PurchaseUnit Model
    // In the PurchaseUnit model
    public function subPurchaseUnits()
    {
        return $this->hasMany(PurchaseUnit::class, 'parent_purchase_unit_id', 'id');
    }

    public function parentPurchaseUnit()
    {
        return $this->belongsTo(PurchaseUnit::class, 'parent_purchase_unit_id', 'id');
    }


}
