<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait SetCreatedBy
{
    protected static function bootSetCreatedBy()
    {
        static::creating(function ($model) {
            // Check if the table has 'created_by' column and set it
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'created_by')) {
                $model->created_by = Auth::id();
            }

            // Check if the table has 'user_id' column and set it
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'user_id')) {
                $model->user_id = Auth::id();
            }

            // Check if the table has 'organization_id' column and set it
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'organization_id')) {
                $model->organization_id = Auth::id(); // Adjust as needed
            }

            // Check if the table has 'updated_by' column and set it
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            // Always set 'updated_by' during an update, regardless of current value
           
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'updated_by')) {
               
                $model->updated_by = Auth::id();
            }
        });
    }
}
