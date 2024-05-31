<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;


class Subscription extends Model
{
    use  SetCreatedBy, HasFactory;

    protected $fillable = [
        'plan_name', 
        'description',
        'created_by',
        'updated_by'
    ];
}
