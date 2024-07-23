<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Illuminate\Support\Facades\Auth;

class Organization extends Model
{
    use  SetCreatedBy, HasUuids,HasFactory;
    
    protected $fillable = [

        'organization_name',
        //'organization_url',
        'organization_type',
        'organization_code', 
        'organization_logo',
        'user_id',
        'id',
        'created_by',
        'updated_by',
        'company_name', 
        'contact_person', 
        'company_address', 
    ];
   

    // public function getOrganizationLogoAttribute($value): string
    // {
        
    //     return url('/') . $value;
    // }
    public function getOrganizationTypeAttribute($value): string
    {
        return $value == 2 ? 'company' : 'sole_proprietor';
    }
    public function setOrganizationTypeAttribute($value)
    {
       
        $this->attributes['organization_type'] = $value == 'company' ? 2 : 1;
    }
   

}
