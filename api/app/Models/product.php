<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Product extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    
    protected $fillable = [
        'product_name',
        'product_description',
        'product_image',
        'measurement_id',
        'sub_category_id',
        'created_by',
        'updated_by',
        'category_id'
    ];

    public function measurement(){
       
        return $this->belongsTo(Measurement::class, 'measurement_id','id');
    }
    // public function getProductImageAttribute($value): string
    // {
        
//    // return url('/') . $value;[]
    // }
    public function subCategory()
    {
        
        return $this->belongsTo(ProductSubCategory::class, 'sub_category_id','id');

    }
    public function productType()
    {
        
        return $this->hasMany(ProductType::class, 'product_id','id');

    }
    public function product_category()
    {
        
        return $this->belongsTo(ProductCategory::class, 'category_id','id');

    }
    public function suppliers(){

        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }
    // public function price(){

    //     return $this->hasMany(Price::class);
    // }
    // public function store(){

    //     return $this->hasOne(Store::class,'product_type_id','id');
    // }
    // public function activePrice() {
    //     return $this->hasOne(Price::class)->where('status', 1);
    // }
    // public function latestPurchase() {
    //     return $this->hasOne(Purchase::class, 'product_type_id','id')->latest('created_at');;
    // }

}
