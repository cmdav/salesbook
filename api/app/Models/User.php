<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class User extends Authenticatable
{
    use HasUuids, HasApiTokens, HasFactory, Notifiable;

   

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'phone_number',
        'type_id',
        'organization_id',
        'organization_code',
        'email',
        'dob',
        'password',
        'token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getTypeIdAttribute($value)
    {
        
        switch ($value) {
        
            case 0:
                return 'customer';
            case 1:
                return 'supplier';
            case 2:
                return 'company';
            default:
                return 'customer'; 
        }
    }
    protected static function boot() {
        parent::boot();

        static::creating(function ($user) {

            $request = app('request');

            $user->token = \Str::uuid()->toString();
            
            if (isset($user->organization_code) && $request->has('password')) {
               
                $organization = Organization::where('organization_code', $user->organization_code)->first();

                if (!$organization) {
                  
                    throw new ModelNotFoundException('The provided organization code does not exist.');
                }

              
                $user->organization_id = $organization->id;
            }
        });
    }
}
