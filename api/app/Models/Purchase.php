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
        'purchase_by',
        'organization_id',
        'status',
        'created_by',
        'updated_by',
    ];
    protected static function boot() {

        parent::boot();
        

        static::created(function ($purchase) {
           
                $store = Store::where('product_type_id', $purchase->product_type_id)
                ->where('store_owner', $purchase->purchase_owner) 
                ->where('price_id', $purchase->price_id)
                ->first();

                if ($store) {
                    $store->quantity_available += $purchase->quantity;
                    $store->save();
                } else {
                    // Create a new store record
                    Store::create([
                        'product_type_id' => $purchase->product_type_id,
                        'store_owner' => $purchase->purchase_by,
                        'price_id' => $purchase->price_id,
                        'quantity_available' => $purchase->quantity,
                        'store_type' => auth()->user()->type_id,
                        'created_by' => $purchase->created_by, 
                        'status' => 1, 
                    ]);
                }
        });

    
    }
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
