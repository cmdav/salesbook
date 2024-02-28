<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;


class ProductSubCategory extends Model
{
    use  SetCreatedBy, HasUuids,HasFactory;
    
    protected $fillable = [

        'sub_category_name',
        'category_id',
        'created_by',
    ];

    public function category(){

        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
}
