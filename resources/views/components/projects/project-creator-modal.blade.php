<div>
    <button type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#newProjectModal">
        New Project
    </button>

    <div class="modal fade" id="newProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="projectName" class="form-label">Project Name</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="projectName"
                                   name="name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
