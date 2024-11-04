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
        try {
            $request->validate(
                [
                    'name' => [
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
                ],
                [
                    'name.required' => 'The project name is required.',
                    'name.max' => 'The project name cannot be longer than 100 characters.',
                    'name.min' => 'The project name cannot be empty.',
                    'name.not_regex' => 'The project name cannot contain only whitespace.',
                    'name.unique' => 'The project name has already been taken.',
                ]
            );

            $project = Project::create($request->only('name'));

            return redirect()
                ->route('projects.tasks.index', $project)
                ->with('success', 'Project created successfully!');
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            // Don't use withInput() at all - the modal JS will handle repopulating
            return back()->with('error', $e->validator->errors()->first());
        }
        catch (\Exception $e) {
            Log::error('Failed to create project', [
                'name' => $request->name,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Unable to create project. Please try again.');
        }
    }

    public function update(Request $request, Project $project)
    {
        try {
            $validated = $request->validate(
                [
                    'name' => [
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
                ],
                [
                    'name.required' => 'The project name is required.',
                    'name.max' => 'The project name cannot be longer than 100 characters.',
                    'name.min' => 'The project name cannot be empty.',
                    'name.not_regex' => 'The project name cannot contain only whitespace.',
                    'name.unique' => 'The project name has already been taken.',
                ]
            );

            $project->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'project' => [
                    'name' => $project->name,
                    'slug' => $project->slug
                ]
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->validator->errors()
            ], 422);
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
