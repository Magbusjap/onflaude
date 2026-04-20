/**
 * OnFlaude Default Theme — Bootstrap
 *
 * Configures axios as the default HTTP client and sets AJAX headers
 * required by Laravel (CSRF, X-Requested-With).
 *
 * @module bootstrap
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
