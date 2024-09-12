import {defineConfig} from "vite"
import laravel from "laravel-vite-plugin"

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/main.css",
                "resources/css/prose.css",
                "resources/js/map.ts"
            ],
            refresh: true,
        }),
    ],
})
