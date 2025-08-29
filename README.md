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
   ```
2. Instalar dependencias:
   ```bash
   composer install
   ```
3. Copiar archivo de entorno:
   ```bash
   cp .env.example .env
   ```
4. Generar la clave de la aplicación:
   ```bash
   php artisan key:generate
   ```
5. Iniciar el servidor de desarrollo:
   ```bash
   php artisan serve
   ```
   El backend correrá en: **http://127.0.0.1:8000**

---

### 🔹 Frontend (Vue 2 + Vite)
1. Ir a la carpeta `frontend`:
   ```bash
   cd frontend
   ```
2. Instalar dependencias:
   ```bash
   npm install
   ```
3. Iniciar servidor de desarrollo:
   ```bash
   npm run dev
   ```
   El frontend correrá en: **http://127.0.0.1:5173**

---

## 👨‍💻 Autor

**Miguel Contreras**  
- 👔 Ingeniero en Informática.  





