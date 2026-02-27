import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Frontend (public-facing)
                'resources/css/frontend.css',
                'resources/js/frontend.js',
                
                // Admin (backend/dashboard)
                'resources/css/admin.css',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
