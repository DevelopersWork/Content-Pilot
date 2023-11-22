const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    important: '#wpbody-content',
    content: [
        "./src/dashboard/**/*.{html,js,ts,jsx,tsx}"
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                background: 'var(--vajrawp__color-background)',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}