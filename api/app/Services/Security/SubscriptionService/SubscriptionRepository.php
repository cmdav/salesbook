<?php

namespace App\Services\Security\SubscriptionService;
use Illuminate\Support\Facades\Log;

use App\Models\Subscription;

use Exception;

class SubscriptionRepository
{
    public function index()
    {
        return Subscription::paginate(20);
    }

    public function show($id)
    {
        return Subscription::where('id',$id)->first();
    }

    public function store($data)
    {
        try {
            return Subscription::create($data);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Insertion error'
            ], 500);
        }
        
    }

    public function update($data, $id)
    {
        try {  
        $model = Subscription::where('id',$id)->first();
            if($model){
                $model->update($data);
            }
            return $model;
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Insertion error'
            ], 500);
        }
    }   

    public function destroy($id)
    {
        $model = Subscription::findOrFail($id);
        $model->delete();
        return $model;
    }
}
