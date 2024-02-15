<?php

namespace App\Services\TaskService;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;



class TaskRepository
{
   public function getAllTask(){

        return Task::latest('created_at')->where('user_id', Auth::id())->paginate(5);

   }
   public function getTaskById($task)
   {

        return $task;

   }

    public function createTask(array $data)
    {
        try {
             return Task::Create($data);

        } catch (QueryException $exception) {

            return response()->json(['message' => 'Insertion failed.'], 500);
        }
    }

    public function updateTask(array $request, $task)
    {
        try{

             $task->update($request);
             return $task;
        }
       catch (QueryException $exception){
        
            return response()->json(['message' => 'Task not found.'], 404);
        }
      
    }

    public function deleteTask(array $request, $task)
    {
        try{

             $task->delete($request);
             return $task;
        }
       catch (QueryException $exception){
        
            return response()->json(['message' => 'Task not found.'], 404);
        }
      
    }

   
}
