import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                'resources/js/app.js',
                'resources/js/backgroundScroller.ts',
                'resources/js/clientScripts/getXp.ts',
                'resources/js/clientScripts/sidebar.ts'],
            refresh: true,
        }),
    ],
});
