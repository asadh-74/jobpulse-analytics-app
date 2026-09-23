/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                ink: {
                    DEFAULT: '#0B1220',
                    panel: '#121B2E',
                    line: '#26314A',
                },
                signal: {
                    amber: '#E5A94B',
                    teal: '#4FD1C5',
                    rose: '#E06B6B',
                },
                paper: {
                    DEFAULT: '#E7EAF2',
                    muted: '#8B93A7',
                },
            },
            fontFamily: {
                display: ['"Space Grotesk"', 'sans-serif'],
                body: ['"Inter"', 'sans-serif'],
                mono: ['"IBM Plex Mono"', 'monospace'],
            },
        },
    },
    plugins: [],
};
