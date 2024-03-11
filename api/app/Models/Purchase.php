<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Purchase extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;

    protected $fillable = [
        'product_type_id',
        'price_id',
        'currency_id',
        'supplier_id',
        'selling_price',
        'discount',
        'batch_no',
        'quantity',
        'product_identifier',
        'expired_date',
        'purchase_owner',
        'status',
        'created_by',
        'updated_by',
    ];
    public function suppliers(){

        return $this->belongsTo(User::class);
    }
    public function currency(){

        return $this->belongsTo(Currency::class);
    }
    public function productType(){

        return $this->belongsTo(ProductType::class);
    }
    public function price(){

        return $this->belongsTo(price::class);
    }
}
