const colors = require("tailwindcss/colors")
const defaultConfig = require("tailwindcss/defaultConfig")

/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php", "./resources/**/*.ts"],
    theme: {
        extend: {
            colors: {
                technical: colors.amber[700],
                governance: colors.blue[600],
            },
            fontFamily: {
                sans: ["Inter", ...defaultConfig.theme.fontFamily.sans],
                display: ["Gilroy", ...defaultConfig.theme.fontFamily.sans],
            },
        },
    },
    plugins: [require("@tailwindcss/forms")],
}
