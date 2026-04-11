/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                'onflaude': {
                    'dark':  '#003893',
                    'light': '#0097D7',
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
}
