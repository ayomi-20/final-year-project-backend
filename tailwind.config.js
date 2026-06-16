import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    content: [
        './vendor/filament/**/*.blade.php',
        './resources/views/**/*.blade.php',
        './app/Filament/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};