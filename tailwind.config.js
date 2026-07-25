import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [

        // Meta/tab colors
    'bg-green-50', 'bg-green-100', 'text-green-600', 'text-green-700',
    'bg-blue-50', 'bg-blue-100', 'text-blue-600', 'text-blue-700',
    'bg-pink-50', 'bg-pink-100', 'text-pink-600', 'text-pink-700',
    'bg-purple-50', 'bg-purple-100', 'text-purple-600', 'text-purple-700',
    'bg-amber-50', 'bg-amber-100', 'text-amber-600', 'text-amber-700',
    'bg-red-50', 'bg-red-100', 'text-red-600', 'text-red-700',

        'bg-green-50', 'text-green-700',
        'bg-blue-50', 'text-blue-700',
        'bg-orange-50', 'text-orange-700',
        'bg-yellow-50', 'text-yellow-700',
        'bg-amber-50', 'text-amber-700',
        'bg-red-50', 'text-red-700',
        'bg-gray-50', 'text-gray-700',
        'text-blue-600',
        'text-pink-600',

        // Grade colors
        'bg-green-50', 'bg-green-700', 'text-green-600', 'text-green-700',
        'bg-blue-50', 'text-blue-700',
        'bg-orange-50', 'text-orange-700',
        'bg-yellow-50', 'text-yellow-700',
        'bg-amber-50', 'text-amber-700',
        'bg-red-50', 'text-red-600', 'text-red-700',
        'bg-gray-50', 'text-gray-700',

        // Background colors
        'bg-blue-50', 'bg-blue-100',
        'bg-green-50', 'bg-green-100',
        'bg-purple-50', 'bg-purple-100',
        'bg-amber-50', 'bg-amber-100',
        'bg-yellow-50', 'bg-yellow-100',
        'bg-red-50', 'bg-red-100',
        'bg-indigo-50', 'bg-indigo-100',

        // Text colors
        'text-blue-500', 'text-blue-600', 'text-blue-700',
        'text-green-500', 'text-green-600', 'text-green-700',
        'text-purple-500', 'text-purple-600', 'text-purple-700',
        'text-amber-500', 'text-amber-600', 'text-amber-700',
        'text-yellow-500', 'text-yellow-600', 'text-yellow-700',
        'text-red-500', 'text-red-600', 'text-red-700',
        'text-indigo-500', 'text-indigo-600', 'text-indigo-700',

        // Border colors
        'border-blue-100', 'border-blue-200',
        'border-green-100', 'border-green-200',
        'border-purple-100', 'border-purple-200',
        'border-amber-100', 'border-amber-200',
        'border-yellow-100', 'border-yellow-200',
        'border-red-100', 'border-red-200',
        'border-indigo-100', 'border-indigo-200',

        // Hover border colors
        'hover:border-blue-200', 'hover:border-blue-300',
        'hover:border-green-200', 'hover:border-green-300',
        'hover:border-purple-200', 'hover:border-purple-300',
        'hover:border-amber-200', 'hover:border-amber-300',
        'hover:border-yellow-200', 'hover:border-yellow-300',
        'hover:border-red-200', 'hover:border-red-300',
        'hover:border-indigo-200', 'hover:border-indigo-300',

        // Hover background colors
        'hover:bg-blue-50', 'hover:bg-blue-100',
        'hover:bg-green-50', 'hover:bg-green-100',
        'hover:bg-purple-50', 'hover:bg-purple-100',
        'hover:bg-amber-50', 'hover:bg-amber-100',
        'hover:bg-yellow-50', 'hover:bg-yellow-100',
        'hover:bg-red-50', 'hover:bg-red-100',
        'hover:bg-indigo-50', 'hover:bg-indigo-100',

        // Hover text colors
        'hover:text-blue-700', 'group-hover:text-blue-700',
        'hover:text-green-700', 'group-hover:text-green-700',
        'hover:text-purple-700', 'group-hover:text-purple-700',
        'hover:text-amber-700', 'group-hover:text-amber-700',
        'hover:text-yellow-700', 'group-hover:text-yellow-700',
        'hover:text-red-700', 'group-hover:text-red-700',
        'hover:text-indigo-700', 'group-hover:text-indigo-700',

        // Shadow colors
        'hover:shadow-blue-500/10',
        'hover:shadow-green-500/10',
        'hover:shadow-purple-500/10',
        'hover:shadow-amber-500/10',
        'hover:shadow-yellow-500/10',
        'hover:shadow-red-500/10',
        'hover:shadow-indigo-500/10',

        // Group hover bg
        'group-hover:bg-blue-50', 'group-hover:bg-blue-100',
        'group-hover:bg-green-50', 'group-hover:bg-green-100',
        'group-hover:bg-purple-50', 'group-hover:bg-purple-100',
        'group-hover:bg-amber-50', 'group-hover:bg-amber-100',
        'group-hover:bg-yellow-50', 'group-hover:bg-yellow-100',
        'group-hover:bg-red-50', 'group-hover:bg-red-100',
        'group-hover:bg-indigo-50', 'group-hover:bg-indigo-100',

        // Gradient colors used in metric cards
        'from-blue-400', 'to-blue-600',
        'from-green-400', 'to-green-600',
        'from-purple-400', 'to-purple-600',
        'from-amber-400', 'to-amber-600',
        'from-yellow-400', 'to-yellow-600',
        'from-red-400', 'to-red-600',
        'from-indigo-400', 'to-indigo-600',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};