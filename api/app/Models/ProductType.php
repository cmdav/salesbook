<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Carbon\Carbon;
class ProductType extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = [
        'product_type_name',
        'product_id',
        'product_type_image',   
        'product_type_description',
        'organization_id',
        'selling_price',
        'supplier_id',
        'created_by',
        'updated_by',
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-y H:i:s');
    }

    public function suppliers(){

        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }
    public function product(){

        return $this->belongsTo(Product::class);
    }
    public function price(){

        return $this->hasMany(Price::class);
    }
    public function store(){

        return $this->hasOne(Store::class,'product_type_id','id');
    }
    public function activePrice() {
        return $this->hasOne(Price::class)->where('status', 1);
    }
    public function latestPurchase() {
        return $this->hasOne(Purchase::class, 'product_type_id','id')->latest('created_at');;
    }
}   
