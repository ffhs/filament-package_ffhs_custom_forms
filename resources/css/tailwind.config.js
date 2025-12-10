/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        '../../views/filament/components/**/*.php',
        '../../../src/Filament/**/*.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
