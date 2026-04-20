import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

/**
 * Vite-конфиг админки OnFlaude (Filament 3).
 *
 * Input: resources/admin/css/theme.css -> public/build/filament/
 * Tailwind v4 pipeline (через @tailwindcss/vite автоматически,
 * если директива @config загружает обычный JS-конфиг).
 *
 * Отдельно от vite.config.js (фронт) потому что Filament 3
 * строится по своему пути buildDirectory.
 */
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/admin/css/theme.css'],
            buildDirectory: 'build/filament',
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
