<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Organization extends Model
{
    use  SetCreatedBy, HasUuids,HasFactory;
    
    protected $fillable = [

        'organization_name',
        'organization_url',
        'organization_code', 
        'organization_logo',
        'created_by',
    ];
    protected static function boot() {

        parent::boot();

        static::creating(function ($organization) {
            do {
                $token = rand(100000, 999999); 
            } while (Organization::where('organization_code', $token)->exists());

            $organization->organization_code = $token;
        });
    }

    public function getOrganizationLogoAttribute($value): string
    {
        
        return url('/') . $value;
    }
}
