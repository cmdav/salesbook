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
        'product_type_id',
        'store_owner',
        'batch_no',
        'quantity_available',
        'store_type',
        'status',
        'created_by',
        'updated_by',
    ];
    protected static function boot() {

        parent::boot();
        

        static::created(function ($store) {
            //supply to company
            // if ($store->store_type == 1) {
            //     SupplyToCompany::updateOrCreate(
            //         [
            //             'supplier_id' => auth()->user()->id,
            //             'organization_id' => $store->store_owner,
            //             'supplier_product_id' => $store->product_type_id,
            //         ],
            //         [
            //             'supplier_id' => auth()->user()->id,
            //             'organization_id' => $store->store_owner,
            //             'supplier_product_id' => $store->supplier_product_id,
            //         ]
            //     );
            // }
            
                
        });

        static::updated(function ($store) {
            // If an existing store is updated, add the difference to the inventory
            // $originalQuantity = $store->getOriginal('quantity');
            // $newQuantity = $store->quantity;
            // $quantityToAdd = $newQuantity - $originalQuantity; // Calculate the difference

            // $inventory = Inventory::where('store_id', $store->id)
            //                       ->where('supplier_product_id', $store->supplier_product_id)
            //                       ->first();

            // if ($inventory) {
            //     // If inventory exists, update it
            //     $inventory->quantity_available += $quantityToAdd;
            //     $inventory->last_updated_by = auth()->user()->id;
            //     $inventory->save();
            // } 
        });
    }

    public function supplier_product(){

        return $this->belongsTo(SupplierProduct::class,'supplier_product_id','id');
    }
    public function price(){

        return $this->belongsTo(Price::class);
    }
    public function batch_price(){

        return $this->belongsTo(Price::class, 'batch_no','batch_no');
    }
    public function productType(){

        return $this->belongsTo(ProductType::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')
                    ->select(['id', \DB::raw("CONCAT(first_name, ' ', COALESCE(contact_person, ''), ' ', last_name) as fullname")]);
    }

   
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')
                    ->select(['id', \DB::raw("CONCAT(first_name, ' ', COALESCE(contact_person, ''), ' ', last_name) as fullname")]);
    }
}
