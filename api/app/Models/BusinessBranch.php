<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SetCreatedBy;

class BusinessBranch extends Model
{
    use SetCreatedBy;
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
        'address',
        'created_by',
        'updated_by'
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
