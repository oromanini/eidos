import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            // CORREÇÃO AQUI: Adicionamos o CSS e transformamos em um array
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            ssr: "resources/js/ssr.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    ssr: {
        noExternal: ["vue", "@protonemedia/laravel-splade"]
    },

    server: {
        host: '0.0.0.0',
    },
});
