import preset from '../../vendor/filament/filament/tailwind.config.preset'

/**
 * Tailwind-конфиг админки OnFlaude (Filament 3).
 *
 * Отдельный конфиг от themes/default (v3 через PostCSS) — админка
 * строится через собственный Vite-конфиг (vite.filament.config.js)
 * и use preset'а Filament.
 *
 * Content охватывает PHP-классы Filament (Resources/Pages/Widgets),
 * кастомные Blade в resources/admin/views/ и vendor Filament'а.
 */
export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/admin/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
