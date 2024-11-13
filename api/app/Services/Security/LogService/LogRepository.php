<?php

namespace App\Services\Security\LogService;

use App\Models\Log;

use Exception;

class LogRepository
{
    public function index($request)
    {


        $activity = $request->query('search');


        if ($activity) {
            $model = Log::where('activity', 'like', '%' . $activity . '%')->latest()->get();

            if ($model->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Records retrieved successfully',
                    'data' => $model
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No records found',
                'data' => []
            ], 404);
        } else {


            /////////
            $model =  Log::latest()->paginate(20);
            if($model) {
                return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
            }
            return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
        }
    }

    public function show($id)
    {
        // dd('reach');
        $model = Log::where('id', $id)->first();
        if($model) {
            return response()->json([ 'success' => true, 'message' => 'Record retrieved successfully', 'data' => $model], 200);
        }
        return response()->json([ 'success' => false, 'message' => 'No record found', 'data' => $model], 404);
    }

    public function store($data)
    {

        try {
            $model =  Log::create($data);
            return response()->json([ 'success' => true, 'message' => 'Insertion successful', 'data' => $model], 200);
        } catch (Exception $e) {
            //Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Insertion error'], 500);
        }

    }


    public function destroy($id)
    {
        $model = Log::findOrFail($id);
        $model->delete();
        return $model;
    }
    public function logEvent($route, $event, $modelId, $model, $activity, $payload = [])
    {
        // Define the log data in an array format
        $logData = [
            'route' => $route,
            'user_id' => auth()->check() ? auth()->user()->id : null,
            'event' => $event,
            'model_id' => $modelId,
            'model' => $model,
            'activity' => $activity,
            'payload' => json_encode($payload),
        ];

        // Use the LogRepository to save the log
        return Log::create($logData);
    }
}
