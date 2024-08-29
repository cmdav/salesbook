<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Carbon\Carbon;

class ProductType extends Model
{
    use  SetCreatedBy;
    use HasUuids;
    use HasFactory;
    protected $fillable = [
        'product_type_name',
        'product_id',
        'product_type_image',
        'product_type_description',
        'vat',
        'organization_id',
       // 'measurement_id',
        'selling_price',
        'selling_unit_capacity_id',
        'purchase_unit_id',
       // 'container_type_capacity_id',
       // 'container_type_id',
        'supplier_id',
        'created_by',
        'updated_by',
       // 'type',
        'barcode',
        'is_container_type'
    ];
    protected $hidden = [
       // 'barcode',


    ];
    // protected $casts = [

    //     'barcode' => 'hashed',
    // ];
    // public function getIsContainerTypeAttribute($value)
    // {
    //     return $value == 1 ? 'Yes' : 'No';
    // }

    // Mutator for is_container_type
    // public function setIsContainerTypeAttribute($value)
    // {
    //     $this->attributes['is_container_type'] = strtolower($value) == 'yes' ? 1 : 0;
    // }
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-y H:i:s');
    }

    public function suppliers()
    {

        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }
    public function product()
    {

        return $this->belongsTo(Product::class);
    }
    public function batches()
    {

        return $this->hasMany(Store::class, 'product_type_id', 'id')->select('id', 'product_type_id', 'batch_no', 'quantity_available');
    }
    public function price()
    {

        return $this->hasMany(Price::class);
    }
    public function pricenotification()
    {

        return $this->hasOne(PriceNotification::class);
    }
    public function store()
    {

        return $this->hasOne(Store::class, 'product_type_id', 'id');
    }
    public function activePrice()
    {
        return $this->hasOne(Price::class)->where('status', 1)->latest('created_at');
    }
    public function latestPurchase()
    {
        return $this->hasOne(Purchase::class, 'product_type_id', 'id')->latest('created_at');
    }
    // public function containerCapacities()
    // {
    //     return $this->belongsTo(ContainerTypeCapacity::class, 'container_type_capacity_id', 'id');
    // }
    // public function sellingUnitCapacities()
    // {
    //     return $this->belongsTo(SellingUnitCapacity::class, 'selling_unit_capacity_id', 'id');
    // }
    public function getLatestPriceAttribute()
    {
        // Get the latest price record
        $latestPrice = $this->price()->latest()->first();

        // Check if price exists
        if ($latestPrice) {
            // If price_id is set, fetch the related price
            if ($latestPrice->price_id) {
                $referencePrice = Price::find($latestPrice->price_id);
                if ($referencePrice) {
                    return [
                        'price_id' => $latestPrice->price_id,
                        'cost_price' => $referencePrice->is_new ? $referencePrice->new_cost_price : $referencePrice->cost_price,
                        'selling_price' => $referencePrice->is_new ? $referencePrice->new_selling_price : $referencePrice->selling_price,
                    ];
                }
            }

            // If price_id is not set, use the current price
            return [
                'price_id' => $latestPrice->id,
                'cost_price' => $latestPrice->is_new ? $latestPrice->new_cost_price : $latestPrice->cost_price,
                'selling_price' => $latestPrice->is_new ? $latestPrice->new_selling_price : $latestPrice->selling_price,
            ];
        }

        // Return null if no price found
        return null;
    }
    public function sellingUnitCapacity()
    {
        return $this->belongsTo(SellingUnitCapacity::class, 'selling_unit_capacity_id', 'id');
    }

    public function sellingUnit()
    {
        return $this->hasOneThrough(
            SellingUnit::class,
            SellingUnitCapacity::class,
            'id', // Foreign key on SellingUnitCapacity
            'id', // Foreign key on SellingUnit
            'selling_unit_capacity_id', // Local key on ProductType
            'selling_unit_id' // Local key on SellingUnitCapacity
        )->select('selling_units.id as selling_unit_id', 'selling_units.purchase_unit_id', 'selling_units.selling_unit_name');
    }

    public function purchaseUnit()
    {
        return $this->hasOneThrough(
            PurchaseUnit::class,
            SellingUnit::class,
            'id', // Foreign key on SellingUnit
            'id', // Foreign key on PurchaseUnit
            'selling_unit_capacity_id', // Local key on ProductType
            'purchase_unit_id' // Local key on SellingUnit
        )->select('purchase_units.id as purchase_unit_id', 'purchase_units.purchase_unit_name');
    }
    public function unitPurchase()
    {

        return $this->belongsTo(PurchaseUnit::class, "purchase_unit_id", "id");
    }
    public function getVatAttribute($value)
    {
        switch ($value) {
            case 0:
                return 'No';
            case 1:
                return 'Yes';
            default:
                return 'No';
        }
    }




}
