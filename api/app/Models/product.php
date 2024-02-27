<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class product extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    
    protected $fillable = [
        'product_name',
        'product_description',
        'product_image',
        'measurement_id',
        'created_by'
    ];

    public function measurement(){
        return $this->belongsTo(Measurement::class, 'measurement_id','id');
    }
    public function getProductImageAttribute($value): string
    {
        
        return url('/') . $value;
    }
}
