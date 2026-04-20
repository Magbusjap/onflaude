import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

/**
 * Vite config — фронт (активная тема).
 *
 * Input: themes/default/css/app.css + themes/default/js/app.js
 * (на этапе рефакторинга hardcoded на default; после внедрения
 * динамической активной темы будет резолвиться через config('onflaude.theme.active')).
 *
 * Для админки Filament — отдельный конфиг: vite.filament.config.js
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
