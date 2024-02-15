<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Task extends Model
{
    use HasUuids, HasFactory;
    protected $fillable=[
        'name',
        'description',
        'start_time',
        'end_time',
        'created_by',
        'id',
        'user_id',
        'task_status'
    ];

    protected $hidden =['user_id'];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($task) {
            $task->user_id = Auth::id();
			$task->task_status = "pending";
        });
    }
    
}