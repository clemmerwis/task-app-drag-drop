import axios from 'axios';
window.axios = axios;

// Laravel will auto includes CSRF protection for AJAX requests
// when the 'X-Requested-With' header is set.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
