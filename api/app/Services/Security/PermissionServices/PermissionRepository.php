<?php

namespace App\Services\Security\PermissionServices;
use Illuminate\Support\Facades\Log;
use App\Models\Permission;
use Exception;


class PermissionRepository 
{
    public function index($roleId)
    {
        //return $roleId;
        $permissions = Permission::with('role:id,role_name', 'page:id,page_name')
                    ->where('role_id', $roleId)
                        ->get();
        
    
        $transformedItems = $permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'page_name' => optional($permission->page)->page_name, 
                'page_id' => $permission->page_id,
                'role_name' => optional($permission->role)->role_name, 
                'role_id' => $permission->role_id,
                'read' => $permission->read,
                'write' => $permission->write,
                'update' => $permission->update,
                'del' => $permission->del,
            ];
        })->toArray();

       // $permissions->setCollection(collect($transformedItems));
        
        return  $transformedItems;
    }

    

    public function create(array $data)
    {
        try{
        $roleId = $data['role_id'];
        $permissionsData = $data['permissions'];
        
        foreach ($permissionsData as $permissionData) {

            
            $uniqueCriteria = [
                'role_id' => $roleId,
                'page_id' => $permissionData['page_id'] 
            ];

            $permissionData['role_id'] = $roleId; 
        
            $permission = Permission::updateOrCreate($uniqueCriteria, $permissionData);
        }
    
        return response()->json(['message' => 'Permissions created or updated successfully!'], 201); 
        
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        DB::rollBack();
        return response()->json(['message' =>'Failed to create permission'] , 500);
        //return 'Failed to create purchases';
    }
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
