/**
 * OnFlaude Default Theme — JavaScript Entry Point
 *
 * Инициализация компонентов темы после DOMContentLoaded.
 * Каждый компонент импортируется как именованный init-модуль.
 *
 * @module app
 */

import './bootstrap';
import { init as initAdminBar } from './components/admin-bar.js';

document.addEventListener('DOMContentLoaded', () => {
    initAdminBar();
});
