<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        'branch_id',
        'organization_id',
        'organization_code',
        'email',
        'role_id',
        'dob',
        'password',
        'token',
        'email_verified_at'
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
    //protected static function boot() {
       // parent::boot();

        // static::creating(function ($user) {

        //     $request = app('request');

        //    //company and individual and supplier do not have role
        //     if (!$request->has('role_id')) {
            
        //         if (!$request->has('type')) 
        //         {
        //             //company and individual do not have a type
        //                 do {
        //                     $time = time(); 
        //                     $randomNumber = rand(100000, 999999);
        //                     $hash = hash('sha256', $randomNumber . $time);
        //                     $code = hexdec(substr($hash, 0, 6)) % 1000000; 
                        
        //                 } while (User::where('organization_code', $code)->exists());
                        
                
        //                 $user->organization_code = $code;
        //                 $user->token =  hexdec(substr($hash, 0, 6)) % 3000000;


        //                 $adminRole = JobRole::where('role_name', 'Admin')->first();
        //                 if (!$adminRole) {
        //                     throw new ModelNotFoundException('Error from user Super Admin role not found.');
        //                 }
        //                 $user->role_id = $adminRole->id;

        //                 if (($request->has('organization_type')) && ($request->input('organization_type') == 'company')) {

        //                     $user->type_id = 2;

        //                 } else if (($request->has('organization_type')) && ($request->input('organization_type') == 'sole_properietor')) {

        //                     $user->type_id = 1;
        //                 }
        //         }
        //         else {
        //             //supplier have a type_id of 3
        //             $user->type_id = 3;
        //             $user->email_verified_at = now();
        //             $time = time(); 
        //             $randomNumber = rand(100000, 999999);
        //             $hash = hash('sha256', $randomNumber . $time); 
        //             $user->token =  hexdec(substr($hash, 0, 6)) % 3000000;
        //         }
                
               
        //     }
        //     else{
        //         //sale personnel commence with role
        //         $user->email_verified_at = now();
    
        //         if (Auth::check()) {
        //             $user->organization_code = Auth::user()->organization_code;
        //             $user->organization_id = Auth::user()->organization_id;
        //         }
        //     }
           
        // });

        // static::created(function ($user) {
        //     $request = app('request');
        //     if (($request->input('organization_type') == 'company') || ($request->input('organization_type') == 'sole_properietor')) { 

        //           $request = app('request');
        //             $organization = new Organization([
        //                 'id' => Str::uuid(),
        //                 'organization_name' => $user->company_name,
        //                 'organization_code' => $user->organization_code,
        //                 'organization_type' =>  $request->input('organization_type'),
        //                 'organization_logo' => 'logo.png', 
        //                 'user_id' => $user->id,
                    
        //             ]);
            
        //             $organization->save();
        //             $user->organization_id = $organization->id;
        //             $user->save();
        //      }
        // });

        // static::updated(function ($user) {
        //     if (isset($user->organization_id)) {
              
        //         SupplierOrganization::where([
        //             'supplier_id' => $user->id, 
        //             'organization_id' => $user->organization_id
        //         ])->update(['status' => 1]);
        //     }
           
        // });
    //}
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
    public function branches(){

        return $this->belongsTo(BusinessBranch::class, 'branch_id','id');
    }














}
