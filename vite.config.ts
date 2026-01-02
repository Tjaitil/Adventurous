/// <reference types="vitest" />
/// <reference types="vite/client" />
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';
import ui from '@nuxt/ui/vite';

export default defineConfig({
  test: {
    environment: 'jsdom',
    setupFiles: ['./resources/js/mocks/setup.ts'],
    exclude: ['**/node_modules/**', '**/vendor/**'],
    globals: true,
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
    tailwindcss(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    ui({
      inertia: true,
      colorMode: false,
      ui: {
        button: {
          slots: {
            base: 'rounded px-3 py-2.5 pixelated-corners-sm',
          },
          variants: {
            size: {
              md: {
                base: 'px-3 py-2.5',
              },
            },
            color: {
              primary: 'border-amber-900',
            },
          },
        },
        card: {
          slots: {
            root: `card relative overflow-visible after:border-primary-700 after:pointer-events-none after:absolute after:top-0 after:left-0 after:h-full after:w-full after:border-4 after:border-solid
              before:pixelated-corners before:pointer-events-none before:absolute before:top-[-8px] before:left-[-8px] before:h-[calc(100%+16px)] before:w-[calc(100%+16px)] before:border-8 before:border-solid before:content-['']
              `,
          },
          variants: {
            variant: {
              outline: {
                root: 'bg-orange-100 before:border-stone-400',
                body: 'p-4 sm:p-4',
              },
              soft: {
                root: 'before:border-stone-400 bg-[#a89a8a]',
                body: 'p-4 sm:p-4',
              },
              /**
               * This uses a opacity background. Adjust when needed a variant
               */
              subtle: {
                root: 'before:border-stone-400',
                body: 'p-4 sm:p-4',
              },
              solid: {
                root: 'before:border-stone-800 after:border-primary-800 bg-stone-700',
                body: 'p-4 sm:p-4',
              },
            },
          },
        },
        colors: {
          primary: 'primary',
          secondary: 'secondary',
          tertiary: 'indigo',
          emerald: 'emerald',
          dark: 'indigo',
          neutral: 'stone',
          success: 'emerald',
        },
      },
      theme: {
        colors: [
          'primary',
          'secondary',
          'tertiary',
          'info',
          'success',
          'warning',
          'error',
          'emerald',
          'neutral',
        ],
      },
    }),
    laravel({
      input: [
        'resources/js/ui/inertia.app.ts',
        'resources/js/app.js',
        'resources/js/ui/main.ts',
        'resources/js/clientScripts/inventory.ts',
        'resources/js/backgroundScroller.ts',
        'resources/css/app.css',
      ],
      refresh: false,
    }),
  ],
  build: {
    target: 'ES2022',
  },
});
