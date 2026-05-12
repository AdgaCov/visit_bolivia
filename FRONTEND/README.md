# 🦜 Bolivia Turismo - Frontend

Plataforma web moderna para promover el turismo en Bolivia. Desarrollada con Vue 3, TypeScript y una arquitectura limpia y escalable.

## 🚀 Características

- **Interfaz Moderna**: Diseño responsive y atractivo inspirado en las mejores plataformas de turismo
- **Gestión de Contenido**: Panel administrativo completo para gestionar lugares, eventos, gastronomía y alojamientos
- **Mapas Interactivos**: Integración con Leaflet para visualización de ubicaciones
- **Búsqueda Avanzada**: Sistema de búsqueda y filtrado por categorías, regiones y tipos
- **Vista 360°**: Experiencia inmersiva con Pannellum
- **Información del Clima**: Integración con OpenWeatherMap
- **Multiidioma**: Preparado para múltiples idiomas (configuración en desarrollo)

## 📋 Requisitos Previos

- Node.js 16.x o superior
- npm 8.x o superior
- Backend API funcionando (ver configuración en `.env`)

## 🛠️ Instalación

1. **Clonar el repositorio**
   ```bash
   git clone [tu-repositorio]
   cd FRONTEND
   ```

2. **Instalar dependencias**
   ```bash
   npm install
   ```

3. **Configurar variables de entorno**
   ```bash
   # Copia el archivo de ejemplo y ajusta las URLs según tu configuración
   cp .env.example .env
   ```
   
   Edita `.env` con tus valores:
   ```env
   VUE_APP_API_URL=http://localhost:8000
   VUE_APP_API_URL_IMG=http://localhost:8000/api_conoce_bolivia/public
   ```

4. **Ejecutar en desarrollo**
   ```bash
   npm run serve
   ```

   La aplicación estará disponible en `http://localhost:8080`

## 📜 Scripts Disponibles

```bash
# Desarrollo con hot-reload
npm run serve

# Compilar para producción
npm run build

# Ejecutar linter
npm run lint

# Verificar tipos TypeScript
npm run type-check

# Generar deck de presentación para negocios
npm run deck:business
```

## 📁 Estructura del Proyecto

```
FRONTEND/
├── public/                 # Archivos estáticos
│   ├── icons/             # Iconos y logos
│   ├── images/            # Imágenes del sitio
│   └── videos/            # Videos promocionales
├── src/
│   ├── assets/            # Recursos (estilos, datos mock)
│   ├── components/        # Componentes reutilizables
│   │   ├── accommodation/ # Componentes de alojamientos
│   │   ├── cultura/       # Componentes de cultura
│   │   ├── events/        # Componentes de eventos
│   │   ├── gastronomy/    # Componentes de gastronomía
│   │   └── home/          # Componentes del home
│   ├── composables/       # Composables Vue 3
│   ├── constants/         # Constantes y configuraciones
│   ├── features/          # Features/páginas completas
│   │   ├── admin/         # Panel administrativo
│   │   ├── places/        # Gestión de lugares
│   │   └── public/        # Páginas públicas
│   ├── layouts/           # Layouts (MainLayout, AdminLayout)
│   ├── plugins/           # Plugins (Bootstrap, Leaflet, FontAwesome)
│   ├── router/            # Configuración de rutas
│   ├── services/          # Servicios API
│   │   └── admin/         # Servicios del admin
│   ├── store/             # Pinia stores
│   ├── types/             # Definiciones TypeScript
│   └── utils/             # Utilidades
├── .env.example           # Ejemplo de configuración
├── .gitignore            # Archivos ignorados por git
├── package.json          # Dependencias y scripts
├── tsconfig.json         # Configuración TypeScript
└── vue.config.js         # Configuración Vue CLI
```

## 🎨 Tecnologías Utilizadas

### Core
- **Vue 3** - Framework frontend
- **TypeScript** - Tipado estático
- **Vue Router** - Enrutamiento
- **Pinia** - Gestión de estado

### UI/UX
- **Bootstrap 5** - Framework CSS
- **FontAwesome** - Iconos
- **Leaflet** - Mapas interactivos
- **Pannellum** - Visor 360°

### Utilidades
- **Axios** - Cliente HTTP
- **Sharp** - Procesamiento de imágenes
- **pptxgenjs** - Generación de presentaciones

## 🔐 Autenticación

El panel administrativo requiere autenticación. Las rutas están protegidas por guards que verifican el token almacenado en localStorage.

- **Login**: `/admin/login`
- **Dashboard**: `/admin`

## 🌐 Configuración de APIs

### API Backend
Configura la URL base de tu API en `.env`:
```env
VUE_APP_API_URL=http://localhost:8000
```

### Imágenes
Configura la URL base para las imágenes:
```env
VUE_APP_API_URL_IMG=http://localhost:8000/api_conoce_bolivia/public
```

## 📱 Responsive Design

La aplicación está optimizada para:
- 📱 Móviles (320px+)
- 📱 Tablets (768px+)
- 💻 Desktop (1024px+)
- 🖥️ Large Desktop (1440px+)

## 🐛 Debugging

Para depurar la aplicación:

1. **Console logs**: Revisa la consola del navegador
2. **Network**: Verifica las peticiones API en DevTools
3. **Vue DevTools**: Instala la extensión para inspeccionar componentes

## 📝 Próximas Mejoras

- [ ] Sistema de favoritos implementado
- [ ] Integración real con API de clima
- [ ] Optimización de imágenes lazy loading
- [ ] Testing unitario y E2E
- [ ] PWA completa con offline support
- [ ] Internacionalización (i18n)
- [ ] Sistema de reviews y ratings

## 🤝 Contribución

1. Fork el proyecto
2. Crea tu feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto es privado y protegido por derechos de autor.

## 📧 Contacto

Para más información, contacta al equipo de desarrollo.

---

Desarrollado con ❤️ para promover el turismo en Bolivia 🇧🇴
