export function initializeTaskEditing() {
    const editModal = document.getElementById('editTaskModal');
    if (!editModal) return;

    const modal = new bootstrap.Modal(editModal);
    const editInput = document.getElementById('editTaskName');
    const saveButton = document.getElementById('saveTaskEdit');
    const toast = document.getElementById('taskToast');
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 1200
    });

    let activeTaskElement = null;
    let isSaving = false;

    function showToast(message, isError = false) {
        const titleEl = document.getElementById('toastTitle');
        const messageEl = document.getElementById('toastMessage');

        toast.classList.remove('bg-danger', 'text-white');
        titleEl.classList.remove('text-white');

        if (isError) {
            toast.classList.add('bg-danger', 'text-white');
            titleEl.classList.add('text-white');
        }

        messageEl.textContent = message;
        bsToast.show();
    }

    function setLoading(isLoading) {
        // Disable/enable all form elements
        saveButton.disabled = isLoading;
        editInput.disabled = isLoading;

        // Disable/enable close buttons - more specific selectors
        const closeButton = editModal.querySelector('.btn-close');
        const cancelButton = editModal.querySelector('.modal-footer .btn-secondary');
        closeButton.disabled = isLoading;
        cancelButton.disabled = isLoading;

        // Prevent/allow modal from closing by clicking outside
        modal._config.backdrop = isLoading ? 'static' : true;
        modal._config.keyboard = !isLoading;

        // Add/remove loading spinner
        if (isLoading) {
            saveButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Saving...
            `;
        } else {
            saveButton.textContent = 'Save Changes';
        }
    }

    // Add click handlers to all edit buttons
    document.querySelectorAll('.edit-task-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            const taskItem = e.target.closest('.list-group-item');
            const taskName = taskItem.querySelector('.task-name').textContent.trim();

            activeTaskElement = taskItem;
            editInput.value = taskName;
            modal.show();
        });
    });

    // Handle save
    saveButton.addEventListener('click', () => {
        if (!activeTaskElement || !editInput.value.trim()) {
            editInput.classList.add('is-invalid');
            return;
        }

        const taskId = activeTaskElement.dataset.taskId;
        // const projectId = document.getElementById('taskList').dataset.projectId; erase me
        const projectSlug = document.getElementById('taskList').dataset.projectSlug; // Changed from projectId

        const newName = editInput.value.trim();

        isSaving = true;
        const taskElement = activeTaskElement;

        // Enable loading state
        setLoading(true);

        axios.patch(`/projects/${projectSlug}/tasks/${taskId}`, {
            name: newName
        })
        .then(response => {
            modal.hide();
            taskElement.querySelector('.task-name').textContent = newName;
            showToast(response.data.message || 'Task updated successfully');
            activeTaskElement = null;
        })
        .catch(error => {
            console.error('Failed to update task:', error);

            if (error.response?.data?.errors?.name) {
                editInput.classList.add('is-invalid');
                const feedback = editInput.nextElementSibling;
                feedback.textContent = error.response.data.errors.name[0];
            } else {
                showToast(error.response?.data?.message || 'Failed to update task. Please try again.', true);
            }
        })
        .finally(() => {
            isSaving = false;
            setLoading(false);
        });
    });

    editModal.addEventListener('hide.bs.modal', () => {
        if (!isSaving) {
            activeTaskElement = null;
            editInput.classList.remove('is-invalid');
        }
    });

    editInput.addEventListener('input', () => {
        editInput.classList.remove('is-invalid');
    });
}
