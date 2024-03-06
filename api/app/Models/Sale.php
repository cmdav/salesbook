<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Sale extends Model
{
    use SetCreatedBy, HasUuids, HasFactory;

    protected $fillable = [
        'store_id',
        'organization_id',
        'customer_id',
        'price',
        'quantity',
        'sales_owner',
        'created_by',
    ];

    protected static function boot() {
        parent::boot();

        static::created(function ($sale) {
            // When a sale is made, subtract the sold quantity from the inventory
            $inventory = Inventory::where('store_id', $sale->store_id)->first();

            if ($inventory) {
                // Ensure we don't end up with negative inventory
                $newQuantityAvailable = max($inventory->quantity_available - $sale->quantity, 0);
                $inventory->quantity_available = $newQuantityAvailable;
                $inventory->last_updated_by = auth()->user()->id; // Assuming you have user authentication
                $inventory->save();
            } else {
                // Log error or handle case where no inventory exists for this store_id
                // This would be an unusual situation that you need to decide how to handle
            }
        });
    }
}
