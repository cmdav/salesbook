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
        'product_id',
        'product_description',
        'product_image',
        'product_name',
        'created_by',
        'updated_by',
        'supplier_id'
    ];

    public function storeItem(){

        return $this->hasMany(Store::class, 'supplier_product_id', 'id');
    }
}
