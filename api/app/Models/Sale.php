<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Illuminate\Support\Facades\DB; 
use Exception;
use Carbon\Carbon;
class Sale extends Model
{
    use SetCreatedBy, HasUuids, HasFactory;

    protected $fillable = [
        'product_type_id',
        'customer_id',
        'price_id',
        'price_sold_at',
        'quantity',
        'batch_no',
        'vat',
        'payment_method',
        'transaction_id',
        'sales_owner',
        'created_by',
        'updated_by'
    ];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-y H:i:s');
    }
    
    // public function store(){

    //     return $this->belongsTo(Store::class,'product_type_id','product_type_id');
    // }
    public function product(){

        return $this->belongsTo(ProductType::class, 'product_type_id','id');
    }
    public function customers(){

        return $this->belongsTo(Customer::class,'customer_id','id');
    }
    public function organization(){

        return $this->belongsTo(Organization::class,'organization_id','id');
    }
    public function price() {
        return $this->belongsTo(Price::class,'price_id','id');
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
