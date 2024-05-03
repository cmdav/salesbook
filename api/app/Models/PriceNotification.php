<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class PriceNotification extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = [
        'product_type_id',
        'supplier_id',
        'cost_price',
        'selling_price',
        'status',
        'created_by',
        'updated_by',
        
        
    ];
    public function getStatusAttribute($value)
    {
        return $value == 1 ? 'Accepted' : 'Declined';
    }
    public function productTypes(){

        return $this->belongsTo(ProductType::class, 'product_type_id','id');
    }
    public function supplier(){

        return $this->belongsTo(User::class, 'supplier_id','id');
    }
}
