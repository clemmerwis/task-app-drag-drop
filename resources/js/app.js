import './bootstrap'; // Initialize custom JavaScript dependencies (e.g., Axios)
import * as bootstrap from 'bootstrap';// Import Bootstrap's JavaScript components
import { initializeTaskManagement } from './task-manager';
import { initializeProjectModals } from './project-modals';

// Make Bootstrap's components globally available
window.bootstrap = bootstrap;

// Initialize task management only on task list pages
if (document.getElementById('taskList')) {
    initializeTaskManagement();
}

// Initialize project modals if they exist on the page
if (document.getElementById('newProjectModal') || document.getElementById('deleteProjectModal')) {
    initializeProjectModals();
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle project-name edit modal loading state
    const editProjectModal = document.getElementById('editProjectModal');
    const editProjectForm = editProjectModal?.querySelector('form');

    if (editProjectForm && editProjectModal) {
        const modal = new bootstrap.Modal(editProjectModal);
        const submitButton = editProjectForm.querySelector('button[type="submit"]');
        const closeButtons = editProjectModal.querySelectorAll('.btn-close, .btn-secondary');

        editProjectForm.addEventListener('submit', function() {
            if (submitButton) {
                // Only submit if there's a value
                const editInput = editProjectForm.querySelector('input[name="edit_project_name"]'); // Update name here
                if (!editInput.value.trim()) {
                    return;
                }

                // Disable the buttons
                submitButton.disabled = true;
                closeButtons.forEach(button => button.disabled = true);

                // Prevent modal from being dismissed
                editProjectModal.dataset.bsBackdrop = 'static';
                modal._config.backdrop = 'static';
                modal._config.keyboard = false;

                // Update button state
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Updating Project...
                `;

                // Re-enable everything if form submission fails
                setTimeout(() => {
                    if (!editProjectForm.classList.contains('submitted')) {
                        submitButton.disabled = false;
                        closeButtons.forEach(button => button.disabled = false);
                        editProjectModal.dataset.bsBackdrop = 'true';
                        modal._config.backdrop = true;
                        modal._config.keyboard = true;
                        submitButton.textContent = 'Save Changes';
                    }
                }, 5000);
            }

            editProjectForm.classList.add('submitted');
        });
    }

    // Handle initial project creation form loading state
    const initialProjectForm = document.getElementById('initial-project-form');
    if (initialProjectForm) {
        initialProjectForm.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Creating Project...
                `;

                // Re-enable if submission fails
                setTimeout(() => {
                    if (!initialProjectForm.classList.contains('submitted')) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Create Project';
                    }
                }, 5000);
            }

            initialProjectForm.classList.add('submitted');
        });
    }
});
