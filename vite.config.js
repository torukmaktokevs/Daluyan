import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js' , 'resources/css/tenant/style.css',
                'resources/js/tenant/script.js', 'resources/css/tenant-dashboard.css',
                'resources/js/tenant-dashboard.js'],
            refresh: true,
        }),
    ],
});
