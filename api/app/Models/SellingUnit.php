<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class SellingUnit extends Model
{
    use  SetCreatedBy;
    use HasUuids;
    use HasFactory;
    protected $fillable = [
        'id',
        'purchase_unit_id',
        'selling_unit_name',
          'created_by',
        'updated_by'
    ];

    public function purchaseUnit()
    {
        return $this->belongsTo(PurchaseUnit::class, 'purchase_unit_id', 'id');
    }

    public function sellingUnitCapacities()
    {
        return $this->hasMany(SellingUnitCapacity::class, 'selling_unit_id', 'id');
    }
}
