import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    optimizeDeps: {
        include: ['datatables.net', 'datatables.net-dt'],
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            css: {
                additionalData: `
                    @import "node_modules/datatables.net-dt/css/jquery.dataTables.min.css";
                `
            }
        }
    },
    server: {
        hmr: {
            overlay: false,  // Disable the HMR error overlay
        }
    }
});
