<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class PaymentMethod extends Model
{
    use   SetCreatedBy;
    use HasUuids;
    use HasFactory;
    protected $fillable = ['payment_name','created_by','updated_by'];
}
