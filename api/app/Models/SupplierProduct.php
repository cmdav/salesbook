<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class SupplierProduct extends Model
{
    use  SetCreatedBy,  HasUuids, HasFactory;
    protected $fillable = [
        'product_type_id',
        'product_description',
        'batch_no',
        // 'price',
        // 'product_image',
        // 'product_name',
        'created_by',
        'updated_by',
        'supplier_id',
        'branch_id'
    ];

    public function storeItem(){

        return $this->hasMany(Store::class, 'supplier_product_id', 'id');
    }
    public function users(){

        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }
    public function producttypes(){

        return $this->belongsTo(ProductType::class, 'product_type_id', 'id');
    }
    // In ProductType model
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    

}
