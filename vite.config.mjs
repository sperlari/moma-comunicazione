import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ command }) => {
    const isBuild = command === 'build';

    return {
        // Build assets must stay portable across environments where the theme
        // folder name can differ (e.g. demo zip uploads or renamed theme dirs).
        // Using a relative base keeps dynamic Vite chunks resolvable from dist/assets.
        base: isBuild ? './' : '/',
        server: {
            port: 3000,
            cors: true,
            origin: 'http://localhost:8000',
        },
        build: {
            manifest: true,
            outDir: 'dist',
            rollupOptions: {
                input: [
                    'resources/js/app.js',
                    'resources/css/app.css',
                    'resources/css/editor-style.css'
                ],
            },
        },
        plugins: [
            tailwindcss(),
        ],
    }
});
