<?php

namespace App\Services\Security\BusinessBranchService;

use Illuminate\Support\Facades\Log;
use App\Models\BusinessBranch;

use Exception;

class BusinessBranchRepository
{
    public function index()
    {
        // Get the paginated data with relations
        $branches = BusinessBranch::with(['creator', 'creator.organization', 'updater', 'updater.organization'])->paginate(20);

        // Modify the collection to set created_by and updated_by according to the condition
        $branches->getCollection()->transform(function ($branch) {
            $branch->created_by = optional($branch->creator)->first_name && optional($branch->creator)->last_name
                ? optional($branch->creator)->first_name . ' ' . optional($branch->creator)->last_name
                : (optional($branch->creator)->organization ? optional($branch->creator->organization)->organization_name : null);

            $branch->updated_by = optional($branch->updater)->first_name && optional($branch->updater)->last_name
                ? optional($branch->updater)->first_name . ' ' . optional($branch->updater)->last_name
                : (optional($branch->updater)->organization ? optional($branch->updater->organization)->organization_name : null);

            // Remove the creator and updater objects
            unset($branch->creator, $branch->updater);

            return $branch;
        });

        return $branches;
    }


    public function show($id)
    {
        return BusinessBranch::where('id', $id)->first();
    }

    public function store($data)
    {
        try {
            return BusinessBranch::create($data);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => 'false',
                'message' => 'Insertion error'
            ], 500);
        }

    }

    public function update($data, $id)
    {
        try {

            $model = BusinessBranch::where('id', $id)->first();

            if ($model) {
                $updateSuccessful = $model->update($data);

                if ($updateSuccessful) {
                    // Fetch the user using the 'created_by' field from the BusinessBranch model
                    $user = \App\Models\User::where('id', $model->created_by)->first();

                    if ($user) {
                        $user->update(['is_profile_complete' => 1]);
                    }
                }
            }

            return $model;
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => 'false',
                'message' => 'Insertion error'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $model = BusinessBranch::where('id', $id)->first();

            if($model) {
                $model->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Deletion successful'
            ], 200);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This branch is already in use'
            ], 500);
        }
    }
    public function listing()
    {
        return BusinessBranch::select('id', 'name')->get();
    }

}
