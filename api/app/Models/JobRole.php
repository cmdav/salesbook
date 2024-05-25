<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class JobRole extends Model
{
    use   SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = ['role_name','created_by','updated_by'];


    public function permissions(){

        return $this->hasMany(Permission::class, 'role_id','id');
    }
   
}
