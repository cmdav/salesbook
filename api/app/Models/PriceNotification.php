<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class PriceNotification extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = [
        'product_type_id',
        'supplier_id',
        'cost_price',
        'selling_price',
        'branch_id',
        'status',
        'created_by',
        'updated_by',
        
        
    ];
    public function getStatusAttribute($value)
    {
        switch ($value) {
            case 0:
                return 'pending';
            case 1:
                return 'decline';
            case 2:
                return 'accepted';
            default:
                return 'pending'; 
        }
    }

    
    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'pending':
                $this->attributes['status'] = 0;
                break;
            case 'decline':
                $this->attributes['status'] = 1;
                break;
            case 'accepted':
                $this->attributes['status'] = 2;
                break;
            default:
                $this->attributes['status'] = 0; 
        }
    }

    public function productTypes(){

        return $this->belongsTo(ProductType::class, 'product_type_id','id');
    }
    public function supplier(){

        return $this->belongsTo(User::class, 'supplier_id','id');
    }
    public function branches(){

        return $this->belongsTo(BusinessBranch::class, 'branch_id','id');
    }

}
