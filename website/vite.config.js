import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import {sentryVitePlugin} from "@sentry/vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
    ],
});
