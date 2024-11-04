@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h1>Task Manager</h1>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <div>
                <x-projects.project-selector-dropdown :projects="$projects" :project="$project" />
                <div class="d-flex justify-content-between mt-2">
                    <div class="btn-group">
                        <x-projects.project-creator-modal />
                        <x-projects.project-edit-modal :project="$project" />
                    </div>
                    <x-projects.project-delete-modal :project="$project" />
                </div>
            </div>
        </div>
    </div>

    <x-tasks.task-creator-form :project="$project" />
    <x-tasks.task-list-group :tasks="$tasks" :project="$project" />
@endsection
