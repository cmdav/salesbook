<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SetCreatedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ContactForm extends Model
{
    use SetCreatedBy, HasUuids;
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'message',  
    ];

}
