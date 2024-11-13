<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class ProductMeasurement extends Model
{
    use HasFactory;
    use  SetCreatedBy;
    use HasUuids;
    protected $fillable = [
        'product_type_id',
        'selling_unit_capacity_id',
        'purchasing_unit_id',
        'selling_unit_id'

    ];
    public function sellingUnitCapacity()
    {

        return $this->belongsTo(SellingUnitCapacity::class, 'selling_unit_capacity_id', 'id');
    }

}
