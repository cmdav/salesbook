<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class SupplierRequest extends Model
{
    use  SetCreatedBy,  HasUuids, HasFactory;
    protected $fillable = [
    
        'organization_id',
        'supplier_product_id',
        'batch_no',
        'quantity',
        'comment',
        'created_by',
        'updated_by'
    ];

}
