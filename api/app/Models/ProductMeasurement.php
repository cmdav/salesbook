<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class ProductMeasurement extends Model
{
    use HasFactory;
    use  SetCreatedBy;
    use HasUuids;
    protected $fillable = [
        'product_type_id',

        'purchasing_unit_id',

    ];

    public function purchaseUnit()
    {
        return $this->belongsTo(PurchaseUnit::class, 'purchasing_unit_id', 'id')->select("id", "purchase_unit_name", "unit", "parent_purchase_unit_id");
    }
}
