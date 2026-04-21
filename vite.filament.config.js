import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

/**
 * Vite config — OnFlaude admin (Filament 3).
 *
 * Inputs:
 *   - resources/admin/css/theme.css  -> ITCSS layered admin styles
 *   - resources/admin/js/index.js    -> admin JS components
 *
 * Output: public/build/filament/
 *
 * Kept separate from vite.config.js (frontend theme) because Filament 3
 * needs its own buildDirectory and asset resolver via viteTheme().
 */
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/admin/css/theme.css',
                'resources/admin/js/index.js',
            ],
            buildDirectory: 'build/filament',
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
