/** @type {import('tailwindcss').Config} */
export default {
    content: [
        // Active theme and any future themes
        './themes/**/*.blade.php',
        './themes/**/*.js',
        // System Blade (recovery, welcome, custom Filament views)
        './resources/views/**/*.blade.php',
        // Dynamic content and PHP classes that may emit HTML
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                'onflaude-dark':    '#003893',
                'onflaude-light':   '#0097D7',
                'onflaude-sidebar': '#DCEBF2',
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
}
