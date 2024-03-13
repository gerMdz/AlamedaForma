import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import vuePlugin from "@vitejs/plugin-vue";

/* if you're using React */
// import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        /* react(), // if you're using React */
        vuePlugin(),
        symfonyPlugin(),
    ],
    build: {
        assetsInlineLimit: 512,
        manifest: true,
        rollupOptions: {
            input: {
                app: "./assets/app.js"
            },
            output: {
                manualChunks: {
                    vue: ['vue']
                }
            }
        }
    },
});
