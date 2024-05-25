<?php

namespace App\Services\Inventory\MeasurementService;

use App\Models\Measurement;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class MeasurementRepository 
{
    private function query(){

        return Measurement::select('id','measurement_name','unit')->latest();
    }
    
    public function index()
    {
        
        $measurements =  Measurement::select('id','measurement_name','unit',"created_by","updated_by")->latest()->with('creator','updater')->get();
      
       
        $transformed = $measurements->map(function($measurement) {
            return [
                'id' => $measurement->id,
                'measurement_name' => $measurement->measurement_name,
                'unit' => $measurement->unit,
                'created_by' => $measurement->creator->fullname ?? '',  
                'updated_by' => $measurement->updater->fullname ?? '',
                
            ];
        });

        return $transformed;
       

    }
    public function searchMeasurement($searchCriteria){

        return $this->query()->where('measurement_name', 'like', '%' . $searchCriteria . '%')->latest()->get();
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
