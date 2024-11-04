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
            $switchToProject = Project::where('slug', $request->switch_to)->firstOrFail();
            return redirect()->route('projects.tasks.index', $request->switch_to);
        }

        // get all projects for dropdown (including slug)
        $projects = Project::select('id', 'name', 'slug')->get();

        // get only the tasks for currently selected project
        $tasks = $project->tasks()
            ->orderBy('priority')
            ->get();

        return view('tasks.index', compact('tasks', 'projects', 'project'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'min:1',
                    'max:255',
                    'not_regex:/^[\s]*$/', // Prevent only-whitespace names
                    // Ensure task name is unique within the project
                    // Parameters:
                    // - tasks: table name
                    // - name: column to check uniqueness
                    // - NULL: no ID to ignore (creating new task)
                    // - id: primary key column name
                    // - project_id: additional column for scoping uniqueness
                    // - $project->id: value for the project_id scope
                    'unique:tasks,name,NULL,id,project_id,' . $project->id, // Ensure unique name within project
                ],
            ],
            [
                'name.required' => 'Please enter a task name.',
                'name.max' => 'Task name cannot be longer than 255 characters.',
                'name.min' => 'Task name cannot be empty.',
                'name.not_regex' => 'Task name cannot contain only whitespace.',
                'name.unique' => 'A task with this name already exists in this project.',
            ]
        );

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
        try {
            $validated = $request->validate(
                [
                    'name' => [
                        'required',
                        'string',
                        'min:1',
                        'max:255',
                        'not_regex:/^[\s]*$/',
                        // Ensure task name is unique within the project (excluding the current task)
                        // Parameters:
                        // - tasks: table name
                        // - name: column to check uniqueness
                        // - $task->id: ignore this ID when checking uniqueness
                        // - id: primary key column name
                        // - project_id: additional column for scoping uniqueness
                        // - $project->id: value for the project_id scope
                        'unique:tasks,name,' . $task->id . ',id,project_id,' . $project->id,
                    ],
                ],
                [
                    'name.required' => 'Please enter a task name.',
                    'name.max' => 'Task name cannot be longer than 255 characters.',
                    'name.min' => 'Task name cannot be empty.',
                    'name.not_regex' => 'Task name cannot contain only whitespace.',
                    'name.unique' => 'A task with this name already exists in this project.',
                ]
            );

            $task->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->validator->errors()
            ], 422); // Use 422 status code for validation errors
        }
        catch (\Exception $e) {
            Log::error('Failed to update task', [
                'project_id' => $project->id,
                'task_id' => $task->id,
                'name' => $request->name,
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
        try {
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
        }
        catch (\Exception $e) {
            Log::error('Failed to delete task', [
                'project_id' => $project->id,
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to delete task. Please try again.'
            ], 500);
        }
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
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
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
