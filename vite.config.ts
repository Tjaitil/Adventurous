/// <reference types="vitest" />
/// <reference types="vite/client" />
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    test: {
        environment: 'jsdom',
        setupFiles: ['./resources/js/mocks/setup.ts'],
        coverage: {
            exclude: ['vendor/**'],
            reporter: ['text', 'json', 'html'],
        },
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, './resources/js'),
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
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/ui/web/index.ts',
                'resources/js/ui/main.ts',
                'resources/js/clientScripts/inventory.ts',
                'resources/js/backgroundScroller.ts',
                'resources/js/clientScripts/sidebar.ts',
            ],
            refresh: false,
        }),
    ],
    build: {
        target: 'ES2022',
    },
});
