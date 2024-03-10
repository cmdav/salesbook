<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SupplierOrganization extends Model
{
    use  HasUuids, HasFactory;

    protected $fillable=[
        'supplier_id',
        'organization_id',
        'created_by',
        'updated_by',
        'status'
        
    ];  
}
