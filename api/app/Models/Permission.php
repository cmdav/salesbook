<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Permission extends Model
{
    use   SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = ['page_id','role_id','read','update','delete','write','created_by','updated_by'];
}
