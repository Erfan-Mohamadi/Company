/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                teal: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
                cyan: colors.cyan,      // or copy from tailwindcss/colors
                emerald: colors.emerald,
                violet: colors.violet,
                sky: colors.sky,
                lime: colors.lime,
                amber: colors.amber,
                orange: colors.orange,
                purple: colors.purple,
                slate: colors.slate,
                indigo: colors.indigo,
                // add more if needed
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
