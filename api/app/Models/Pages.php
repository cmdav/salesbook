<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;

class Pages extends Model
{
    use   SetCreatedBy, HasUuids, HasFactory;
    protected $fillable = ['page_name','created_by','updated_by'];
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
