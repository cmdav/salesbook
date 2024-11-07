<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use App\Traits\SetCreatedBy;

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
    ];
}
