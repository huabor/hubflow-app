import forms from '@tailwindcss/forms';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'selector',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    safelist: [
        {
            pattern: /size-*/,
        },
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Raleway', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                primary: {
                    50: '#f3f6fc',
                    100: '#e6edf8',
                    200: '#c7d8f0',
                    300: '#96b8e3',
                    400: '#5e93d2',
                    500: '#3976be',
                    600: '#295ca0',
                    700: '#224a82',
                    800: '#20406c',
                    900: '#1d3354',
                    950: '#15233c',
                },
            },
        },
    },

    plugins: [forms],
};
