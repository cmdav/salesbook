<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait SetCreatedBy
{
   
    

    protected static function bootSetCreatedBy()
    {
        static::creating(function ($model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'created_by') && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'user_id') && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });
    }

}
