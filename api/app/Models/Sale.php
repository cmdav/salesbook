<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Illuminate\Support\Facades\DB; 
use Exception;

class Sale extends Model
{
    use SetCreatedBy, HasUuids, HasFactory;

    protected $fillable = [
        'product_type_id',
        'customer_id',
        'price_sold_at',
        'quantity',
        'sales_owner',
        'created_by',
        'updated_by'
    ];

    
    // public function store(){

    //     return $this->belongsTo(Store::class,'product_type_id','product_type_id');
    // }
    public function product(){

        return $this->belongsTo(ProductType::class, 'product_type_id','id');
    }
    public function customers(){

        return $this->belongsTo(User::class,'customer_id','id');
    }
    public function organization(){

        return $this->belongsTo(Organization::class,'organization_id','id');
    }
}
