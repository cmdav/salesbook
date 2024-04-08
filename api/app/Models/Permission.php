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

    public function page(){

        return $this->belongsTo(Pages::class, 'page_id','id');
    }
    
    public function role(){

        return $this->belongsTo(JobRole::class, 'role_id','id');
    }
}

