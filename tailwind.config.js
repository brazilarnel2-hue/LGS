import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
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
                // Tinugma sa blue-teal palette ng welcome page
                // (#075985 / #0284c7 / #0ea5e9) para magkatugma ang buong site.
                laundry: {
                    teal: '#0284c7',
                    cyan: '#38bdf8',
                    sky: '#e0f2fe',
                    dark: '#075985',
                    warning: '#FBBF24',
                    success: '#16A34A',
                },
            },
        },
    },

    plugins: [forms],
};