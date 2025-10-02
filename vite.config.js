import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['jquery', 'chart.js', 'sweetalert2'],
                    fontawesome: ['@fortawesome/fontawesome-free'],
                    mask: ['jquery-mask-plugin']
                }
            }
        },
        chunkSizeWarningLimit: 1000
    }
});
