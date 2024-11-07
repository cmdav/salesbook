<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use App\Traits\SetCreatedBy;
use Carbon\Carbon;

class Log extends Model
{
    use HasFactory;
    use SetCreatedBy;
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'event',
        'model_id',
        'model',
        'activity',
        'payload',
        'route'
    ];
    protected $hidden = [
        'id',
        'user_id',
        'event',
        'model_id',
        'model',
        'payload',
        'route',
       // 'created_at' ,// Hide original created_at attribute; we'll use accessor instead
        'updated_at',
    ];

    // Make only activity and updated_at visible
    protected $visible = [
        'activity',
        'updated_at',
        'created_at' // We'll include the formatted created_at through an accessor
    ];

    // Accessor for created_at to return human-readable format
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }
}
