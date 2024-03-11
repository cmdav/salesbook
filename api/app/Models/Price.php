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
        'product_type_price',
        'system_price',
        'currency_id',
        'discount',
        'organization_id',
        'created_by',
        'updated_by',
        'status',
        
    ];


    public function productType(){

        return $this->belongsTo(ProductType::class);
    }
    public function currency(){

        return $this->belongsTo(Currency::class);
    }
    public function supplier(){

        return $this->belongsTo(User::class);
    }
}
