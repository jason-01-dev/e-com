import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/admin.css', 'resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // 🚨 SECTION CRUCIALE MODIFIÉE POUR DOCKER
   server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    hmr: {
        host: 'host.docker.internal',
    },
},

    // ----------------------------------------------------
});