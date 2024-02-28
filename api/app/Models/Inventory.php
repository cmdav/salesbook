<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Inventory extends Model
{
    use   SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = ['product_id','store_id','quantity_available','last_updated_by','created_by'];
}
