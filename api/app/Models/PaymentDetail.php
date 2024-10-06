<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class PaymentDetail extends Model
{
    use   SetCreatedBy;
    use HasUuids;
    use HasFactory;
    protected $fillable = [

        'payment_method_id',
        'account_name',
        'account_number',
        'payment_identifier',
        'created_by',
        'updated_by'
    ];

    public function payment_methods()
    {

        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }
}
