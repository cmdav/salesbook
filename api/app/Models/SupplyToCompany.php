<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class SupplyToCompany extends Model
{
    use  SetCreatedBy,  HasUuids, HasFactory;
    protected $fillable = [
       
        'supplier_id',
        'organization_id',
        'supplier_product_type_id',
        'created_by',
        'updated_by'
    ];

    public function supplier_product(){

        return $this->belongsTo(SupplierProduct::class,'supplier_product_id','id');
    }
   
}
