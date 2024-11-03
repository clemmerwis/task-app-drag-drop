<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:1',
                'max:100',
                'not_regex:/^[\s]*$/', // Prevent only-whitespace names
                'unique:projects',
            ],
            [
                'name.required' => 'Please enter a project name.',
                'name.max' => 'Project name cannot be longer than 100 characters.',
                'name.min' => 'Project name cannot be empty.',
                'name.not_regex' => 'Project name cannot contain only whitespace.',
                'name.unique' => 'A project with this name already exists.',
            ]
        ]);

        try {
            $project = Project::create($validated);
            return redirect()
                ->route('projects.tasks.index', $project)
                ->with('success', 'Project created successfully!');

        }
        catch (\Exception $e) {
            Log::error('Failed to create project', [
                'name' => $validated['name'],
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->with('error', 'Unable to create project. Please try again.');
        }

        return redirect()
            ->route('projects.tasks.index', $project)
            ->with('success', 'Project created successfully!');
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
