<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Currency extends Model
{
    use   SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = ['currency_name','currency_symbol','created_by','updated_by','status'];
    protected static function boot() {
        parent::boot();

        static::creating(function ($currency) {

            if ($currency->currency_name == 'naira') {
                $currency->status = 1;
            }
        });

       
    }
    public function getStatusAttribute($value){

        switch ($value){
            case 1:
                return 'Active';
            default:
               return 'Inactive';
        }
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
