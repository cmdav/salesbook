<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Carbon\Carbon;

class ContainerType extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = [
        'id', 
        'container_type_name',  
        'created_by',
    'updated_by'
  ];
  public function containerCapacities()
  {
      return $this->hasMany(ContainerTypeCapacity::class, 'container_type_id', 'id');
  }
 
}
