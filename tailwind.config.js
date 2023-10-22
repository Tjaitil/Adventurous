/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        "./resources/views/templates/*.php",
        "./resources/js/**/*.ts",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: "#FAF5F0",
                    100: "#F4EBE1",
                    200: "#E9D7C3",
                    300: "#DFC3A5",
                    400: "#D4AF87",
                    500: "#C99B69",
                    600: "#B47C41",
                    700: "#875D31",
                    800: "#5A3E20",
                    900: "#2D1F10",
                    950: "#171008",
                },
            },
        },
    },
    plugins: [],
};
