<?php

namespace App\Services\Security\JobRoleServices;
use Illuminate\Support\Facades\Log;
use App\Models\JobRole;
use Illuminate\Database\QueryException;

class JobRoleRepository 
{
    private function query(){
        
        return JobRole::select('id', 'role_name')->where('role_name', "!=", 'Super Admin');
    }
    public function index()
    {
        return $this->query()->paginate(20);
      
    }
    public function names()
    {
        return $this->query()->get();
      
    }
    

    public function create(array $data)
    {
       
            
        return JobRole::create($data); 

           
    }
    

    public function findById($id)
    {
        return JobRole::find($id);
    }

    public function update($id, array $data)
    {
        try{
        $JobRole = $this->findById($id);
      
        if ($JobRole) {

            $JobRole->update($data);
            return response()->json([
                'success' =>true,
                'message' => 'Update successful',
                'data' => $JobRole
            ], 200);
            }
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This Product could not be updated',
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $JobRole = $this->findById($id);
          
              
                if ($JobRole) {
                    $JobRole->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Deletion successful',
                    ], 200);
                }
            
        } catch (QueryException $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "This Job role is already in use",
                'errors' => 'There was an error deleting this role'
            ], 500);
        }
        
    }
}
