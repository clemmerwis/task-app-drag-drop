import './bootstrap'; // Initialize custom JavaScript dependencies (e.g., Axios)
import * as bootstrap from 'bootstrap';// Import Bootstrap's JavaScript components
import { initializeTaskManagement } from './task-manager';

// Make Bootstrap's components globally available
window.bootstrap = bootstrap;

// Initialize task management only on task list pages
if (document.getElementById('taskList')) {
    initializeTaskManagement();
}
