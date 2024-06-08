import {defineConfig} from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import vuePlugin from "@vitejs/plugin-vue";
import { dirname, resolve } from 'path';
import { fileURLToPath } from 'url';
import vuetify from "vite-plugin-vuetify";



const basicPlaygroundDir = dirname(fileURLToPath(import.meta.url));
const sharedDir = resolve(basicPlaygroundDir, './assets/theme')

/* if you're using React */
// import react from '@vitejs/plugin-react';

export default defineConfig({
    base: '/build/',
    plugins: [
        /* react(), // if you're using React */
        vuePlugin(),
        symfonyPlugin(),
        vuetify({
            autoImport: true,
            icons: {
                defaultSet: 'mdi',
            }
        })
    ],
    build: {
        outDir: 'public/build',
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
    server: {
        // origin: 'http://localhost:5173',
        fs: {
            allow: [
                '.',
                sharedDir
            ]
        },
        watch: {
            ignored: ['**/.idea/**', '**/tests/**', '**/var/**', '**/vendor/**'],
        }
    },

    resolve: {
        alias: {
            '~': resolve(basicPlaygroundDir, 'assets'),
            '~theme': sharedDir
        }
    },


});
