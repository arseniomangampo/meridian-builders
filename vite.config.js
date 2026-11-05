
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig({
  plugins: [tailwindcss()],

  // WordPress serves the compiled assets.
  base: '',

  // Theme source directory.
  root: __dirname,

  build: {
    outDir: resolve(__dirname, 'assets/dist'),
    emptyOutDir: true,
    manifest: true,

    // WordPress PHP templates are our HTML source.
    cssCodeSplit: true,

    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/js/main.js'),
        styles: resolve(__dirname, 'src/css/main.css'),
      },
    },
  },
});
