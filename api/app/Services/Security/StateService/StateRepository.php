<?php

namespace App\Services\Security\StateService;

use App\Models\State;

use Exception;

class StateRepository
{
    public function index($id)
    {
        return State::select('id', 'name')
        ->where('country_id', $id)
        ->orderBy('name')
        ->get();
    }

    public function show($id)
    {
        return State::where('id',$id)->first();
    }

    public function store($data)
    {
        try {
            return State::create($data);
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => 'false',
                'message' => 'Insertion error'
            ], 500);
        }
        
    }

    public function update($data, $id)
    {
        try {  
        $model = State::where('id',$id)->first();
            if($model){
                $model->update($data);
            }
            return $model;
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => 'false',
                'message' => 'Insertion error'
            ], 500);
        }
    }   

    public function destroy($id)
    {
        try { 
            $model = State::where('id',$id)->first();
            $model->delete();
            return $model;
        } catch (Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => 'false',
                'message' => 'Insertion error'
            ], 500);
        }
    }
}
