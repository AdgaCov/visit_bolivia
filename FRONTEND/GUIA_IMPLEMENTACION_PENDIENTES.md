# 🚀 Guía de Implementación - Tareas Pendientes Críticas

## 1. Integración de Google Analytics 4

### Paso 1: Obtener ID de Google Analytics
1. Crear cuenta en [Google Analytics](https://analytics.google.com/)
2. Crear propiedad para el sitio web
3. Obtener el Measurement ID (formato: `G-XXXXXXXXXX`)

### Paso 2: Instalar en el proyecto

**Archivo:** `public/index.html`

Agregar antes del cierre de `</head>`:

```html
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

### Paso 3: Tracking de eventos en Vue Router

**Archivo:** `src/router/index.ts`

Agregar después de crear el router:

```typescript
router.afterEach((to, from) => {
  if (typeof window !== 'undefined' && window.gtag) {
    window.gtag('config', 'G-XXXXXXXXXX', {
      page_path: to.path,
      page_title: to.name || to.path
    })
  }
})
```

### Paso 4: Tracking de eventos personalizados

Crear composable: `src/composables/useAnalytics.ts`

```typescript
export function useAnalytics() {
  const trackEvent = (eventName: string, params?: Record<string, any>) => {
    if (typeof window !== 'undefined' && window.gtag) {
      window.gtag('event', eventName, params)
    }
  }

  return { trackEvent }
}
```

---

## 2. Meta Tags Dinámicos por Página

### Opción A: Usar vue-meta (Recomendado)

**Instalación:**
```bash
npm install @vueuse/head
```

**Archivo:** `src/main.ts`
```typescript
import { createHead } from '@vueuse/head'

const head = createHead()
app.use(head)
```

**Uso en componentes:**
```vue
<script setup>
import { useHead } from '@vueuse/head'

useHead({
  title: 'Lugar: Salar de Uyuni - Bolivia Turismo',
  meta: [
    { name: 'description', content: 'Descubre el Salar de Uyuni...' },
    { property: 'og:title', content: 'Salar de Uyuni' },
    { property: 'og:image', content: 'https://...' }
  ]
})
</script>
```

### Opción B: Plugin personalizado

Crear: `src/plugins/meta.ts`

```typescript
import { App } from 'vue'

export default {
  install(app: App) {
    app.config.globalProperties.$setMeta = (meta: Record<string, string>) => {
      // Actualizar meta tags dinámicamente
      document.title = meta.title || document.title
      // ... actualizar otros meta tags
    }
  }
}
```

---

## 3. Structured Data (Schema.org)

### Crear composable: `src/composables/useStructuredData.ts`

```typescript
export function useStructuredData() {
  const addStructuredData = (data: object) => {
    const script = document.createElement('script')
    script.type = 'application/ld+json'
    script.text = JSON.stringify(data)
    document.head.appendChild(script)
  }

  const placeSchema = (place: any) => {
    return {
      '@context': 'https://schema.org',
      '@type': 'TouristAttraction',
      name: place.nombre,
      description: place.descripcion,
      image: place.imagenes?.[0],
      address: {
        '@type': 'PostalAddress',
        addressCountry: 'BO'
      },
      geo: {
        '@type': 'GeoCoordinates',
        latitude: place.coordenadas?.[0],
        longitude: place.coordenadas?.[1]
      }
    }
  }

  return { addStructuredData, placeSchema }
}
```

**Uso en página de lugar:**
```vue
<script setup>
import { useStructuredData } from '@/composables/useStructuredData'
import { onMounted } from 'vue'

const { addStructuredData, placeSchema } = useStructuredData()

onMounted(() => {
  if (place.value) {
    addStructuredData(placeSchema(place.value))
  }
})
</script>
```

---

## 4. Sitemap.xml Dinámico

### Crear script: `scripts/generate-sitemap.js`

```javascript
const fs = require('fs')
const path = require('path')

const BASE_URL = 'https://bolivia-turismo.com'

const routes = [
  '/',
  '/lugares',
  '/eventos',
  '/gastronomia',
  // ... agregar todas las rutas
]

const sitemap = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${routes.map(route => `  <url>
    <loc>${BASE_URL}${route}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>`).join('\n')}
</urlset>`

fs.writeFileSync(path.join(__dirname, '../public/sitemap.xml'), sitemap)
console.log('✅ Sitemap generado')
```

**Agregar script en package.json:**
```json
"scripts": {
  "sitemap": "node scripts/generate-sitemap.js"
}
```

---

## 5. Robots.txt Optimizado

**Archivo:** `public/robots.txt`

```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/

Sitemap: https://bolivia-turismo.com/sitemap.xml
```

---

## 6. Configuración de Google Search Console

### Pasos:
1. Ir a [Google Search Console](https://search.google.com/search-console)
2. Agregar propiedad (URL del sitio)
3. Verificar propiedad (método recomendado: archivo HTML)
4. Enviar sitemap: `https://bolivia-turismo.com/sitemap.xml`

### Verificación HTML

Crear archivo: `public/google-site-verification.html`

```html
<!-- Contenido proporcionado por Google Search Console -->
```

---

## 7. Manual de Usuario - Estructura Sugerida

### Estructura del Manual:

```
MANUAL_USUARIO/
├── 01_Introduccion.md
├── 02_Acceso_Al_Sistema.md
├── 03_Gestion_Lugares.md
├── 04_Gestion_Eventos.md
├── 05_Gestion_Gastronomia.md
├── 06_Gestion_Alojamientos.md
├── 07_Gestion_Contenido.md
├── 08_Gestion_Usuarios.md
└── 09_Solucion_Problemas.md
```

### Template para cada sección:

```markdown
# Título del Módulo

## Objetivo
[Descripción del módulo]

## Acceso
1. Iniciar sesión en `/admin/login`
2. Navegar a [ruta del módulo]

## Funcionalidades

### Crear nuevo [elemento]
1. Clic en botón "Nuevo"
2. Completar formulario
3. Guardar

### Editar [elemento]
1. Buscar en la lista
2. Clic en "Editar"
3. Modificar campos
4. Guardar

## Capturas de pantalla
[Incluir imágenes]

## Problemas comunes
- **Problema:** [descripción]
  - **Solución:** [pasos a seguir]
```

---

## 8. Configuración SSL/HTTPS

### Para Apache (.htaccess)

**Archivo:** `public/.htaccess`

Agregar al inicio:

```apache
# Forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Headers de seguridad
<IfModule mod_headers.c>
  Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
  Header set X-Content-Type-Options "nosniff"
  Header set X-Frame-Options "SAMEORIGIN"
  Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

### Para Nginx

```nginx
server {
    listen 80;
    server_name bolivia-turismo.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name bolivia-turismo.com;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    # ... resto de configuración
}
```

---

## 9. Sistema de Monitoreo

### Opción A: UptimeRobot (Gratis)

1. Crear cuenta en [UptimeRobot](https://uptimerobot.com/)
2. Agregar monitor HTTP(S)
3. Configurar alertas por email/SMS

### Opción B: Pingdom

Similar a UptimeRobot, con más features en plan de pago.

### Opción C: Dashboard propio

Crear endpoint en backend que retorne estado del sistema.

---

## 10. Reportes Automáticos

### Crear servicio: `src/services/reports.service.ts`

```typescript
export class ReportsService {
  async generateTrafficReport(startDate: string, endDate: string) {
    // Integrar con Google Analytics API
    // Generar reporte de tráfico
  }

  async generateSEOReport() {
    // Integrar con Search Console API
    // Generar reporte de posicionamiento
  }

  async exportToPDF(data: any) {
    // Usar jsPDF (ya instalado)
    // Generar PDF del reporte
  }
}
```

### Crear componente: `src/features/admin/Reports/AdminReports.vue`

```vue
<template>
  <div>
    <h1>Reportes</h1>
    <button @click="generateReport">Generar Reporte Mensual</button>
    <button @click="exportPDF">Exportar a PDF</button>
  </div>
</template>
```

---

## 📋 Checklist de Implementación

- [ ] Google Analytics 4 integrado
- [ ] Meta tags dinámicos implementados
- [ ] Structured Data agregado
- [ ] Sitemap.xml generado
- [ ] Robots.txt configurado
- [ ] Google Search Console verificado
- [ ] Manual de usuario creado
- [ ] SSL/HTTPS configurado
- [ ] Sistema de monitoreo activo
- [ ] Reportes automáticos funcionando

---

**Nota:** Estas son guías básicas. Adaptar según necesidades específicas del proyecto.

