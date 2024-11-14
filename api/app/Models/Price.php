<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Price extends Model
{
    use  SetCreatedBy;
    use HasUuids;
    use HasFactory;
    protected $fillable = [
        'product_type_id',
        'supplier_id',
        'cost_price',
        'selling_price',
        'batch_no',
        'branch_id',
        'new_cost_price',
        'new_selling_price',
        'product_measurement_id',
        'purchase_unit_id',
         'is_new',
        //'auto_generated_selling_price',
        'currency_id',
        'discount',
        'organization_id',
        'created_by',
        'updated_by',
        'status',

    ];


    public function getStatusAttribute($value)
    {
        return $value == 1 ? 'Active price' : 'Inactive price';
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function currency()
    {

        return $this->belongsTo(Currency::class);
    }
    public function supplier()
    {

        return $this->belongsTo(User::class);
    }
    public function referencePrice()
    {
        return $this->belongsTo(Price::class, 'price_id');
    }
    public function productMeasurement()
    {
        return $this->belongsTo(ProductMeasurement::class, 'product_measurement_id', 'id');
    }

}
