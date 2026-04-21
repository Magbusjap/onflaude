/**
 * OnFlaude Admin — JavaScript Entry Point
 *
 * Initialises all admin-side JS components on import.
 * Built by vite.filament.config.js into public/build/filament/.
 *
 * @module index
 */

import { init as initSidebar } from './components/sidebar.js';
import { init as initAccordion } from './components/accordion.js';
import { init as initPostSidebar } from './components/post-sidebar.js';

initSidebar();
initAccordion();
initPostSidebar();
