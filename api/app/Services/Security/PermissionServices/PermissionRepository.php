<?php

namespace App\Services\Security\PermissionServices;

use App\Models\Permission;


class PermissionRepository 
{
    public function index()
    {
        return Permission::paginate(20);
      
    }
    

    public function create(array $data)
    {
        $roleId = $data['role_id'];
        $permissionsData = $data['permissions'];
        
        foreach ($permissionsData as $permissionData) {

            $permissionData['role_id'] = $roleId;
            $permission = Permission::create($permissionData);
        }
    
        return response()->json(['message' => 'Permissions created successfully!'], 201);           
    }
    

    public function findById($id)
    {
        return Permission::find($id);
    }

    public function update($id, array $data)
    {
        $Permission = $this->findById($id);
      
        if ($Permission) {

            $Permission->update($data);
        }
        return $Permission;
    }

    public function delete($id)
    {
        $Permission = $this->findById($id);
        if ($Permission) {
            return $Permission->delete();
        }
        return null;
    }
}
