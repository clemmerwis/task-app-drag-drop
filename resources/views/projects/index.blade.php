@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0">Welcome to Task Manager</h1>
                </div>
                <div class="card-body">
                    <p class="card-text">To get started, create your first project:</p>

                    <form action="{{ route('projects.store') }}" method="POST" id="initial-project-form">
                        @csrf
                        <div class="mb-3">
                            <label for="projectName" class="form-label">Project Name</label>
                            <input type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="projectName"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Create Project</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
