import preset from '../../vendor/filament/filament/tailwind.config.preset'

/**
 * Tailwind config — OnFlaude admin (Filament 3).
 *
 * Separate from themes/default's config (v3 via PostCSS) because the
 * admin is built through its own Vite config (vite.filament.config.js)
 * and uses the official Filament preset.
 *
 * Content covers Filament PHP classes (Resources/Pages/Widgets),
 * custom admin Blade in resources/admin/views/ and Filament vendor.
 */
export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/admin/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
