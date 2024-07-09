<?php

namespace App\Services\Supply\SupplierService;

use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SupplierRepository 
{
    public function index(Request $request)
    {
       
        return Supplier::latest()->paginate(20);

    }
    public function create(array $data)
    {
        try {
            
            return Supplier::create($data);

        } catch (QueryException $exception) {
            Log::channel('insertion_errors')->error('Error creating user: ' . $exception->getMessage());

            return response()->json(['message' => 'Insertion failed.'], 500);
        } 
        
    }

    public function findById($id)
    {
        return Supplier::find($id);
    }

    public function update($id, array $data)
    {
        $Supplier = $this->findById($id);
      
        if ($Supplier) {

            $Supplier->update($data);
        }
        return $Supplier;
    }

    public function delete($id)
    {
        $Supplier = $this->findById($id);
        if ($Supplier) {
            return $Supplier->delete();
        }
        return null;
    }
}
