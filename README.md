# 🚀 Dashboards Demo – Frontend & Backend

Este proyecto es un **demo de dashboards interactivos** desarrollado como parte de mi experiencia en **desarrollo Front-End y Full-Stack**.  
Muestra cómo integrar un **frontend en Vue.js 2** con un **backend en Laravel 10**, incluyendo gráficos, mapas y reportes dinámicos.

---

## ✨ Características principales

- **Frontend (Vue.js + Vite)**  
  - Dashboards con **gráficos interactivos** (ECharts).  
  - Mapas dinámicos con **Leaflet** (mapa de puntos y heatmap).  
  - Tablas con filtros, paginación y exportación a Excel.  
  - Componentes reutilizables (Top 5, DonutChart, LineChart, etc.).

- **Backend (Laravel)**  
  - Endpoints REST que devuelven datos mockeados en JSON.  
  - Simulación de endpoints típicos de reportes:  
    - Top 5 Vehículos / Conductores.  
    - Alertas por fechas.  
    - Detalle de eventos.  
    - Datos geoespaciales para mapas.

- **Visualización profesional**  
  - Estilos consistentes.  
  - Tooltips en gráficos y mapas.  
  - Leyendas dinámicas para interpretar la información.

---

## 🛠️ Tecnologías utilizadas

- **Frontend**  
  - [Vue.js 2] 
  - [Vite]
  - [Axios] 
  - [ECharts]  
  - [Leaflet] 
  - CSS (custom + utilidades)

- **Backend**  
  - [Laravel 10] 
  - PHP 8.x  
  - Endpoints mockeados con `Route::get` para simular datos reales.

## ⚙️ Instalación y ejecución

### 🔹 Backend (Laravel)
1. Ir a la carpeta `laravel-backend`:
   ```bash
   cd laravel-backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan serve
   
### 🔹 Front (Vue2)
2.- Ir a la carpeta 'front'
    cd frontend
    npm install
    npm run dev

    El frontend correrá en: http://127.0.0.1:5173

    👨‍💻 Autor
    Miguel Contreras
    👔 Ingeniero en Informática.
    💻 Experiencia en Vue.js, Laravel, PHP, JavaScript y metodologías ágiles.
    🚀 Enfoque en desarrollo Front-End, interfaces limpias y funcionales.



