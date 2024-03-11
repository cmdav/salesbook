<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Illuminate\Support\Facades\DB; 
use Exception;

class Sale extends Model
{
    use SetCreatedBy, HasUuids, HasFactory;

    protected $fillable = [
        'store_id',
        'customer_id',
        'price_sold_at',
        'quantity',
        'sales_owner',
        'created_by',
        'updated_by'
    ];

    protected static function boot() {
        parent::boot();

        static::created(function ($sale) {

            DB::transaction(function () use ($sale) {
                // access the store using it relationship
                $store = $sale->store;

                if (!$store) {
                  
                    throw new Exception("The error return for sales model state that item does not exist.");
                }

                // Check if store has enough quantity
                if ($store->quantity_available < $sale->quantity) {
                    throw new Exception("The error return for sales model state not enough item in store.");
                }

                // Subtract the sale quantity from the store's available quantity
                $store->quantity_available -= $sale->quantity;

                // Save the updated store
                $store->save();
            });
           
        });
    }
    public function store(){

        return $this->belongsTo(Store::class,'store_id','id');
    }
    public function organization(){

        return $this->belongsTo(Organization::class,'organization_id','id');
    }
}
