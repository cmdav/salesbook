<?php

namespace App\Services\Security\JobRoleServices;

use App\Models\JobRole;


class JobRoleRepository 
{
    public function index()
    {
        return JobRole::select('id', 'role_name')->paginate(20);
      
    }
    public function names()
    {
        return JobRole::select('id', 'role_name')->get();
      
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
        $JobRole = $this->findById($id);
      
        if ($JobRole) {

            $JobRole->update($data);
        }
        return $JobRole;
    }

    public function delete($id)
    {
        $JobRole = $this->findById($id);
        if ($JobRole) {
            return $JobRole->delete();
        }
        return null;
    }
}
