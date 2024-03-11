<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class ProductType extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = [
        'product_type',
        'product_id',
        'product_type_image',   
        'product_type_description',
        'organization_id',
        'selling_price',
        'supplier_id',
        'created_by',
        'updated_by',
    ];

    public function suppliers(){

        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }
    public function product(){

        return $this->belongsTo(Product::class);
    }
}
