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

    theme: {
        extend: {
            fontFamily: {
                sans: ['Raleway', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                primary: {
                    50: '#fff2ed',
                    100: '#ffe2d4',
                    200: '#ffc1a8',
                    300: '#ff9571',
                    400: '#ff5c35',
                    500: '#fe3511',
                    600: '#ef1b07',
                    700: '#c60e08',
                    800: '#9d0f10',
                    900: '#7e1010',
                    950: '#440609',
                },
            },
        },
    },

    plugins: [forms],
};
