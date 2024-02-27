<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Measurement extends Model
{
    use   SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = ['measurement_name','unit','created_by'];
}
