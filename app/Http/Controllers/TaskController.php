<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks for a given project
     */
    public function index(Project $project, Request $request)
    {
        if ($request->has('switch_to')) {
            return redirect()->route('projects.tasks.index', $request->switch_to);
        }

        // get all tasks for dropdown
        $projects = Project::select('id', 'name')->get();

        // get only the tasks for currently selected project
        $tasks = $project->tasks()
            ->orderBy('priority')
            ->get();

        return view('tasks.index', compact('tasks', 'projects', 'project'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'not_regex:/^[\s]*$/', // Prevent only-whitespace names
                'unique:tasks,name,NULL,id,project_id,' . $project->id, // Ensure unique name within project
            ],
            [
                'name.required' => 'Please enter a task name.',
                'name.max' => 'Task name cannot be longer than 255 characters.',
                'name.min' => 'Task name cannot be empty.',
                'name.not_regex' => 'Task name cannot contain only whitespace.',
            ]
        ]);

        try {
            $project->tasks()->create($validated);
            return redirect()
                ->route('projects.tasks.index', $project)
                ->with('success', 'Task created successfully!');

        }
        catch (\Exception $e) {
            Log::error('Failed to create task', [
                'project_id' => $project->id,
                'name' => $validated['name'],
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to create task. Please try again.');
        }
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'not_regex:/^[\s]*$/', // Prevent only-whitespace names
                'unique:tasks,name,' . $task->id . ',id,project_id,' . $project->id, // Unique within project, excluding current task
            ],
            [
                'name.required' => 'Please enter a task name.',
                'name.max' => 'Task name cannot be longer than 255 characters.',
                'name.min' => 'Task name cannot be empty.',
                'name.not_regex' => 'Task name cannot contain only whitespace.',
            ]
        ]);

        try {
            $task->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);

        }
        catch (\Exception $e) {
            Log::error('Failed to update task', [
                'project_id' => $project->id,
                'task_id' => $task->id,
                'name' => $validated['name'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to update task. Please try again.'
            ], 500);
        }
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();

        return redirect()
            ->route('projects.tasks.index', $project)
            ->with('success', 'Task deleted successfully');
    }

    /**
     * Update task priority order.
     */
    public function updatePriority(Request $request, Project $project, Task $task)
    {
        $validated = $request->validate([
            'priority' => 'required|integer|min:1',
        ]);

        $task->updatePriority($validated['priority']);

        return response()->json(['success' => true]);
    }
}
