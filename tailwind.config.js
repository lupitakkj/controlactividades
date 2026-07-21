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
                darkbg: '#020617',
                navbar: '#0F172A',
                card: '#1E293B',
                card2: '#334155',
                primary: '#2563EB',
                primaryHover: '#1D4ED8',
                textMain: '#F8FAFC',
                textSecondary: '#94A3B8',
            },
        },
    },

    plugins: [forms],
};