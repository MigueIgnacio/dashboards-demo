
import { defineConfig } from 'vite'
import vue2 from '@vitejs/plugin-vue2'

export default defineConfig({
  plugins: [vue2()],
  server: {
  port: 5173,
  proxy: {
    '/api': { target: 'http://127.0.0.1:8000', changeOrigin: true },

    // 🔽 específicos de tu backend:
    '/reports/dashboard-excess-alert-speed': {
      target: 'http://127.0.0.1:8000',
      changeOrigin: true
    },
    '/fleet':   { target: 'http://127.0.0.1:8000', changeOrigin: true },
    '/company': { target: 'http://127.0.0.1:8000', changeOrigin: true },
  },
},

  resolve: { alias: { '@': '/src' } }
})
