export function initializeTaskDeletion() {
    const deleteModal = document.getElementById('deleteTaskModal');
    if (!deleteModal) return;

    const modal = new bootstrap.Modal(deleteModal);
    const deleteNameSpan = document.getElementById('deleteTaskName');
    const confirmButton = document.getElementById('confirmTaskDelete');
    const toast = document.getElementById('taskToast');
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 1200
    });

    let activeTaskElement = null;
    let isDeleting = false;

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
        confirmButton.disabled = isLoading;

        // Disable/enable close buttons
        const closeButton = deleteModal.querySelector('.btn-close');
        const cancelButton = deleteModal.querySelector('.modal-footer .btn-secondary');
        closeButton.disabled = isLoading;
        cancelButton.disabled = isLoading;

        // Prevent/allow modal from closing by clicking outside
        modal._config.backdrop = isLoading ? 'static' : true;
        modal._config.keyboard = !isLoading;

        // Add/remove loading spinner
        if (isLoading) {
            confirmButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Deleting...
            `;
        } else {
            confirmButton.textContent = 'Delete Task';
        }
    }

    // Add click handlers to all delete buttons
    document.querySelectorAll('.delete-task-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            const taskItem = e.target.closest('.list-group-item');
            const taskName = taskItem.querySelector('.task-name').textContent.trim();

            activeTaskElement = taskItem;
            deleteNameSpan.textContent = taskName;
            modal.show();
        });
    });

    // Handle delete confirmation
    confirmButton.addEventListener('click', () => {
        if (!activeTaskElement) return;

        const taskId = activeTaskElement.dataset.taskId;
        // const projectId = document.getElementById('taskList').dataset.projectId; erase me
        const projectSlug = document.getElementById('taskList').dataset.projectSlug; // Changed from projectId
        const taskElement = activeTaskElement;

        isDeleting = true;
        setLoading(true);

        axios.delete(`/projects/${projectSlug}/tasks/${taskId}`)
            .then(response => {
                modal.hide();
                taskElement.remove();
                showToast(response.data.message || 'Task deleted successfully');

                // Show "No tasks" message if this was the last task
                const taskList = document.getElementById('taskList');
                if (!taskList.querySelector('.list-group-item')) {
                    taskList.innerHTML = `
                        <div class="alert alert-info">
                            No tasks yet. Add your first task above!
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Failed to delete task:', error);
                showToast(error.response?.data?.message || 'Failed to delete task. Please try again.', true);
            })
            .finally(() => {
                isDeleting = false;
                setLoading(false);
                activeTaskElement = null;
            });
    });

    // Reset state when modal is hidden
    deleteModal.addEventListener('hide.bs.modal', () => {
        if (!isDeleting) {
            activeTaskElement = null;
        }
    });
}
