<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUserRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class TaskController extends Controller
{
    use AuthorizesRequests;
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
        $this->authorize('viewAny', Task::class);
        $tasks = Task::all();

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $taskRequest)
    {
             
        $this->authorize('create', [Task::class, $taskRequest->validated()]);
    
        $task = Task::create($taskRequest->all());

        return response()->json($task, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
        $this->authorize('view', $task);
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $TaskRequest, Task $task)
    {
        //
        $this->authorize('update', [Task::class, $task]);
        $validated = $TaskRequest->validated();
        $task->update($validated);

        return TaskResource::make($task, 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(null, 204);
    }
    public function getTasksInPeriod( TaskUserRequest $taskUserRequest)
    {
        
        $this->authorize('getTasksInPeriod', [Task::class, $taskUserRequest->validated()]);

    $tasks = $this->taskService->getUserTasksInPeriod(
        $taskUserRequest->user_id,
        $taskUserRequest->getStartDate(),
        $taskUserRequest->getEndDate()
    );

    return TaskResource::collection($tasks);
    }
}
