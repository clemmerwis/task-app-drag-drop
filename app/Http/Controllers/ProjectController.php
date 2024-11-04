<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_name' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    'not_regex:/^[\s]*$/',
                    // Ensure project name is unique
                    // Parameters:
                    // - projects: table name
                    // - name: column to check uniqueness
                    'unique:projects,name',
                ],
            ], [
                'project_name.required' => 'The project name is required.',
                'project_name.max' => 'The project name cannot be longer than 100 characters.',
                'project_name.min' => 'The project name cannot be empty.',
                'project_name.not_regex' => 'The project name cannot contain only whitespace.',
                'project_name.unique' => 'The project name has already been taken.',
            ]);

            $project = Project::create([
                'name' => $validated['project_name']
            ]);

            return redirect()
                ->route('projects.tasks.index', $project)
                ->with('success', 'Project created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', $e->errors()['project_name'][0]);
        }

    }

    public function update(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'edit_project_name' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    'not_regex:/^[\s]*$/',
                    // Ensure project name is unique (excluding current project)
                    // Parameters:
                    // - projects: table name
                    // - name: column to check uniqueness
                    // - $project->id: ignore this ID when checking uniqueness
                    'unique:projects,name,' . $project->id,
                ],
            ], [
                'edit_project_name.required' => 'The project name is required.',
                'edit_project_name.max' => 'The project name cannot be longer than 100 characters.',
                'edit_project_name.min' => 'The project name cannot be empty.',
                'edit_project_name.not_regex' => 'The project name cannot contain only whitespace.',
                'edit_project_name.unique' => 'The project name has already been taken.',
            ]);

            $project->update([
                'name' => $validated['edit_project_name']
            ]);

            return redirect()
                ->route('projects.tasks.index', $project)
                ->with('success', 'Project updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', $e->errors()['edit_project_name'][0]);
        }
    }

    public function destroy(Project $project)
    {
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
}
