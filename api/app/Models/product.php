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
        
    //    // return url('/') . $value;
    // }
    public function subCategory()
    {
        
        return $this->belongsTo(ProductSubCategory::class, 'sub_category_id','id');

    }
    public function productType()
    {
        
        return $this->hasMany(ProductType::class, 'product_id','id');

    }

}
