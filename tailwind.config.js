/** @type {import('tailwindcss').Config} */
export default {
    content: [
        // Active theme + любые будущие темы
        './themes/**/*.blade.php',
        './themes/**/*.js',
        // Server-side Blade (recovery, welcome, filament custom)
        './resources/views/**/*.blade.php',
        // Динамический контент и PHP-классы которые могут выдавать HTML
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
