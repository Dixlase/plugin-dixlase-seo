import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'plugins/DixlaseSEO/resources/src/js/app.js',
                'plugins/DixlaseSEO/resources/src/css/style.scss',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'plugins/DixlaseSEO/resources/assets',
        rollupOptions: {
            output: {
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js',
                assetFileNames: 'css/[name][extname]',
            },
        },
    },
});