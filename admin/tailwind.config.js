import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            // setup primary-secondary color, example: https://flowbite.com/docs/customize/colors
            colors: {
                primary: {
                    '50': '#f0f7ff',
                    '100': '#e0effe',
                    '200': '#badffd',
                    '300': '#7ec6fb',
                    '400': '#39a9f7',
                    '500': '#0f8fe8',
                    '600': '#036fc5',
                    '700': '#0459a0',
                    '800': '#084c84',
                    '900': '#0d406d',
                    '950': '#082849',
                },
                transparent: 'transparent',
                current: 'currentColor',
            },
            boxShadow: {
                'card': '0 2px 20px 0px rgb(0 0 0 / 4%)',
            },
            animation: {
                'slow-bounce': 'bounce 5s linear infinite',
                'ring': 'ring 1s infinite'
            }
        },
    },

    plugins: [forms],
};
