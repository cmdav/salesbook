<?php

namespace App\Services\Product\ContainerTypeCapacityService;

use App\Models\ContainerTypeCapacity;

use Exception;

class ContainerTypeCapacityRepository
{
    public function index()
    {
          $model =  ContainerTypeCapacity::paginate(20);
         if($model){
         return response()->json([ 'success' =>true, 'message' => 'Record retrieved successfully', 'data'=>$model], 200);
        }
        return response()->json([ 'success' =>false, 'message' => 'No record found', 'data'=>$model], 404);
    }

    public function show($id)
    {
        $model = ContainerTypeCapacity::where('id',$id)->first();
        if($model){
         return response()->json([ 'success' =>true, 'message' => 'Record retrieved successfully', 'data'=>$model], 200);
        }
        return response()->json([ 'success' =>false, 'message' => 'No record found', 'data'=>$model], 404);
    }

    public function store($data)
    {
        try {
             $model =  ContainerTypeCapacity::create($data);
             return response()->json([ 'success' =>true, 'message' => 'Insertion successful', 'data'=>$model], 200);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }
        
    }

    public function update($data, $id)
    {
        try {  
        $model = ContainerTypeCapacity::where('id',$id)->first();
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
        $model = ContainerTypeCapacity::findOrFail($id);
        $model->delete();
        return $model;
    }
}
