<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Tasks\{
    TaskStoreRequest,
    TaskUpdateRequest
};

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks for a given project
     */
    public function index(Project $project, Request $request)
    {
        if ($request->has('switch_to')) {
            $switchToProject = Project::where('slug', $request->switch_to)->firstOrFail();
            return redirect()->route('projects.tasks.index', $request->switch_to);
        }

        // Get all projects for dropdown (including slug)
        $projects = Project::select('id', 'name', 'slug')->get();

        // Get only the tasks for currently selected project
        $tasks = $project->tasks()
            ->orderBy('priority')
            ->get();

        return view('tasks.index', compact('tasks', 'projects', 'project'));
    }

    public function store(TaskStoreRequest $request, Project $project)
    {
        $validated = $request->validated();

        $project->tasks()->create($validated);
        return redirect()
            ->route('projects.tasks.index', $project)
            ->with('success', 'Task created successfully!');
    }

    public function update(TaskUpdateRequest $request, Project $project, Task $task)
    {
        $task->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully'
        ]);
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Update task priority order within its project.
     * Called when tasks are reordered via drag and drop.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePriority(Request $request, Project $project, Task $task)
    {
        // Ensure the task actually belongs to the project.
        // GUI should ensure this anyway but this is extra security to prevent mistaken programtic attempts.
        if ($task->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'This task does not belong to the specified project.'
            ], 403);
        }

        try {
            $validated = $request->validate(
                [
                    'priority' => 'required|integer|min:1',
                ],
                [
                    'priority.required' => 'Priority is required.',
                    'priority.integer' => 'Priority must be a number.',
                    'priority.min' => 'Priority must be at least 1.',
                ]
            );

            $task->updatePriority($validated['priority']);

            return response()->json([
                'success' => true,
                'message' => 'Task priority updated successfully'
            ]);
        }
        catch (\Exception $e) {
            Log::error('Failed to update task priority', [
                'project_id' => $project->id,
                'task_id' => $task->id,
                'priority' => $request->priority,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to update task priority. Please try again.'
            ], 500);
        }
    }
}
