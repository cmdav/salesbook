<?php

namespace App\Services\Inventory\OrganizationService;

use App\Models\Organization;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class OrganizationRepository 
{
    public function index()
    {
       
        return Organization::latest()->paginate(3);

    }
    public function create(array $data)
    {
        try {

            $organization  =Organization::create($data);
            return response()->json(['message' => 'Insertion failed.', 'data' => $organization,   'success' => 'true'], 201);
            
        } catch (QueryException $exception) {
            Log::channel('insertion_errors')->error('Error creating user: ' . $exception->getMessage());

            return response()->json(['message' => 'Insertion failed.',   'success' => false,], 500);
        }
    }

    public function findById($id)
    {
        return Organization::find($id);
    }

    public function update($id, array $data)
    {
        try{
        $organization = Organization::where('user_id', $id)->first();
      
        
        if ($organization) {

            $organization->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Update was successful',
            ], 200);
        }
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'This record could not be updated',
        ], 500);
    }
    }

    public function delete($id)
    {
        try{
        $organization = $this->findById($id);
        if ($organization) {
             $organization->delete();
            return response()->json([
                'success' => true,
                'message' => 'Deletion successful',
            ], 200);
        }
      
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Organization could not be deleted',
        ], 500);
    }
    }
}
