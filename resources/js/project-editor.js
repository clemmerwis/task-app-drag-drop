export function initializeProjectEditing() {
    const editModal = document.getElementById('editProjectModal');
    if (!editModal) return;

    const modal = new bootstrap.Modal(editModal);
    const editInput = document.getElementById('editProjectName');
    const saveButton = document.getElementById('saveProjectEdit');
    const toast = document.getElementById('taskToast');
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 1200
    });

    let isUpdating = false;

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
        saveButton.disabled = isLoading;
        editInput.disabled = isLoading;

        const closeButton = editModal.querySelector('.btn-close');
        const cancelButton = editModal.querySelector('.modal-footer .btn-secondary');
        closeButton.disabled = isLoading;
        cancelButton.disabled = isLoading;

        modal._config.backdrop = isLoading ? 'static' : true;
        modal._config.keyboard = !isLoading;

        if (isLoading) {
            saveButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Saving...
            `;
        } else {
            saveButton.textContent = 'Save Changes';
        }
    }

    saveButton.addEventListener('click', () => {
        if (!editInput.value.trim()) {
            editInput.classList.add('is-invalid');
            return;
        }

        const projectSlug = document.getElementById('taskList').dataset.projectSlug;
        const newName = editInput.value.trim();

        isUpdating = true;
        setLoading(true);

        axios.patch(`/projects/${projectSlug}`, {
            name: newName
        })
        .then(response => {
            modal.hide();

            // Update project name in the dropdown
            const projectSelect = document.querySelector('select[name="switch_to"]');
            const selectedOption = projectSelect.querySelector(`option[value="${projectSlug}"]`);
            selectedOption.textContent = newName;

            // Update URL if slug changed
            if (response.data.project.slug !== projectSlug) {
                window.history.replaceState(
                    {},
                    '',
                    window.location.pathname.replace(projectSlug, response.data.project.slug)
                );

                // Update the taskList data attribute
                document.getElementById('taskList').dataset.projectSlug = response.data.project.slug;

                // Update the dropdown option value
                selectedOption.value = response.data.project.slug;
            }

            showToast(response.data.message || 'Project updated successfully');
        })
        .catch(error => {
            console.error('Failed to update project:', error);

            if (error.response?.data?.errors?.name) {
                editInput.classList.add('is-invalid');
                const feedback = editInput.nextElementSibling;
                feedback.textContent = error.response.data.errors.name[0];
            } else {
                showToast(error.response?.data?.message || 'Failed to update project. Please try again.', true);
            }
        })
        .finally(() => {
            isUpdating = false;
            setLoading(false);
        });
    });

    editModal.addEventListener('hide.bs.modal', () => {
        if (!isUpdating) {
            editInput.classList.remove('is-invalid');
            // Reset to original value
            editInput.value = editInput.defaultValue;
        }
    });

    editInput.addEventListener('input', () => {
        editInput.classList.remove('is-invalid');
    });
}
