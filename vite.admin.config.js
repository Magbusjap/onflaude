import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/admin/css/theme.css',
            ],
            buildDirectory: 'build/filament',
            refresh: false,
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