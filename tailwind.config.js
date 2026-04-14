/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.css',
    ],
    theme: {
        extend: {
            colors: {
                // 'onflaude': {
                //     'dark':  '#003893',
                //     'light': '#0097D7',
                // },
                'onflaude-dark': '#003893',
                'onflaude-light': '#0097D7',
                'onflaude-sidebar': '#DCEBF2',
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
}
