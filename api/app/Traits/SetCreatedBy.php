<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait SetCreatedBy
{
   
    

    protected static function bootSetCreatedBy()
    {
        static::creating(function ($model) {
            // check if the table has created_by column
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'created_by') && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
            // check if the table has user_id column
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'user_id') && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
            // check if the table has updated_by column
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'updated_by') && empty($model->updated_by)) {
                $model->updated_by = Auth::id();
            }
           
        });
    }

}
