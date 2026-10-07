<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()
            ->tasks()
            ->with('category');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->latest()->get();

        return TaskResource::collection($tasks);
    }
    public function store(TaskRequest $request)
    {
        $task = $request->user()->tasks()->create(
            $request->validated()
        );

        $task->load('category');

        return (new TaskResource($task))
            ->additional([
                'message' => 'Task created successfully'
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $id)
    {
        $task = $request->user()
            ->tasks()
            ->with('category')
            ->findOrFail($id);

        return new TaskResource($task);
    }

    public function update(TaskRequest $request, string $id)
    {
        $task = $request->user()
            ->tasks()
            ->findOrFail($id);

        $task->update($request->validated());

        $task->load('category');

        return (new TaskResource($task))
            ->additional([
                'message' => 'Task updated successfully'
            ]);
    }

    public function destroy(Request $request, string $id)
    {
        $task = $request->user()
            ->tasks()
            ->findOrFail($id);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed',
        ]);

        $task = $request->user()
            ->tasks()
            ->findOrFail($id);

        $task->update([
            'status' => $request->status,
        ]);

        return new TaskResource($task);
    }
}
