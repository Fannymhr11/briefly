import defaultTheme from 'tailwindcss/defaultTheme';

const v = (name) => `rgb(var(--c-${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Semantic tokens: berubah otomatis antara Light & Dark Mode (lihat app.css)
                page: v('page'),
                surface: v('surface'),
                soft: v('soft'),
                line: v('line'),
                ink: v('ink'),
                muted: v('muted'),
                brand: {
                    50: '#EFF5FF',
                    100: '#DDE9FF',
                    200: '#BFD5FF',
                    300: '#93B8FF',
                    400: '#5E94FA',
                    500: '#3B7BF6',
                    600: '#2563EB',
                    700: '#1D4FD1',
                    800: '#1E3FA6',
                    900: '#16295F',
                },
            },
            boxShadow: {
                card: '0 1px 2px rgb(15 27 61 / 0.04), 0 1px 1px rgb(15 27 61 / 0.02)',
                pop: '0 12px 32px -8px rgb(15 27 61 / 0.18), 0 2px 6px rgb(15 27 61 / 0.06)',
            },
        },
    },
    plugins: [],
};
