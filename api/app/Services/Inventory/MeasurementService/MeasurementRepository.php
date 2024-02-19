<?php

namespace App\Services\Inventory\OrganizationService;

use App\Models\Organization;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class OrganizationRepository 
{
    public function index()
    {
       
        return Organization::latest()->paginate(20);

    }
    public function create(array $data)
    {
       
        return Organization::create($data);
    }

    public function findById($id)
    {
        return Organization::find($id);
    }

    public function update($id, array $data)
    {
        $organization = $this->findById($id);
      
        if ($organization) {

            $organization->update($data);
        }
        return $organization;
    }

    public function delete($id)
    {
        $organization = $this->findById($id);
        if ($organization) {
            return $organization->delete();
        }
        return null;
    }
}
