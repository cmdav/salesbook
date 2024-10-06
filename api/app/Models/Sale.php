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
    use SetCreatedBy;
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'product_type_id',
        'customer_id',
        'price_id',
        'price_sold_at',
        //'capacity_qty',
        //'container_qty',
        'quantity',
        'batch_no',
        'vat',
        'payment_method',
        'transaction_id',
        'sales_owner',
        'created_by',
        'branch_id',
        'updated_by'
    ];
    public function payment_details()
    {

        return $this->belongsTo(PaymentDetail::class, 'payment_method', 'id');
    }
    public function getVatAttribute($value)
    {
        switch ($value) {
            case 0:
                return 'no';
            case 1:
                return 'yes';
            default:
                return 'No';
        }
    }

    public function setVatAttribute($value)
    {
        $this->attributes['vat'] = $value == 'yes' ? 1 : 0;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-y H:i:s');
    }

    public function product()
    {

        return $this->belongsTo(ProductType::class, 'product_type_id', 'id');
    }
    public function customers()
    {

        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function organization()
    {

        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
    public function price()
    {
        return $this->belongsTo(Price::class, 'price_id', 'id');
    }
    public function branches()
    {

        return $this->belongsTo(BusinessBranch::class, 'branch_id', 'id');
    }

}
