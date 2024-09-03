<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class SellingUnitCapacity extends Model
{
    use HasFactory;
    protected $fillable = [
        'selling_unit_id',
        'selling_unit_capacity',
        'piece_name',
    ];
    public function sellingUnit()
    {
        return $this->belongsTo(SellingUnit::class, 'selling_unit_id', 'id');
    }
}
