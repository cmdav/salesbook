<?php

namespace App\Services\Security\SubscriptionStatusService;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionStatus;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class SubscriptionStatusRepository
{
    public function index()
    {
        $subscriptionStatuses = SubscriptionStatus::select('id', 'plan_id', 'start_time', 'end_time', 'organization_id')
        ->with('subscription:id,plan_name,description', 'organization:id,user_id,organization_name,organization_code')
            ->paginate(20);

        // Transform the collection to a linear structure
        $flattenedData = $subscriptionStatuses->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                //'plan_id' => $item->plan_id,
                'start_time' => $item->start_time,
                'end_time' => $item->end_time,
                'status' => Carbon::parse($item->end_time)->isPast() ? 'expired' : 'active',
                //'organization_id' => $item->organization_id,
                //'subscription_id' => $item->subscription->id,
                'subscription_plan_name' => $item->subscription->plan_name,
                'subscription_description' => $item->subscription->description,
                //'organization_id' => $item->organization->id,
                //'organization_user_id' => $item->organization->user_id,
                'organization_name' => $item->organization->organization_name,
                'organization_code' => $item->organization->organization_code,
            ];
        });

        // Replace the original data with the transformed data
        $subscriptionStatuses->setCollection($flattenedData);

        return $subscriptionStatuses;
    }

    public function show($id)
    {
        $subscriptionStatus = SubscriptionStatus::select('id', 'plan_id', 'start_time', 'end_time', 'organization_id')
            ->with('subscription:id,plan_name,description', 'organization:id,user_id,organization_name,organization_code')
            ->where('organization_id', $id)
            ->first();

        if ($subscriptionStatus) {
            // Flatten the structure
            $flattenedData = [
                'id' => $subscriptionStatus->id,
                //'plan_id' => $subscriptionStatus->plan_id,
                'start_time' => $subscriptionStatus->start_time,
                'end_time' => $subscriptionStatus->end_time,
                'status' => Carbon::parse($subscriptionStatus->end_time)->isPast() ? 'expired' : 'active',
               // 'organization_id' => $subscriptionStatus->organization_id,
                //'subscription_id' => $subscriptionStatus->subscription->id,
                'subscription_plan_name' => $subscriptionStatus->subscription->plan_name,
                'subscription_description' => $subscriptionStatus->subscription->description,
                //'organization_id' => $subscriptionStatus->organization->id,
               // 'organization_user_id' => $subscriptionStatus->organization->user_id,
                'organization_name' => $subscriptionStatus->organization->organization_name,
                'organization_code' => $subscriptionStatus->organization->organization_code,
            ];

            return response()->json($flattenedData);
        } else {
            return response()->json(['message' => 'Subscription Status not found'], 404);
        }
    }


    public function store($data)
    {
        try {
            return SubscriptionStatus::create($data);
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
        $model = SubscriptionStatus::where('id',$id)->first();
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
        $model = SubscriptionStatus::findOrFail($id);
        $model->delete();
        return $model;
    }
}
