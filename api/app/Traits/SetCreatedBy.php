<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Http\Request;

trait SetCreatedBy
{
    protected static function bootSetCreatedBy()
    {
        static::creating(function ($model) {
            $request = request(); // Get the current request instance
            
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
                if ($request->has('organization_id')) {
                    $model->organization_id = $request->input('organization_id');
                } else {
                    $model->organization_id = Auth::id();
                }
            }

            // Always set 'updated_by' during creation
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')
                    ->select(['id', \DB::raw("
                        CASE 
                            WHEN contact_person IS NOT NULL AND contact_person != '' 
                            THEN contact_person 
                            ELSE CONCAT(first_name, ' ', last_name) 
                        END as fullname
                    ")]);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')
                    ->select(['id', \DB::raw("
                        CASE 
                            WHEN contact_person IS NOT NULL AND contact_person != '' 
                            THEN contact_person 
                            ELSE CONCAT(first_name, ' ', last_name) 
                        END as fullname
                    ")]);
    }
}
