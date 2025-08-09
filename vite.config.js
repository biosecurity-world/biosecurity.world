import {defineConfig} from "vite"
import laravel from "laravel-vite-plugin"
import tailwindcss from "@tailwindcss/vite"

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/main.css",
                "resources/css/prose.css",
                "resources/js/map.ts",
                "resources/js/render-testcase.ts",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
})
