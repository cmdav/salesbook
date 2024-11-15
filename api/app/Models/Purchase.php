<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Purchase extends Model
{
    use  SetCreatedBy;
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'product_type_id',
        'price_id',
        'currency_id',
        'supplier_id',
        'batch_no',
        'container_capacity_id',
        'product_identifier',
        'expiry_date',
        'container_qty',
        'capacity_qty',
        'status',
        'created_by',
        'branch_id',
        'updated_by',
        'purchasing_unit_id',
        'selling_unit_id',
'is_actual'

    ];


    public function suppliers()
    {

        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }
    public function currency()
    {

        return $this->belongsTo(Currency::class);
    }
    public function branches()
    {

        return $this->belongsTo(BusinessBranch::class, 'branch_id', 'id');
    }
    public function productType()
    {

        return $this->belongsTo(ProductType::class);
    }
    public function price()
    {

        return $this->belongsTo(Price::class);
    }
    public function containerTypeCapacities()
    {

        return $this->belongsTo(ContainerTypeCapacity::class, 'container_type_capacity_id', 'id');
    }

}
