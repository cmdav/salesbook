<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Customer extends Model
{
    use  SetCreatedBy, HasUuids, HasFactory;

   

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'company_name',
        'contact_person',
        'email',
        'phone_number',
        'branch_id',
        'address',
        'type_id', // assuming 'type_id' is the correct column name in your database
        'created_by' // only include this if you still need it
    ];
    
     public function getTypeIdAttribute($value)
    {
        
        switch ($value) {
        
            case 0:
                return 'others';
            case 1:
                return 'individual';
            case 2:
                return 'company';
            default:
                return 'others'; 
        }
    }
    public function setTypeIdAttribute($value)
    {
        switch (strtolower($value)) {
            case 'others':
                $this->attributes['type_id'] = 0;
                break;
            case 'individual':
                $this->attributes['type_id'] = 1;
                break;
            case 'company':
                $this->attributes['type_id'] = 2;
                break;
            default:
                $this->attributes['type_id'] = 1; 
        }
    }
    public function scopeOfType($query, $type)
    {
        $typeId = 0; // Default type_id for 'other'
    
        switch ($type) {
            case 'company':
                $typeId = 2;
                break;
            case 'individual':
                $typeId = 1;
                break;
        }
    
        return $query->where('type_id', $typeId);
    }
    
   

}
