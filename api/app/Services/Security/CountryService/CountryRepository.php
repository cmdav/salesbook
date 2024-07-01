<?php

namespace App\Services\Security\CountryService;

use App\Models\Country;

use Exception;

class CountryRepository
{
    public function index()
    {
        return Country::select('id','name')->get();
    }

    public function show($id)
    {
        return Country::select('id','name')->where('id',$id)->with('states:id,country_id,name')->first();
    }

    public function store($data)
    {
        try {
            return Country::create($data);
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
        $model = Country::where('id',$id)->first();
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
            $model = Country::where('id',$id)->first();
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
