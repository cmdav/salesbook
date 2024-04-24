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
        'created_by',
        'updated_by',
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')
                    ->select(['id', \DB::raw("CONCAT(first_name, ' ', COALESCE(contact_person, ''), ' ', last_name) as fullname")]);
    }

   
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')
                    ->select(['id', \DB::raw("CONCAT(first_name, ' ', COALESCE(contact_person, ''), ' ', last_name) as fullname")]);
    }

}
