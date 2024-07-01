<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessBranch extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'state_id',
        'postal_code',
        'city',
        'country_id',
        'contact_person',
        'phone_number',
        'email',
    ];
    protected $hidden = [
    //  'name'
        'country',
        'state'
      
    ];
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Define the relationship with the State model
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // Accessor for country name
    public function getCountryNameAttribute()
    {
        return $this->country ? $this->country->name : null;
    }

    // Accessor for state name
    public function getStateNameAttribute()
    {
        return $this->state ? $this->state->name : null;
    }
    protected $appends = ['country_name', 'state_name'];
    
}
