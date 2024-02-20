<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Supplier extends Model
{
    use  SetCreatedBy,HasUuids, HasFactory;

    protected $fillable = [
        'user_id', 
        'bank_name', 
        'account_number', 
        'account_name', 
        'state', 
        'address', 
        'dob'
    ];
}
