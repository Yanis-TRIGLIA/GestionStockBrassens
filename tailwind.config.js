/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            backgroundImage: {
                'login-fond': "url('http://127.0.0.1:8000/ressources/image/login/fond.jpg')",
            },
        },
    },
    plugins: [],
};
