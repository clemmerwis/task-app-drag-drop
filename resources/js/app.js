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
