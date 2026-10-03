import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/*@type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                laundry: {
                    teal: '#0F766E',
                    cyan: '#06B6D4',
                    sky: '#E0F7FA',
                    dark: '#1E293B',
                    warning: '#FBBF24',
                    success: '#16A34A',
                },
            },
        },
    },

    plugins: [forms],
};