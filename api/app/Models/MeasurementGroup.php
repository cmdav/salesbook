<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class MeasurementGroup extends Model
{
    use HasFactory;
    use  SetCreatedBy;
    use HasUuids;
    protected $fillable = [
        'group_name',
        'created_by',
        'updated_by',
    ];

    public function purchaseUnits()
    {
        return $this->hasMany(PurchaseUnit::class, 'measurement_group_id', 'id');
    }
}
