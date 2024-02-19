<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Organization extends Model
{
    use  HasUuids,HasFactory;
    protected $fillable = [

        'organization_name',
        'organization_url',
        'organization_code', 
        'organization_logo',
        'created_by',
    ];
}
