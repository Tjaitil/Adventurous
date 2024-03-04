import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/clientScripts/inventory.ts',
                'resources/js/backgroundScroller.ts',
                'resources/js/clientScripts/getXp.ts',
                'resources/js/clientScripts/sidebar.ts',
            ],
        }),
    ],
});
