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
                               value="{{ $project->name }}"
                               required>
                        <div class="invalid-feedback">
                            Please enter a project name
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveProjectEdit">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>
