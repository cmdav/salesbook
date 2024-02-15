<?php

namespace App\Http\Controllers\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\TaskFormRequest;
use App\Services\TaskService\TaskService;

class TaskController extends Controller
{
    
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;

    }
    public function index()
    { 	
        
            return $this->taskService->getAllTask();
        
    }
    public function show(Task $task)
    { 	
        
            return $this->taskService->getTaskById($task);
        
    }
   
    public function store(TaskFormRequest $request)
    { 	
        
            return $this->taskService->createTask($request->all());
        
    }
    public function update(Request $request, Task $task)
    { 	
           
        return $this->taskService->updateTask($request->all(), $task);
        
    }
    public function destroy(Request $request, Task $task)
    { 	
           return $this->taskService->deleteTask($request->all(), $task);
        
    }
   
}
