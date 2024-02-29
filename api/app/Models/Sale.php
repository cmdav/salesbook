<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Sale extends Model
{
    use  SetCreatedBy, HasUuids,HasFactory;

    protected $fillable = [
        'store_id',
        'organization_id',
        'customer_id',
        'price',
        'quantity',
        'sales_owner',
        'created_by',
    ];
}
