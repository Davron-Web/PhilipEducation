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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Единая палитра пользовательской части сайта (см. layouts/app.blade.php,
                // где та же палитра продублирована в конфиге Tailwind CDN — сборки через
                // npm сейчас нет, поэтому оба места нужно держать в синхроне).
                ink: '#1E3A8A',
                brand: '#2563EB',
                sky: '#38BDF8',
                skylight: '#7DD3FC',
                sun: '#FACC15',
            },
        },
    },
    plugins: [forms],
};
