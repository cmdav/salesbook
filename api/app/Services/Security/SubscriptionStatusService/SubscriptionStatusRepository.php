<?php

namespace App\Services\Security\SubscriptionStatusService;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionStatus;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class SubscriptionStatusRepository
{
    public function query(){

        return SubscriptionStatus::select('id', 'plan_id', 'start_time', 'end_time', 'organization_id')
        ->with('subscription:id,plan_name,description', 'organization:id,user_id,organization_name,organization_code');
    }
    public function checkSubscriptionStatus($orgId)
    {
        $subscription = SubscriptionStatus::select('start_time', 'end_time', 'organization_id')
            ->where('organization_id', $orgId)
            ->first();

        // If organization_id is not found, return true
        if (!$subscription) {
            return true;
        }

        // Check if the end_time has not expired
        if (Carbon::parse($subscription->end_time)->isFuture()) {
            return true;
        }

        // If end_time has expired, return false
        return false;
    }
    public function index()
    {
        $subscriptionStatuses = $this->query()->paginate(20);

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
                'subscription_plan_name' => $item->subscription->plan_name ?? null,
                'subscription_description' => $item->subscription->description ?? null,
                'organization_name' => $item->organization->organization_name ?? null,
                'organization_code' => $item->organization->organization_code ?? null,
                'organization_id' => $item->organization->id ?? null,
            ];
        });

        // Replace the original data with the transformed data
        $subscriptionStatuses->setCollection($flattenedData);

        return $subscriptionStatuses;
    }

    public function show($id)
    {
        $subscriptionStatus = $this->query()
            ->where('organization_id', $id)
            ->first();

        if ($subscriptionStatus) {
            // Flatten the structure
            $flattenedData = [
                'id' => $subscriptionStatus->id,
                'start_time' => $subscriptionStatus->start_time,
                'end_time' => $subscriptionStatus->end_time,
                'status' => Carbon::parse($subscriptionStatus->end_time)->isPast() ? 'expired' : 'active',
                'subscription_plan_name' => $subscriptionStatus->subscription->plan_name ?? null,
                'subscription_description' => $subscriptionStatus->subscription->description ?? null,
                'organization_name' => $subscriptionStatus->organization->organization_name ?? null,
                'organization_id' => $subscriptionStatus->organization->id ?? null,
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
        $model = SubscriptionStatus::where('organization_id',$id)->first();
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
            $subscription = SubscriptionStatus::findOrFail($id);
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
