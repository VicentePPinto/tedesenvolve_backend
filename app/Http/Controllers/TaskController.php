<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $taskRequest)
    {
        //
        $task = Task::create($taskRequest->all());

        return response()->json($task, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $TaskRequest, Task $task)
    {
        //
        $this->authorize('update', $task);
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
}
