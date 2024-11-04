<div>
    <button type="button"
            class="btn btn-secondary"
            data-bs-toggle="modal"
            data-bs-target="#editProjectModal">
        Edit
    </button>

    <div class="modal fade" id="editProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editProjectName" class="form-label">Project Name</label>
                            <input type="text"
                                   class="form-control"
                                   id="editProjectName"
                                   name="edit_project_name"
                                   value="{{ old('edit_project_name', $project->name) }}"
                                   required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
