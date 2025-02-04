export function initializeProjectModals() {
    // Project Create Modal
    const newProjectModal = document.getElementById('newProjectModal');
    const newProjectForm = newProjectModal?.querySelector('form');

    if (newProjectForm) {
        const createButton = newProjectForm.querySelector('button[type="submit"]');
        const projectNameInput = newProjectForm.querySelector('input[name="project_name"]');
        const closeButtons = newProjectModal.querySelectorAll('.btn-close, .btn-secondary');
        const modal = new bootstrap.Modal(newProjectModal);

        function setCreateLoading(isLoading) {
            createButton.disabled = isLoading;
            closeButtons.forEach(button => button.disabled = isLoading);

            // Prevent/allow modal from closing by clicking outside
            modal._config.backdrop = isLoading ? 'static' : true;
            modal._config.keyboard = !isLoading;

            // Add/remove loading spinner
            if (isLoading) {
                createButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Creating...
                `;
            } else {
                createButton.textContent = 'Create Project';
            }
        }

        // Handle form submission
        newProjectForm.addEventListener('submit', (event) => {
            const name = projectNameInput.value.trim();

            // Prevent submit and show errors if name is empty
            if (!name) {
                event.preventDefault();
                projectNameInput.classList.add('is-invalid');
                return;
            }

            setCreateLoading(true)
        });

        // Reset loading state if validation failed (page reloaded)
        if (projectNameInput.classList.contains('is-invalid')) {
            setCreateLoading(false);
        }

        // Clear validation state when user starts typing
        projectNameInput.addEventListener('input', () => {
            projectNameInput.classList.remove('is-invalid');
        });

        // Reset form when modal is hidden
        newProjectModal.addEventListener('hidden.bs.modal', () => {
            // Only reset the form if there's no error message
            if (!document.querySelector('.alert.alert-danger')) {
                setCreateLoading(false);
                projectNameInput.classList.remove('is-invalid');
                newProjectForm.reset();
            }
        });
    }

    // Project Delete Modal
    const deleteProjectModal = document.getElementById('deleteProjectModal');
    const deleteProjectForm = deleteProjectModal?.querySelector('form');

    if (deleteProjectForm) {
        const deleteButton = deleteProjectForm.querySelector('button[type="submit"]');
        const closeButtons = deleteProjectModal.querySelectorAll('.btn-close, .btn-secondary');
        const modal = new bootstrap.Modal(deleteProjectModal);

        function setDeleteLoading(isLoading) {
            deleteButton.disabled = isLoading;
            closeButtons.forEach(button => button.disabled = isLoading);

            // Prevent/allow modal from closing by clicking outside
            modal._config.backdrop = isLoading ? 'static' : true;
            modal._config.keyboard = !isLoading;

            // Add/remove loading spinner
            if (isLoading) {
                deleteButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Deleting...
                `;
            } else {
                deleteButton.textContent = 'Delete Project';
            }
        }

        deleteProjectForm.addEventListener('submit', () => {
            setDeleteLoading(true);
        });
    }
}
