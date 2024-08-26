<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class PurchaseUnit extends Model
{
    use HasUuids;
    use HasFactory;
    protected $fillable = [
        'id',
        'purchase_unit_name',
    ];

    public function sellingUnits()
    {
        return $this->hasMany(SellingUnit::class, 'purchase_unit_id', 'id');
    }
}
