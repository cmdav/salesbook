<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Currency extends Model
{
    use   SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = ['currency_name','currency_symbol','created_by','updated_by',];
}
