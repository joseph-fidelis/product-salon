import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from "@tailwindcss/vite";
import { resolve } from 'node:path';
import { defineConfig } from 'vite';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),

        AutoImport({
            imports: [
                'vue',
                'vue-router',
                '@vueuse/core',
                {
                    '@inertiajs/vue3': [
                        'router',
                        'useForm',
                        'usePage',
                    ],
                },
            ],
            dts: 'resources/js/auto-imports.d.ts',
            dirs: ['resources/js/composables'],
        }),
        
        Components({
            dirs: ['resources/js/components', 'resources/js/components/ui'],
            extensions: ['vue'],
            deep: true,
            dts: 'resources/js/components.d.ts',
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
});
