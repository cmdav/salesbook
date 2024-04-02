<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Pages extends Model
{
    use   SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = ['page_name','created_by','updated_by'];
}
