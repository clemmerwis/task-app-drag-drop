<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Projects\{
    ProjectStoreRequest,
    ProjectUpdateRequest
};

class ProjectController extends Controller
{
    public function index()
    {
        $firstProject = Project::first();

        if ($firstProject) {
            return redirect()->route('projects.tasks.index', $firstProject);
        }

        // If no projects exist, show the projects index page
        return view('projects.index');
    }

    public function store(ProjectStoreRequest $request)
    {
        try {
            $project = Project::create($request->validated());

            return redirect()
                ->route('projects.tasks.index', $project)
                ->with('success', 'Project created successfully!');
        }
        catch (\Exception $e) {
            Log::error('Failed to create project', [
                'name' => $request->name,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Unable to create project. Please try again.');
        }
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        try {
            $project->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'project' => [
                    'name' => $project->name,
                    'slug' => $project->slug
                ]
            ]);
        }
        catch (\Exception $e) {
            Log::error('Failed to update project', [
                'project_id' => $project->id,
                'name' => $request->name,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to update project. Please try again.'
            ], 500);
        }
    }

    public function destroy(Project $project)
    {
        try {
            $projectName = $project->name;

            // Tasks will be automatically deleted due to cascade delete in migration
            $project->delete();

            // If this was the last project, redirect to projects.index
            $nextProject = Project::first();
            if (!$nextProject) {
                return redirect()
                    ->route('projects.index')
                    ->with('success', "Project '$projectName' was deleted successfully.");
            }

            return redirect()
                ->route('projects.tasks.index', $nextProject)
                ->with('success', "Project '$projectName' was deleted successfully.");

        }
        catch (\Exception $e) {
            Log::error('Failed to delete project', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Unable to delete project. Please try again.');
        }
    }
}
