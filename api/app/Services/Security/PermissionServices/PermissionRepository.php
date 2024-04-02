<?php

namespace App\Services\Security\PermissionServices;

use App\Models\Permission;


class PermissionRepository 
{
    public function index($roleId)
    {
       
        $permissions = Permission::with('role:id,role_name', 'page:id,page_name')
                        ->where('role_id', $roleId)
                        ->paginate(20);
        
      
        $transformedItems = $permissions->getCollection()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'page_name' => $permission->page->page_name,
                'page_id' => $permission->page_id,
                'role_name' => $permission->role->role_name,
                'role_id' => $permission->role_id,
                'read' => $permission->read,
                'write' => $permission->write,
                'update' => $permission->update,
                'delete' => $permission->delete,
                
              
            ];
        })->toArray();
    
        $permissions->setCollection(collect($transformedItems));
    
        
        return $permissions;
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
