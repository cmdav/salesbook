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

            return Organization::create($data);
            
        } catch (QueryException $exception) {
            Log::channel('insertion_errors')->error('Error creating user: ' . $exception->getMessage());

            return response()->json(['message' => 'Insertion failed.'], 500);
        }
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
