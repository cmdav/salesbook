<?php

namespace App\Services\Inventory\MeasurementService;

use App\Models\Measurement;
use Illuminate\Support\Facades\Log;
use Exception;

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
        try{
       
         $data  =Measurement::create($data);
        return response()->json([
            'success' => true,
            'message' => 'Measurement created successfully',
            'data'=>$data
        ], 201);
      } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Product category could not be created',
        ], 500);
      }
    }

    public function findById($id)
    {
        return Measurement::find($id);
    }

    public function update($id, array $data)
    {
        try{
       $measurement = $this->findById($id);
      
        if ($measurement) {

           $measurement->update($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Measurement successfully updated',
            'data' => $measurement,
        ], 200);
    }
       
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Product category could not be updated',
        ], 500);
      }
    }

    public function delete($id)
    {
        try{
       $measurement = $this->findById($id);
        if ($measurement) {
            $measurement->delete();
            return response()->json([
                'success' => true,
                'message' => 'Deletion successful',
            ], 200);
        }
        return null;

    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'This Measurement is already in use',
        ], 500);
    }
    }
}
