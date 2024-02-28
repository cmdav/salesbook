<?php

namespace App\Services\Inventory\MeasurementService;

use App\Models\Measurement;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class MeasurementRepository 
{
    public function index()
    {
       
        return Measurement::select('id','measurement_name','unit')->latest()->get();

    }
    public function create(array $data)
    {
       
        return Measurement::create($data);
    }

    public function findById($id)
    {
        return Measurement::find($id);
    }

    public function update($id, array $data)
    {
       $measurement = $this->findById($id);
      
        if ($measurement) {

           $measurement->update($data);
        }
        return $measurement;
    }

    public function delete($id)
    {
       $measurement = $this->findById($id);
        if ($measurement) {
            return $measurement->delete();
        }
        return null;
    }
}
