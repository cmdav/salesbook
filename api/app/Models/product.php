<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Carbon\Carbon;

class Product extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    
    protected $fillable = [
        'product_name',
        'product_description',
        'product_image',
        'vat',
        'measurement_id',
        'sub_category_id',
        'created_by',
        'updated_by',
        'category_id'
    ];

    protected static function booted()
    {
        static::updated(function ($product) {
            
            $now = Carbon::now();
            $product->productType()->update([
                'product_type_name' => $product->product_name,
                'product_type_image' => $product->product_image,
                'product_type_description' => $product->product_description,
                'updated_by' => $product->updated_by,
                'updated_at' => $now,
               
            ]);
        });

        static::created(function ($product) {
            $now = Carbon::now();
            $product->productType()->create([
    
                'product_type_name' => $product->product_name,
                'product_type_image' => $product->product_image,
                'product_type_description' => $product->product_description,
                'created_by' => $product->created_by,
                'updated_by' => $product->updated_by,
                'created_at' => $now, 
                'updated_at' => $now,
                'type' => 1
            ]);
        });
    }
    public function getVatAttribute($value)
    {
        switch ($value) {
            case 0:
                return 'No';
            case 1:
                return 'Yes';
            default:
                return 'No'; 
        }
    }


    public function measurement(){
       
        return $this->belongsTo(Measurement::class, 'measurement_id','id');
    }
    
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
    
}
