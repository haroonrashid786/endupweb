import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'public/assets/css/bootstrap.min.css',
                'public/assets/css/icons.min.css',
                'public/assets/css/app.min.css'
            ],
            refresh: true,
        }),
    ],
});
