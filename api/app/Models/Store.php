<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Store extends Model
{
    use  SetCreatedBy,  HasUuids, HasFactory;
    
    protected $fillable = [
        'supplier_product_id',
        'currency',
        'discount',
        'batch_no',
        'product_identifier',
        'supplier_price',
        'expired_date',
        'store_owner',
        'quantity',
        'store_type',
        'created_by',
        'updated_by',
        'status'
    ];
    protected static function boot() {

        parent::boot();
        

        static::created(function ($store) {
            //supply to company
            if ($store->store_type == 1) {
                SupplyToCompany::updateOrCreate(
                    [
                        'supplier_id' => auth()->user()->id,
                        'organization_id' => $store->store_owner,
                        'supplier_product_id' => $store->supplier_product_id,
                    ],
                    [
                        'supplier_id' => auth()->user()->id,
                        'organization_id' => $store->store_owner,
                        'supplier_product_id' => $store->supplier_product_id,
                    ]
                );
            }
        });
    }
}
