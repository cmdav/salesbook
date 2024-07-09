<?php

namespace App\Services\Security\BusinessBranchService;
use Illuminate\Support\Facades\Log;
use App\Models\BusinessBranch;

use Exception;

class BusinessBranchRepository
{
    public function index()
    {
        return BusinessBranch::paginate(20);
    }

    public function show($id)
    {
        return BusinessBranch::where('id',$id)->first();
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
        $model = BusinessBranch::where('id',$id)->first();
            if($model){
                $model->update($data);
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
            $model = BusinessBranch::where('id',$id)->first();
            
            if($model){
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
        return BusinessBranch::select('id','name')->get();
    }

}
