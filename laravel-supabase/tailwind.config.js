/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue", // Biarkan jika Anda mungkin menggunakan Vue
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};