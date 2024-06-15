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
                return response()->json([
                    'success' => true,
                    'message' => 'Update successful',
                    'data' => $data,
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Update fail',
            ], 404);
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
        try{
            $subscription = Subscription::where('id',$id)->first();
            if ($subscription) {
                 $subscription->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Deletion successful',
                ], 200);
            }
          
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'subscription could not be deleted',
            ], 500);
        }
    }
}
