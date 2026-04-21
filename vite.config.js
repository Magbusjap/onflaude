import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

/**
 * Vite config — public-site frontend (active theme).
 *
 * Input: themes/default/css/app.css + themes/default/js/app.js
 * Hardcoded to `default` for now; once the dynamic active-theme
 * resolver lands, the input will be derived from
 * config('onflaude.theme.active').
 *
 * Filament admin uses a separate config: vite.filament.config.js.
 */
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'themes/default/css/app.css',
                'themes/default/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        transformer: 'postcss',
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
