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
        'batch_no',
        'quantity',
        'product_identifier',
        'expiry_date',
        'container_type_capacity_id',
        'status',
        'created_by',
        'branch_id',
        'updated_by',
    ];

   
    protected static function boot() {

        parent::boot();
        

        static::created(function ($purchase) {
           
                $store = Store::where([['product_type_id', $purchase->product_type_id],
                    ['batch_no', $purchase->batch_no]])
                    
                ->where('branch_id', auth()->check() ? auth()->user()->branch_id : 0) 
               // ->where('price_id', $purchase->price_id)
                ->first();

                if ($store) {
                    $store->quantity_available += $purchase->quantity;
                    $store->save();
                } else {
                    // Create a new store record
                    Store::create([
                        'product_type_id' => $purchase->product_type_id,
                        //'store_owner' => $purchase->purchase_by,
                        'store_owner'=> auth()->check() ? auth()->user()->branch_id : 0,
                        'quantity_available' => $purchase->quantity,
                        'batch_no' => $purchase->batch_no,
                        'store_type' => auth()->check() ?auth()->user()->type_id:2,
                        'created_by' => $purchase->created_by, 
                        'status' => 1, 
                    ]);
                }
        });

    
    }
    public function suppliers(){

        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }
    public function currency(){

        return $this->belongsTo(Currency::class);
    }
    public function branches(){

        return $this->belongsTo(BusinessBranch::class,'branch_id', 'id');
    }
    public function productType(){

        return $this->belongsTo(ProductType::class);
    }
    public function price(){

        return $this->belongsTo(Price::class);
    }
    public function containerTypeCapacities(){

        return $this->belongsTo(ContainerTypeCapacity::class,'container_type_capacity_id','id');
    }

}
