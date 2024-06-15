<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\SetCreatedBy;
use Carbon\Carbon;

class SubscriptionStatus extends Model
{
    use  SetCreatedBy, HasFactory;

    protected $fillable = [
        'plan_id',  
        'start_time', 
        'end_time', 
        'organization_id',
        'created_by',
        'updated_by'
    ];
    public function subscription(){

        return $this->belongsTo(Subscription::class,'plan_id', 'id');
    }
    public function organization(){

        return $this->belongsTo(Organization::class,'organization_id', 'id');
    }
    public function users(){

        return $this->belongsTo(User::class,'organization_id', 'id');
    }
    public function getStatusAttribute()
    {
        return Carbon::parse($this->end_time)->isPast() ? 'expired' : 'active';
    }
}
