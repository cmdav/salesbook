<?php

namespace App\Services\Product\ContainerTypeService;

use App\Models\ContainerType;

use Exception;

class ContainerTypeRepository
{
    public function index()
    {
        $model =  ContainerType::select('id','container_type_name','created_by','updated_by')->with('containerCapacities:id,container_type_id,container_capacity')->paginate(20);
         
         if($model){
         return response()->json([ 'success' =>true, 'message' => 'Record retrieved successfully', 'data'=>$model], 200);
        }
        return response()->json([ 'success' =>false, 'message' => 'No record found', 'data'=>$model], 404);
    }

    public function show($id)
    {
        $model = ContainerType::where('id',$id)->with('containerCapacities:id,container_type_id,container_capacity')->first();
        if($model){
         return response()->json([ 'success' =>true, 'message' => 'Record retrieved successfully', 'data'=>$model], 200);
        }
        return response()->json([ 'success' =>false, 'message' => 'No record found', 'data'=>$model], 404);
    }

    public function store($data)
    {
        try {
             $model =  ContainerType::create($data);
             return response()->json([ 'success' =>true, 'message' => 'Insertion successful', 'data'=>$model], 200);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }
        
    }

    public function update($data, $id)
    {
        try {  
        $model = ContainerType::where('id',$id)->first();
            if($model){
                $model->update($data);
                 return response()->json([ 'success' =>true, 'message' => 'Update successful', 'data'=>$model], 200);
            }
           
             return response()->json([ 'success' =>false, 'message' => 'Record not found', 'data'=>$model], 404);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }
    }   

    public function destroy($id)
    {
        $model = ContainerType::findOrFail($id);
        $model->delete();
        return $model;
    }

    public function listAllContainer()
        {
            return ContainerType::select("id","container_type_name")->get();
        }

       
        public function containerWithCapacity($id)
        {
            $containerType = ContainerType::select('id', 'container_type_name')
                ->where('id', $id)
                ->with(['containerCapacities' => function ($query) {
                    $query->select('id', 'container_capacity', 'container_type_id');
                }])
                ->first();
        
            if ($containerType && $containerType->containerCapacities) {
                $containerType->container_capacities = $containerType->containerCapacities->map(function ($capacity) {
                    return collect($capacity)->except('container_type_id');
                });
            }
        
            return [
            
                    'id' => $containerType->id,
                    'container_type_name' => $containerType->container_type_name,
                    'container_capacities' => $containerType->container_capacities
                
            ];
        }
        
            
        

        
}