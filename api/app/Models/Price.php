<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Price extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = [
        'product_type_id',
        'supplier_id',
        'product_price',
        'product_currency',
        'discount',
        'organization_id',
        'created_by',
        'updated_by',
        'status',
        
    ];
}
