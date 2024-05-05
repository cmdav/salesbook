<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Carbon\Carbon;
class ProductType extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = [
        'product_type_name',
        'product_id',
        'product_type_image',   
        'product_type_description',
        'vat',
        'organization_id',
        'selling_price',
        'supplier_id',
        'created_by',
        'updated_by',
        'type'
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-y H:i:s');
    }
   
    public function suppliers(){

        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }
    public function product(){

        return $this->belongsTo(Product::class);
    }
    public function batches(){

        return $this->hasMany(Store::class, 'product_type_id', 'id')->select('id','product_type_id','batch_no','quantity_available');
    }
    public function price(){

        return $this->hasMany(Price::class);
    }
    public function pricenotification(){

        return $this->hasOne(PriceNotification::class);
    }
    public function store(){

        return $this->hasOne(Store::class,'product_type_id','id');
    }
    public function activePrice() {
        return $this->hasOne(Price::class)->where('status', 1);
    }
    public function latestPurchase() {
        return $this->hasOne(Purchase::class, 'product_type_id','id')->latest('created_at');;
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
