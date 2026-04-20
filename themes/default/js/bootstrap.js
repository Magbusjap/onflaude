/**
 * OnFlaude Default Theme — Bootstrap
 *
 * Настройка axios как HTTP-клиента по умолчанию и AJAX-заголовков
 * требуемых Laravel (CSRF, X-Requested-With).
 *
 * @module bootstrap
 */

import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
