<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class ProductCategory extends Model
{
    use  SetCreatedBy, HasUuids,HasFactory;
    
    protected $fillable = [

        'category_name',
        'created_by',
        'updated_by',
        'category_description'
    ];
    public function product_sub_category()
    {
        
        return $this->hasMany(ProductSubCategory::class, 'category_id','id');

    }
}
