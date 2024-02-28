<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Store extends Model
{
    use  SetCreatedBy,  HasUuids, HasFactory;
    
    protected $fillable = [
        'supplier_product_id',
        'currency',
        'discount',
        'batch_no',
        'product_identifier',
        'supplier_price',
        'expired_date',
        'store_owner',
        'created_by',
        'updated_by'
    ];
}
