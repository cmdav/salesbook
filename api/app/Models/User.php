<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Support\Str;
use App\Traits\SetCreatedBy;
use Auth;

class User extends Authenticatable
{
    use SetCreatedBy, HasUuids, HasApiTokens, HasFactory, Notifiable;

   

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'company_name',
        'contact_person',
        'last_name',
        'middle_name',
        'phone_number',
        'type_id',
        'organization_id',
        'organization_code',
        'email',
        'role_id',
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
    // public function getTypeIdAttribute($value)
    // {
        
    //     switch ($value) {
        
    //         case 0:
    //             return 'customer';
    //         case 1:
    //             return 'supplier';
    //         case 2:
    //             return 'company';
    //         default:
    //             return 'customer'; 
    //     }
    // }
    protected static function boot() {
        parent::boot();

        static::creating(function ($user) {

            $request = app('request');

           
            if (!$request->has('role_id')) {

              
                do {
                    $time = time(); 
                    $randomNumber = rand(100000, 999999);
                    $hash = hash('sha256', $randomNumber . $time);
                    $code = hexdec(substr($hash, 0, 6)) % 1000000; 
                
                } while (User::where('organization_code', $code)->exists());
                
           
                 $user->organization_code = $code;
                 $user->token =  hexdec(substr($hash, 0, 6)) % 3000000;


                $adminRole = JobRole::where('role_name', 'Admin')->first();
                if (!$adminRole) {
                    throw new ModelNotFoundException('Error from user Admin role not found.');
                }
                $user->role_id = $adminRole->id;
            }
            else{

                $user->email_verified_at = now();
              
                if (Auth::check()) {
                    $user->organization_code = Auth::user()->organization_code;
                }
            }
            // $user->token = \Str::uuid();
        });

        static::created(function ($user) {
            $request = app('request');
            if (!$request->has('role_id')) {

                  $request = app('request');
                    $organization = new Organization([
                        'id' => Str::uuid(),

                        'organization_code' => $user->organization_code,
                        'organization_type' =>  $request->input('organization_type'),
                        'organization_logo' => 'logo.png', 
                        //'organization_email' => $user->email,
                        'user_id' => $user->id,
                    
                    ]);
            
                    $organization->save();
             }
        });

        static::updated(function ($user) {
            if (isset($user->organization_id)) {
              
                SupplierOrganization::where([
                    'supplier_id' => $user->id, 
                    'organization_id' => $user->organization_id
                ])->update(['status' => 1]);
            }
        });
    }
    public function supplier(){

        return $this->hasOne(Supplier::class);
    }
    public function supplierOrganization(){

        return $this->hasMany(SupplierOrganization::class, "supplier_id","id");
    }
    public function role(){

        return $this->hasOne(JobRole::class, 'id','role_id');
    }
    public function organization(){

        return $this->hasOne(Organization::class, 'user_id','id');
    }














}
