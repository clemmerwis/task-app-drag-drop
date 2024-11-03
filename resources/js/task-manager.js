import Sortable from 'sortablejs';
import { initializeTaskEditing } from './task-editor';


export function initializeTaskManagement() {
    const taskList = document.getElementById('taskList');

    if (taskList) {
        let oldIndex;

        new Sortable(taskList, {
            animation: 150, // animation speed in ms
            handle: '.drag-handle', // user can only drag by clicking on the handle "☰"
            ghostClass: 'sortable-ghost', // shadow outline that shows where item will be dropped
            chosenClass: 'sortable-chosen', // grays out the original position of the item until drag/drop completes
            dragClass: 'sortable-drag', // the item being dragged is a little transparent so you can see what's under it

            // Store the original index when drag starts
            onStart: function(evt) {
                oldIndex = evt.oldIndex;
            },

            // Called when sorting is stopped/completed
            onEnd: function(evt) {
                const projectId = taskList.dataset.projectId;
                const taskId = evt.item.dataset.taskId;
                const newPriority = evt.newIndex + 1; // Convert to 1-based index

                axios.patch(`/projects/${projectId}/tasks/${taskId}/priority`, {
                    priority: newPriority
                })
                .catch(error => {
                    console.error('Failed to update task priority:', error);

                    // Revert the item to its original position
                    const item = evt.item;
                    const parent = item.parentNode;

                    if (oldIndex < parent.children.length) {
                        parent.insertBefore(item, parent.children[oldIndex]);
                    } else {
                        parent.appendChild(item);
                    }

                    alert('Failed to update task order. The list order has been restored.');
                });
            }
        });

        initializeTaskEditing();
    }
}
