<template>
  <div class="map-wrapper">
    <!-- Contenedor del mapa con ID único -->
    <div 
      :id="uniqueMapId" 
      class="map-container"
      :style="{ height: mapHeight, width: '100%' }"
    ></div>

    <!-- Indicador de carga fuera del contenedor que Leaflet manipula -->
    <div v-if="!mapInitialized" class="map-loading-overlay">
      <div class="loading-content">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando mapa...</span>
        </div>
        <p class="mt-2 text-muted">Inicializando mapa interactivo...</p>
      </div>
    </div>
    
    <!-- Botón de reinicio si hay error -->
    <div v-if="mapError" class="map-error-overlay">
      <div class="error-content">
        <i class="fas fa-exclamation-triangle text-warning mb-3"></i>
        <h6>Error al cargar el mapa</h6>
        <p class="text-muted">Hubo un problema al inicializar el mapa interactivo</p>
        <button @click="retryMap" class="btn btn-primary btn-sm">
          <i class="fas fa-redo me-2"></i>Reintentar
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { patchLeafletForVueLifecycle } from '@/utils/leafletSafety'

export default {
  name: 'InteractiveMap',
  emits: ['place-clicked', 'place-centered'],
  props: {
    lugares: {
      type: Array,
      default: () => []
    },
    lugarActivo: {
      type: Object,
      default: null
    },
    center: {
      type: Array,
      default: () => [-16.5, -68.15]
    },
    zoom: {
      type: Number,
      default: 6
    },
    height: {
      type: String,
      default: '400px'
    },
    enableMarkerPopups: {
      type: Boolean,
      default: false
    },
    openPopupOnCenter: {
      type: Boolean,
      default: false
    },
    showRoutes: {
      type: Boolean,
      default: false
    },
    selectedTransport: {
      type: String,
      default: 'car'
    }
  },
  data() {
    return {
      map: null,
      markers: [],
      routes: [],
      markerLayer: null,
      activeMarker: null,
      isZooming: false,
      uniqueMapId: `interactive-map-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
      mapInitialized: false,
      mapError: false,
      leafletLoaded: false,
      L: null,
      retryCount: 0,
      maxRetries: 3,
      pendingActivePlace: null,
      isProgrammaticMove: false,
      isDestroyed: false,
      scheduledTimers: []
    }
  },
  computed: {
    mapHeight() {
      return this.height
    }
  },
  async mounted() {
    this.isDestroyed = false
    // Esperar múltiples ciclos para asegurar que el DOM esté listo
    await this.$nextTick()
    if (this.isDestroyed) return
    await new Promise(resolve => setTimeout(resolve, 100))
    await this.$nextTick()
    
    // Inicializar el mapa de manera segura
    await this.initializeMapSafely()
  },
  beforeUnmount() {
    this.isDestroyed = true
    this.clearScheduledTimers()
    this.destroyMap()
  },
  methods: {
    schedule(callback, delay = 0) {
      const timer = setTimeout(() => {
        this.scheduledTimers = this.scheduledTimers.filter(id => id !== timer)
        if (!this.isDestroyed) callback()
      }, delay)
      this.scheduledTimers.push(timer)
      return timer
    },

    clearScheduledTimers() {
      this.scheduledTimers.forEach(timer => clearTimeout(timer))
      this.scheduledTimers = []
    },

    isMapUsable() {
      return !this.isDestroyed && this.map && this.mapInitialized && this.L
    },

    closeMapOverlays() {
      try { this.map && this.map.closePopup && this.map.closePopup() } catch (e) { /* noop */ }
      try { this.map && this.map.closeTooltip && this.map.closeTooltip() } catch (e) { /* noop */ }
    },

    async initializeMapSafely() {
      try {
        if (this.isDestroyed) return
        // Cargar Leaflet dinámicamente
        await this.loadLeaflet()
        
        // Esperar un poco más para asegurar que todo esté listo
        await new Promise(resolve => setTimeout(resolve, 200))
        if (this.isDestroyed) return
        
        // Verificar que el elemento del mapa existe y tiene dimensiones
        if (!this.ensureMapElement()) {
          return
        }
        
        // Crear el mapa
        this.createMap()
        
      } catch (error) {
        console.error('Error in initializeMapSafely:', error)
        this.handleMapError()
      }
    },
    
    async loadLeaflet() {
      try {
        // Importar Leaflet de manera dinámica
        const leafletModule = await import('leaflet')
        this.L = leafletModule.default || leafletModule
        patchLeafletForVueLifecycle(this.L)
        this.leafletLoaded = true
        
        // Configurar iconos por defecto
        this.setupDefaultIcons()
        
      } catch (error) {
        console.error('Error loading Leaflet:', error)
        throw error
      }
    },
    
    setupDefaultIcons() {
      if (!this.L) return
      
      try {
        // Eliminar configuración de iconos por defecto problemática
        delete this.L.Icon.Default.prototype._getIconUrl
        
        // Configurar iconos desde CDN externo
        this.L.Icon.Default.mergeOptions({
          iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
          iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
          shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        })
      } catch (error) {
        console.error('Error setting up icons:', error)
      }
    },
    
    ensureMapElement() {
      const mapElement = document.getElementById(this.uniqueMapId)
      if (!mapElement) {
        console.error('Map element not found:', this.uniqueMapId)
        return false
      }
      
      // Verificar dimensiones
      if (mapElement.offsetWidth === 0 || mapElement.offsetHeight === 0) {
        console.warn('Map element has no dimensions, waiting...')
        this.schedule(() => this.initializeMapSafely(), 100)
        return false
      }
      
      return true
    },
    
    createMap() {
      if (this.isDestroyed) return
      if (!this.L || !this.leafletLoaded) {
        console.error('Leaflet not ready')
        return
      }
      
      try {
        // Crear mapa con configuración mínima y estable
        this.map = this.L.map(this.uniqueMapId, {
          center: this.center,
          zoom: this.zoom,
          zoomControl: true,
          attributionControl: true,
          // Configuraciones para evitar errores
          preferCanvas: false,
          zoomSnap: 1,
          zoomDelta: 1,
          wheelPxPerZoomLevel: 60,
          // Interacciones del usuario
          dragging: true,
          scrollWheelZoom: true,
          touchZoom: true,
          doubleClickZoom: true,
          boxZoom: true,
          keyboard: true,
          // Deshabilitar animaciones problemáticas
          animate: false,
          zoomAnimation: false,
          fadeAnimation: false,
          markerZoomAnimation: false
        })
        
        // Agregar capa de tiles de manera segura
        this.addTileLayer()
        
        // Invalidar tamaño tras crear para evitar cálculos con contenedor sin tamaño
        this.schedule(() => {
          try { this.map && this.map.invalidateSize(false) } catch (e) { /* noop */ }
        }, 200)

        // Esperar a que el mapa esté listo antes de agregar capas y marcadores
        this.map.whenReady(() => {
          if (this.isDestroyed || !this.map) return
          // Marcar como inicializado
          this.mapInitialized = true
          this.mapError = false

          // Habilitar arrastre, zoom con rueda, tactil, doble click y teclado.
          try {
            this.map.dragging.enable()
            this.map.scrollWheelZoom.enable()
            this.map.touchZoom.enable()
            this.map.doubleClickZoom.enable()
            this.map.boxZoom.enable()
            this.map.keyboard.enable()
          } catch (e) { /* noop */ }

          // Crear capa para agrupar marcadores y facilitar limpieza segura
          try { this.markerLayer = this.L.layerGroup().addTo(this.map) } catch(e) { this.markerLayer = null }

          // Escuchar zoom para evitar operaciones durante animación
          try {
            this.map.on('zoomstart', () => { this.isZooming = true })
            this.map.on('movestart', () => { this.isZooming = true })
            const handleMovementEnd = () => {
              this.isZooming = false
              const queuedPlace = this.pendingActivePlace
              this.pendingActivePlace = null
              this.isProgrammaticMove = false
              if (this.isDestroyed || !this.mapInitialized) return
              if (queuedPlace) {
                this.centerOnPlace(queuedPlace)
              }
            }
            this.map.on('zoomend', handleMovementEnd)
            this.map.on('moveend', handleMovementEnd)
          } catch (e) { /* noop */ }

          // Agregar marcadores y rutas de forma segura
          this.addMarkers()
          if (this.showRoutes) {
            this.addRoutes()
          }
          if (this.lugarActivo) {
            this.centerOnPlace(this.lugarActivo)
          }
          if (this.pendingActivePlace) {
            const queuedPlace = this.pendingActivePlace
            this.pendingActivePlace = null
            this.centerOnPlace(queuedPlace)
          }
        })
        
      } catch (error) {
        console.error('Error creating map:', error)
        this.handleMapError()
      }
    },
    
    addTileLayer() {
      if (!this.map || !this.L) return
      
      try {
        // Usar tiles de CartoDB que son más estables
        const tileLayer = this.L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
          attribution: '© OpenStreetMap contributors, © CartoDB',
          subdomains: 'abcd',
          maxZoom: 19,
          minZoom: 1,
          // Configuraciones para estabilidad
          updateWhenIdle: true,
          updateWhenZooming: false,
          keepBuffer: 2
        })
        
        tileLayer.addTo(this.map)
        
      } catch (error) {
        console.error('Error adding tile layer:', error)
        // Fallback a OpenStreetMap
        try {
          const fallbackLayer = this.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18,
            minZoom: 2
          })
          fallbackLayer.addTo(this.map)
        } catch (fallbackError) {
          console.error('Error adding fallback tile layer:', fallbackError)
        }
      }
    },
    
    getLocationScopeConfig(lugar) {
      const normalize = (value) => String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()

      const rawScope = normalize(lugar?.categoria || lugar?.category || lugar?.scope || lugar?.locType || lugar?.type)
      const scopeColors = {
        internacional: {
          color: '#dc2626',
          bgColor: '#ffffff',
          className: 'scope-internacional-marker'
        },
        nacional: {
          color: '#16a34a',
          bgColor: '#ffffff',
          className: 'scope-nacional-marker'
        },
        regional: {
          color: '#f97316',
          bgColor: '#ffffff',
          className: 'scope-regional-marker'
        },
        local: {
          color: '#06b6d4',
          bgColor: '#ffffff',
          className: 'scope-local-marker'
        }
      }

      return scopeColors[rawScope] || null
    },

    getMarkerIcon(lugar) {
      if (!this.L) return null
      
      // Iconos SVG personalizados para mejor calidad y contraste
      const iconSVGs = {
        // Gastronomía - Tenedor y cuchillo
        restaurant: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z" fill="currentColor"/>
        </svg>`,
        // Alojamiento - Cama
        accommodation: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M7 14c1.66 0 3-1.34 3-3S8.66 8 7 8s-3 1.34-3 3 1.34 3 3 3zm12-7h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z" fill="currentColor"/>
        </svg>`,
        // Atracciones/Naturaleza - Montaña
        attraction: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 3L2 21h20L12 3zm0 4.29l5.79 10.71H6.21L12 7.29z" fill="currentColor"/>
        </svg>`,
        // Museos - Edificio
        museum: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" fill="currentColor"/>
        </svg>`,
        // Eventos - Calendario
        event: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-7 5h5v5h-5v-5z" fill="currentColor"/>
        </svg>`,
        // Ciudades - Edificios múltiples
        city: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M15 11V5l-3-3-3 3v2H3v14h18V11h-6zm-8 8H5v-2h2v2zm0-4H5v-2h2v2zm0-4H5V9h2v2zm6 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm6 12h-2v-2h2v2zm0-4h-2v-2h2v2z" fill="currentColor"/>
        </svg>`,
        // Destinos - Ruta
        destination: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2C8.69 2 6 4.69 6 8c0 4.5 6 11 6 11s6-6.5 6-11c0-3.31-2.69-6-6-6zm0 8.25A2.25 2.25 0 1 1 12 5.75a2.25 2.25 0 0 1 0 4.5zM4 22l4-2 4 2 4-2 4 2v-2.25l-4-2-4 2-4-2-4 2V22z" fill="currentColor"/>
        </svg>`
      }
      
      // Configuración de colores y tamaños
      const iconConfigs = {
        restaurant: {
          svg: iconSVGs.restaurant,
          color: '#dc2626', // rojo más vibrante
          bgColor: '#ffffff',
          borderColor: '#dc2626',
          size: 24
        },
        accommodation: {
          svg: iconSVGs.accommodation,
          color: '#2563eb', // azul más vibrante
          bgColor: '#ffffff',
          borderColor: '#2563eb',
          size: 24
        },
        attraction: {
          svg: iconSVGs.attraction,
          color: '#059669', // verde más vibrante
          bgColor: '#ffffff',
          borderColor: '#059669',
          size: 24
        },
        museum: {
          svg: iconSVGs.museum,
          color: '#7c3aed', // morado más vibrante
          bgColor: '#ffffff',
          borderColor: '#7c3aed',
          size: 24
        },
        event: {
          svg: iconSVGs.event,
          color: '#d97706', // naranja más vibrante
          bgColor: '#ffffff',
          borderColor: '#d97706',
          size: 24
        },
        city: {
          svg: iconSVGs.city,
          color: '#059669', // verde oscuro
          bgColor: '#ffffff',
          borderColor: '#059669',
          size: 22
        },
        destination: {
          svg: iconSVGs.destination,
          color: '#0f766e',
          bgColor: '#ffffff',
          borderColor: '#0f766e',
          size: 24
        }
      }
      
      const defaultMarkerSvg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z" fill="currentColor"/></svg>'
      const scopeConfig = this.getLocationScopeConfig(lugar)
      const legacyTypes = Object.keys(iconConfigs)
      const rawType = lugar.locType || lugar.tipo || 'place'
      const lugarType = legacyTypes.includes(rawType) ? rawType : 'place'

      const applyScopeColor = (config) => {
        if (!scopeConfig) return config
        return {
          ...config,
          color: scopeConfig.color,
          borderColor: scopeConfig.color,
          bgColor: scopeConfig.bgColor
        }
      }
      
      // Si es destacado, usar estilo especial (más grande y destacado)
      if (lugar.destacado) {
        const config = applyScopeColor(iconConfigs[lugarType] || {
          svg: defaultMarkerSvg,
          color: '#f59e0b',
          bgColor: '#ffffff',
          borderColor: '#f59e0b',
          size: 26
        })
        return this.L.divIcon({
          className: 'highlighted-marker',
          html: `
            <div style="
              background-color: ${config.bgColor}; 
              width: ${config.size}px; 
              height: ${config.size}px; 
              border-radius: 50%; 
              border: 3px solid ${config.borderColor}; 
              box-shadow: 0 4px 8px rgba(0,0,0,0.4), 0 0 0 2px rgba(255,255,255,0.8);
              display: flex;
              align-items: center;
              justify-content: center;
              position: relative;
            ">
              <span style="color: ${config.color}; display: flex; align-items: center; justify-content: center;">
                ${config.svg}
              </span>
            </div>
          `,
          iconSize: [config.size, config.size],
          iconAnchor: [config.size / 2, config.size / 2]
        })
      }
      
      // Icono normal según tipo
      const config = applyScopeColor(iconConfigs[lugarType] || {
        svg: defaultMarkerSvg,
        color: '#6b7280',
        bgColor: '#ffffff',
        borderColor: '#6b7280',
        size: 24
      })
      if (config) {
        return this.L.divIcon({
          className: `${lugarType}-marker ${scopeConfig ? scopeConfig.className : ''}`.trim(),
          html: `
            <div style="
              background-color: ${config.bgColor}; 
              width: ${config.size}px; 
              height: ${config.size}px; 
              border-radius: 50%; 
              border: 2.5px solid ${config.borderColor}; 
              box-shadow: 0 2px 6px rgba(0,0,0,0.3);
              display: flex;
              align-items: center;
              justify-content: center;
              position: relative;
            ">
              <span style="color: ${config.color}; display: flex; align-items: center; justify-content: center;">
                ${config.svg}
              </span>
            </div>
          `,
          iconSize: [config.size, config.size],
          iconAnchor: [config.size / 2, config.size / 2]
        })
      }
      
      // Marcador por defecto (sin tipo específico) - usar punto azul estándar
      return null
    },
    
    addMarkers() {
      if (!this.isMapUsable()) return
      if (this.isZooming) { this.schedule(() => this.addMarkers(), 120); return }
      // Evitar manipular capas si el mapa aún no está completamente cargado
      try { if (!this.map._loaded) return } catch(e) { /* noop */ }
      
      try {
        // Limpiar marcadores existentes
        this.clearMarkers()
        
        this.lugares.forEach(lugar => {
          if (lugar.coordenadas && Array.isArray(lugar.coordenadas) && lugar.coordenadas.length === 2) {
            try {
              const [lat, lng] = lugar.coordenadas
              
              // Validar coordenadas
              if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                console.warn('Invalid coordinates:', lugar.nombre, lugar.coordenadas)
                return
              }
              
              // Obtener icono personalizado según el tipo
              const customIcon = this.getMarkerIcon(lugar)
              
              // Crear opciones del marcador
              const markerOptions = {}
              if (customIcon) {
                markerOptions.icon = customIcon
              }
              
              const marker = this.L.marker([lat, lng], markerOptions)
              if (this.markerLayer) {
                marker.addTo(this.markerLayer)
              } else {
                marker.addTo(this.map)
              }
              if (this.enableMarkerPopups) {
                try { marker.bindPopup(this.createPopupContent(lugar), { className: 'cb-popup' }) } catch(e) { /* noop */ }
              }
              
              // Agregar tooltip
              marker.bindTooltip(lugar.nombre, {
                permanent: false,
                direction: 'top',
                className: lugar.destacado ? 'highlighted-marker-tooltip' : 'custom-marker-tooltip'
              })
              
              // Emitir evento al hacer clic en el marcador
              try {
                marker.on('click', () => {
                  try { this.$emit('place-clicked', lugar) } catch (e) { /* noop */ }
                })
              } catch (e) { /* noop */ }

              this.markers.push(marker)
              
            } catch (error) {
              console.error('Error adding marker for:', lugar.nombre, error)
            }
          }
        })
        
      } catch (error) {
        console.error('Error in addMarkers:', error)
      }
    },
    
    disposeMarker(marker) {
      try { marker && marker.closePopup && marker.closePopup() } catch(e) {}
      try { marker && marker.unbindPopup && marker.unbindPopup() } catch(e) {}
      try { marker && marker.closeTooltip && marker.closeTooltip() } catch(e) {}
      try { marker && marker.unbindTooltip && marker.unbindTooltip() } catch(e) {}
      try { marker && marker.off && marker.off() } catch(e) {}
      try { marker && marker.remove && marker.remove() } catch(e) {}
    },

    clearMarkers() {
      try {
        this.closeMapOverlays()
        // Eliminar marcador activo primero
        try {
          if (this.activeMarker) {
            this.disposeMarker(this.activeMarker)
          }
        } catch(e) { /* noop */ }
        this.activeMarker = null

        this.markers.forEach(marker => {
          this.disposeMarker(marker)
        })

        if (this.markerLayer && typeof this.markerLayer.clearLayers === 'function') {
          this.markerLayer.clearLayers()
        }

        // Asegurar limpieza también de arreglo fallback
      } catch (error) {
        console.error('Error clearing markers:', error)
      }
      this.markers = []
    },
    
    async addRoutes() {
      if (!this.map || !this.mapInitialized || !this.L || !this.showRoutes) return
      
      try {
        // Limpiar rutas existentes
        this.clearRoutes()
        
        // Crear rutas que conecten los lugares principales
        const routeConnections = this.getRouteConnections()
        
        console.log('Route connections found:', routeConnections.length)
        
        // Crear rutas reales usando routing
        for (let i = 0; i < routeConnections.length; i++) {
          const connection = routeConnections[i]
          const { from, to, color, weight } = connection
          
          console.log(`Creating real route ${i + 1}:`, { from, to, color, weight })
          
          try {
            // Crear ruta real siguiendo carreteras
            const route = await this.createRealRoute(from, to, color, weight)
            if (route) {
              this.routes.push(route)
            }
          } catch (error) {
            console.error(`Error creating route ${i + 1}:`, error)
            // Fallback a línea recta si falla el routing
            const fallbackRoute = this.L.polyline([from, to], {
              color: color,
              weight: weight,
              opacity: 0.6,
              dashArray: this.getDashArray(),
              className: 'transport-route-fallback'
            }).addTo(this.map)
            this.routes.push(fallbackRoute)
          }
        }
        
        console.log('Total routes added:', this.routes.length)
        
      } catch (error) {
        console.error('Error adding routes:', error)
      }
    },
    
    clearRoutes() {
      this.routes.forEach(route => {
        try {
          if (route && typeof route.remove === 'function') {
            route.remove()
          }
        } catch (error) {
          console.error('Error removing route:', error)
        }
      })
      this.routes = []
    },
    
    getRouteConnections() {
      // Definir conexiones principales entre lugares turísticos de Bolivia
      const connections = []
      
      // Obtener coordenadas de los lugares
      const coords = this.lugares
        .filter(lugar => lugar.coordenadas && lugar.coordenadas.length === 2)
        .map(lugar => ({
          nombre: lugar.nombre,
          region: lugar.region,
          coords: lugar.coordenadas
        }))
      
      console.log('Available places:', coords.map(c => c.nombre))
      
      if (coords.length < 2) {
        console.log('Not enough places to create routes')
        return connections
      }
      
      // Crear rutas principales entre lugares importantes
      const mainRoutes = [
        // Ruta La Paz (Tiwanaku) - Santa Cruz (Amboró)
        { from: 'Tiwanaku', to: 'Parque Nacional Amboró', color: '#007bff', weight: 4 },
        // Ruta La Paz (Tiwanaku) - Cochabamba (Cristo)
        { from: 'Tiwanaku', to: 'Cristo de la Concordia', color: '#28a745', weight: 3 },
        // Ruta Santa Cruz (Amboró) - Cochabamba (Cristo)
        { from: 'Parque Nacional Amboró', to: 'Cristo de la Concordia', color: '#ffc107', weight: 3 },
        // Ruta La Paz (Titicaca) - Potosí (Salar de Uyuni)
        { from: 'Lago Titicaca', to: 'Salar de Uyuni', color: '#dc3545', weight: 3 },
        // Ruta Potosí (Salar) - Potosí (Cerro Rico)
        { from: 'Salar de Uyuni', to: 'Cerro Rico', color: '#6f42c1', weight: 2 },
        // Ruta Oruro (Carnaval) - Potosí (Salar)
        { from: 'Carnaval de Oruro', to: 'Salar de Uyuni', color: '#17a2b8', weight: 2 },
        // Ruta Cochabamba (Cristo) - Tarija (Valle)
        { from: 'Cristo de la Concordia', to: 'Valle de Tarija', color: '#fd7e14', weight: 2 }
      ]
      
      mainRoutes.forEach(route => {
        const fromCoords = coords.find(c => c.nombre === route.from)
        const toCoords = coords.find(c => c.nombre === route.to)
        
        console.log(`Looking for route: ${route.from} -> ${route.to}`)
        console.log('From found:', fromCoords)
        console.log('To found:', toCoords)
        
        if (fromCoords && toCoords) {
          connections.push({
            from: fromCoords.coords,
            to: toCoords.coords,
            color: route.color,
            weight: route.weight
          })
          console.log('Route added successfully')
        } else {
          console.log('Route not added - missing coordinates')
        }
      })
      
      console.log('Total connections created:', connections.length)
      return connections
    },
    
    async createRealRoute(from, to, color, weight) {
      try {
        // Usar rutas predefinidas que sigan caminos reales de Bolivia
        const routePath = this.getPredefinedRoute(from, to)
        
        if (routePath && routePath.length > 0) {
          // Crear la ruta con puntos intermedios
          const route = this.L.polyline(routePath, {
            color: color,
            weight: weight,
            opacity: 0.8,
            dashArray: this.getDashArray(),
            className: 'transport-route-real'
          }).addTo(this.map)
          
          return route
        }
        
        // Fallback a línea recta si no hay ruta predefinida
        return null
        
      } catch (error) {
        console.error('Error creating real route:', error)
        throw error
      }
    },
    
    getPredefinedRoute(from, to) {
      // Rutas predefinidas que siguen carreteras reales de Bolivia
      const routes = {
        // Ruta La Paz (Tiwanaku) - Santa Cruz (Amboró) - Ruta 4
        'Tiwanaku-Parque Nacional Amboró': [
          [-16.5547, -68.6734], // Tiwanaku
          [-16.5, -68.15],      // La Paz
          [-16.8, -67.5],       // Punto intermedio
          [-17.2, -67.0],       // Punto intermedio
          [-17.3895, -66.1568], // Cochabamba (Cristo)
          [-17.6, -65.5],       // Punto intermedio
          [-18.0, -64.5],       // Punto intermedio
          [-18.1667, -63.8333]  // Santa Cruz (Amboró)
        ],
        
        // Ruta La Paz (Tiwanaku) - Cochabamba (Cristo) - Ruta 4
        'Tiwanaku-Cristo de la Concordia': [
          [-16.5547, -68.6734], // Tiwanaku
          [-16.5, -68.15],      // La Paz
          [-16.8, -67.5],       // Punto intermedio
          [-17.1, -67.0],       // Punto intermedio
          [-17.3895, -66.1568]  // Cochabamba (Cristo)
        ],
        
        // Ruta Santa Cruz (Amboró) - Cochabamba (Cristo) - Ruta 4
        'Parque Nacional Amboró-Cristo de la Concordia': [
          [-18.1667, -63.8333], // Santa Cruz (Amboró)
          [-17.8, -64.5],       // Punto intermedio
          [-17.5, -65.2],       // Punto intermedio
          [-17.3895, -66.1568]  // Cochabamba (Cristo)
        ],
        
        // Ruta La Paz (Titicaca) - Potosí (Salar) - Ruta 1
        'Lago Titicaca-Salar de Uyuni': [
          [-16.1667, -69.0833], // Lago Titicaca
          [-16.3, -68.8],       // Punto intermedio
          [-16.5, -68.15],      // La Paz
          [-17.2, -67.8],       // Punto intermedio
          [-17.9754, -67.1105], // Oruro (Carnaval)
          [-18.8, -66.5],       // Punto intermedio
          [-19.5833, -65.75],   // Cerro Rico
          [-19.8, -66.8],       // Punto intermedio
          [-20.1339, -67.4891]  // Salar de Uyuni
        ],
        
        // Ruta Potosí (Salar) - Potosí (Cerro Rico) - Ruta corta
        'Salar de Uyuni-Cerro Rico': [
          [-20.1339, -67.4891], // Salar de Uyuni
          [-19.8, -66.8],       // Punto intermedio
          [-19.5833, -65.75]    // Cerro Rico
        ],
        
        // Ruta Oruro (Carnaval) - Potosí (Salar) - Ruta 1
        'Carnaval de Oruro-Salar de Uyuni': [
          [-17.9754, -67.1105], // Oruro (Carnaval)
          [-18.8, -66.5],       // Punto intermedio
          [-19.5833, -65.75],   // Cerro Rico
          [-19.8, -66.8],       // Punto intermedio
          [-20.1339, -67.4891]  // Salar de Uyuni
        ],
        
        // Ruta Cochabamba (Cristo) - Tarija (Valle) - Ruta 1
        'Cristo de la Concordia-Valle de Tarija': [
          [-17.3895, -66.1568], // Cochabamba (Cristo)
          [-18.5, -65.8],       // Punto intermedio
          [-19.5833, -65.75],   // Cerro Rico
          [-20.5, -65.2],       // Punto intermedio
          [-21.5355, -64.7296]  // Valle de Tarija
        ]
      }
      
      // Buscar la ruta por nombre
      const routeKey = `${this.getPlaceName(from)}-${this.getPlaceName(to)}`
      const reverseRouteKey = `${this.getPlaceName(to)}-${this.getPlaceName(from)}`
      
      return routes[routeKey] || routes[reverseRouteKey] || null
    },
    
    getPlaceName(coords) {
      // Obtener el nombre del lugar basado en las coordenadas
      const place = this.lugares.find(lugar => 
        lugar.coordenadas && 
        Math.abs(lugar.coordenadas[0] - coords[0]) < 0.1 && 
        Math.abs(lugar.coordenadas[1] - coords[1]) < 0.1
      )
      return place ? place.nombre : 'Unknown'
    },
    
    getRoutingProfile() {
      // Perfil de routing según el transporte
      const profiles = {
        car: 'driving-car',
        bus: 'driving-hgv', // Heavy goods vehicle para buses
        plane: 'driving-car', // Los aviones no usan carreteras, pero usamos car para simular
        train: 'driving-car', // Los trenes tienen sus propias vías, pero usamos car para simular
        boat: 'driving-car' // Los barcos no usan carreteras, pero usamos car para simular
      }
      
      return profiles[this.selectedTransport] || 'driving-car'
    },
    
    getDashArray() {
      // Diferentes estilos de línea según el transporte
      const dashStyles = {
        car: null, // Línea sólida
        bus: '10, 5', // Línea punteada
        plane: '20, 10', // Línea más espaciada
        train: '5, 5', // Línea corta
        boat: '15, 5, 5, 5' // Línea con patrón especial
      }
      
      return dashStyles[this.selectedTransport] || null
    },
    
    createPopupContent(lugar) {
      const fallbackImg = '/images/viaja.jpg'
      const hasImages = Array.isArray(lugar.imagenes) && lugar.imagenes.length > 0
      const imgUrl = hasImages ? String(lugar.imagenes[0]) : fallbackImg
      const detailUrl = lugar.detailUrl ? String(lugar.detailUrl) : ''
      const mediaContent = `<div class="popup-media${hasImages ? '' : ' placeholder'}" style="background-image:url('${imgUrl}')"></div>`
      const mediaDiv = detailUrl
        ? `<a class="popup-media-link" href="${detailUrl}">${mediaContent}</a>`
        : mediaContent
      const readMore = detailUrl ? ` <a class="popup-read-more" href="${detailUrl}">Leer más</a>` : ''
      const region = lugar.region ? String(lugar.region) : (lugar.tipo === 'city' ? 'Ciudad' : 'Lugar')
      const descripcion = lugar.descripcion ? String(lugar.descripcion) : 'Sin descripción disponible'
      const shortDescription = descripcion.length > 165 ? `${descripcion.slice(0, 165).trim()}...` : descripcion
      return `
        <div class="map-popup-card">
          ${mediaDiv}
          <span class="popup-badge">${region}</span>
          <div class="popup-body">
            <h4 class="popup-title">${lugar.nombre}</h4>
            <div class="popup-sub"><i class="fas fa-map-marker-alt"></i><span>${region}</span></div>
            <p class="popup-desc">${shortDescription}${readMore}</p>
          </div>
        </div>
      `
    },
    
    centerOnPlace(lugar) {
      if (this.isDestroyed) return
      if (!lugar || !lugar.coordenadas) return
      if (!this.map || !this.leafletLoaded) {
        this.pendingActivePlace = lugar
        return
      }
      if (!this.mapInitialized) {
        this.pendingActivePlace = lugar
        this.map.whenReady(() => {
          if (this.pendingActivePlace) {
            const queuedPlace = this.pendingActivePlace
            this.pendingActivePlace = null
            this.centerOnPlace(queuedPlace)
          }
        })
        return
      }
      if (this.isZooming) {
        this.pendingActivePlace = lugar
        return
      }
      
      try {
        const [lat, lng] = lugar.coordenadas
        
        // Validar coordenadas
        if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
          console.warn('Invalid coordinates for centering:', lugar.coordenadas)
          return
        }
        
        // Asegurar tamaño correcto y centrar sin animación
        this.closeMapOverlays()
        try { this.map.invalidateSize(false) } catch (e) { /* noop */ }
        this.isProgrammaticMove = true
        this.map.setView([lat, lng], 12, { animate: false, noMoveStart: true })
        this.pendingActivePlace = null
        
        // Eliminar marcador activo anterior si existe
        try {
          if (this.activeMarker) this.disposeMarker(this.activeMarker)
        } catch (e) { /* noop */ }
        this.activeMarker = null

        // Crear marcador especial para el lugar activo
        this.activeMarker = this.L.marker([lat, lng], {
          icon: this.L.divIcon({
            className: 'active-marker',
            html: `<div style="background-color: #dc3545; width: 25px; height: 25px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 3px 6px rgba(220,53,69,0.4);"></div>`,
            iconSize: [25, 25],
            iconAnchor: [12.5, 12.5]
          })
        })
        try {
          this.activeMarker.on('click', () => {
            try { this.$emit('place-clicked', lugar) } catch (e) { /* noop */ }
          })
        } catch (e) { /* noop */ }
        if (this.markerLayer) {
          try { this.activeMarker.addTo(this.markerLayer) } catch(e) { try { this.activeMarker.addTo(this.map) } catch(_) {} }
        } else {
          try { this.activeMarker.addTo(this.map) } catch(e) { /* noop */ }
        }

        if (this.enableMarkerPopups) {
          try { this.activeMarker.bindPopup(this.createPopupContent(lugar), { className: 'cb-popup' }) } catch (e) { /* noop */ }
        }
        
        if (this.enableMarkerPopups && this.openPopupOnCenter) {
          this.schedule(() => {
            if (!this.isMapUsable() || !this.activeMarker) return
            try { this.activeMarker.openPopup() } catch (e) { /* noop */ }
          }, 0)
        }
        
        // Agregar tooltip especial
        try { this.activeMarker.bindTooltip(`Ubicación: ${lugar.nombre}`, {
          permanent: true,
          direction: 'top',
          className: 'active-marker-tooltip'
        }) } catch (e) { /* noop */ }

        // Emitir la posición en pantalla del marcador para posicionar panel externo
        try {
          const mapElement = document.getElementById(this.uniqueMapId)
          if (mapElement) {
            const containerPoint = this.map.latLngToContainerPoint([lat, lng])
            const rect = mapElement.getBoundingClientRect()
            const pageY = rect.top + containerPoint.y
            // También devolver X por si se requiere más adelante
            const pageX = rect.left + containerPoint.x
            this.$emit('place-centered', { pageY, pageX })
          }
        } catch (e) { /* noop */ }
        
      } catch (error) {
        console.error('Error centering on place:', error)
      }
    },
    
    handleMapError() {
      this.mapError = true
      this.mapInitialized = false
      console.error('Map initialization failed')
    },
    
    async retryMap() {
      this.mapError = false
      this.retryCount++
      
      if (this.retryCount <= this.maxRetries) {
        console.log(`Retrying map initialization (${this.retryCount}/${this.maxRetries})`)
        await this.initializeMapSafely()
      } else {
        console.error('Max retry attempts reached')
        this.mapError = true
      }
    },
    
    destroyMap() {
      try {
        this.clearScheduledTimers()
        if (this.map) {
          this.closeMapOverlays()
          this.clearMarkers()
          this.clearRoutes()
          try { this.markerLayer && typeof this.markerLayer.remove === 'function' && this.markerLayer.remove() } catch(e) { /* noop */ }
          this.markerLayer = null
          try { this.activeMarker && this.disposeMarker(this.activeMarker) } catch (e) { /* noop */ }
          this.activeMarker = null
          
          // Desconectar eventos/handlers del mapa para evitar callbacks tardíos
          try { this.map.off && this.map.off() } catch(e) { /* noop */ }
          try { this.map._handlers && this.map._handlers.forEach(h => { try { h.disable() } catch(e) {} }) } catch(e) { /* noop */ }
          try { this.map.stop && this.map.stop() } catch(e) { /* noop */ }

          if (typeof this.map.remove === 'function') {
            this.map.remove()
          }
          
          this.map = null
        }
      } catch (error) {
        console.error('Error destroying map:', error)
      } finally {
        this.mapInitialized = false
        this.mapError = false
      }
    }
  },
  
  watch: {
    lugares: {
      handler() {
        if (!this.map) return
        // Si el mapa aún no disparó whenReady, espera al ready
        const applyUpdates = () => {
          this.addMarkers()
          if (this.showRoutes) {
            this.addRoutes()
          }
        }
        if (this.mapInitialized) {
          this.schedule(applyUpdates, 100)
        } else {
          this.map.whenReady(() => this.schedule(applyUpdates, 100))
        }
      },
      deep: true
    },
    
    lugarActivo: {
      handler(newLugar) {
        if (newLugar && this.map && this.mapInitialized) {
          this.schedule(() => {
            this.centerOnPlace(newLugar)
          }, 200)
        }
      },
      deep: true
    },
    
    showRoutes: {
      handler(newValue) {
        if (this.map && this.mapInitialized) {
          if (newValue) {
            this.schedule(() => {
              this.addRoutes()
            }, 200)
          } else {
            this.clearRoutes()
          }
        }
      }
    },
    
    selectedTransport: {
      handler() {
        if (this.map && this.mapInitialized && this.showRoutes) {
          this.schedule(() => {
            this.addRoutes()
          }, 200)
        }
      }
    }
  }
}
</script>

<style scoped>
.map-wrapper {
  position: relative;
  width: 100%;
}

.map-container {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px var(--shadow-color);
  position: relative;
  background-color: #f8f9fa;
}

.map-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-content {
  text-align: center;
  padding: 20px;
}

.map-error-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.error-content {
  text-align: center;
  padding: 20px;
}

.error-content i {
  font-size: 2rem;
}

/* Estilos para los popups del mapa */
::deep(.leaflet-popup-content-wrapper) {
  border-radius: 24px;
  box-shadow: 0 12px 32px rgba(0,0,0,0.18);
  padding: 0;
  overflow: visible;
}

::deep(.leaflet-popup-content) {
  margin: 0;
  padding: 0;
}

::deep(.map-popup-content) { padding: 0; min-width: 280px; }

::deep(.leaflet-popup-tip) {
  width: 18px !important;
  height: 18px !important;
  background: #fff !important;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* Extra tip to emulate pointer under the card body */
::deep(.map-popup-card)::after {
  content: '';
  position: absolute;
  left: 50%;
  bottom: -10px;
  transform: translateX(-50%);
  width: 0; height: 0;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
  border-top: 10px solid #fff;
  filter: drop-shadow(0 -1px 1px rgba(0,0,0,0.06));
}

::deep(.map-popup-card) {
  width: 560px;
  max-width: 92vw;
  position: relative;
}

::deep(.map-popup-card .popup-media) {
  height: 220px;
  border-radius: 24px 24px 0 0;
}

::deep(.map-popup-card .popup-media::after) {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(0deg, rgba(0,0,0,0.12), rgba(0,0,0,0));
}
::deep(.map-popup-card .popup-media.placeholder) { display:flex; align-items:center; justify-content:center; background: var(--light-gray); color: var(--text-muted); }
::deep(.map-popup-card .popup-badge) { position: relative; top: -16px; left: 12px; background: var(--primary-blue); color:#fff; padding: .35rem .65rem; border-radius:999px; font-size:.7rem; }
::deep(.map-popup-card .popup-body) {
  background: #fff;
  border-radius: 0 0 24px 24px;
  padding: 1rem 1.25rem 1rem;
}
::deep(.map-popup-card .popup-title) {
  font-size: 1.75rem;
}
::deep(.map-popup-card .popup-sub) { display:flex; align-items:center; gap:.4rem; color: var(--text-secondary); font-size: .9rem; margin-bottom:.25rem; }
::deep(.map-popup-card .popup-desc) { color: var(--text-secondary); font-size: 1rem; line-height:1.5; margin: 0 0 .25rem 0; }
::deep(.map-popup-card .popup-media-link) { display: block; text-decoration: none; color: inherit; }
::deep(.map-popup-card .popup-read-more) { display:inline; color: var(--primary-blue); font-weight:700; text-decoration:none; }
::deep(.map-popup-card .popup-read-more:hover) { text-decoration: underline; }
::deep(.map-popup-card .popup-actions) {
  margin-top: .5rem;
  display:grid; grid-template-columns: repeat(3,1fr); gap:.5rem 1rem; padding-top:.5rem; border-top: 1px solid var(--border-light); }
::deep(.map-popup-card .action) { display:inline-flex; align-items:center; gap:.5rem; color: var(--text-primary); background: transparent; border:none; padding:.25rem 0; text-decoration:none; font-size:.95rem; cursor:pointer; }
::deep(.map-popup-card .action i) { color: var(--text-secondary); }
::deep(.map-popup-card .action:hover i) { color: var(--primary-blue); }

/* Estilos para tooltips de marcadores */
:deep(.custom-marker-tooltip) {
  background: rgba(0, 0, 0, 0.8);
  color: white;
  border: none;
  border-radius: 4px;
  padding: 4px 8px;
  font-size: 12px;
  font-weight: 500;
}

:deep(.highlighted-marker-tooltip) {
  background: #ffc107;
  color: #000;
  border: none;
  border-radius: 4px;
  padding: 4px 8px;
  font-size: 12px;
  font-weight: 600;
}

:deep(.active-marker-tooltip) {
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 6px 10px;
  font-size: 14px;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

/* Estilos para marcadores personalizados */
:deep(.highlighted-marker) {
  background: transparent;
  border: none;
}

:deep(.active-marker) {
  background: transparent;
  border: none;
}

/* Estilos para marcadores */
:deep(.leaflet-marker-icon) {
  transition: all 0.3s ease;
}

:deep(.leaflet-marker-icon:hover) {
  transform: scale(1.1);
}

/* Estilos para las rutas de transporte */
:deep(.transport-route-real) {
  transition: all 0.3s ease;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

:deep(.transport-route-real:hover) {
  opacity: 1 !important;
  stroke-width: 8px !important;
  filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
}

:deep(.transport-route-fallback) {
  transition: all 0.3s ease;
  opacity: 0.6;
}

:deep(.transport-route-fallback:hover) {
  opacity: 0.8 !important;
  stroke-width: 6px !important;
}

/* Marcadores personalizados adicionales */
::deep(.city-marker),
::deep(.restaurant-marker),
::deep(.accommodation-marker),
::deep(.attraction-marker),
::deep(.museum-marker),
::deep(.event-marker),
::deep(.destination-marker) {
  background: transparent;
  border: none;
}

/* Badges auxiliares en popups */
:global(.bg-success) { background-color: #198754 !important; color: #fff !important; }

/* Forzar estilos en el popup con clase personalizada */
::deep(.cb-popup .leaflet-popup-content-wrapper) { border-radius: 24px; box-shadow: 0 12px 32px rgba(0,0,0,0.18); padding: 0; overflow: visible; }
::deep(.cb-popup .leaflet-popup-content) { margin: 0; padding: 0; }
::deep(.cb-popup .leaflet-popup-tip) { width: 18px !important; height: 18px !important; background: #fff !important; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
::deep(.cb-popup .map-popup-card) { width: 560px; max-width: 92vw; position: relative; }
::deep(.cb-popup .map-popup-card .popup-media) { height: 220px; border-radius: 24px 24px 0 0; background-size: cover; background-position: center; }
::deep(.cb-popup .map-popup-card .popup-media::after) { content:''; position:absolute; inset:0; background: linear-gradient(0deg, rgba(0,0,0,0.12), rgba(0,0,0,0)); }
::deep(.cb-popup .map-popup-card .popup-body) { background: #fff; border-radius: 0 0 24px 24px; padding: 1rem 1.25rem 1rem; }
::deep(.cb-popup .map-popup-card .popup-title) { font-size: 1.75rem; font-weight: 400; letter-spacing: -0.02em; margin: .25rem 0; color: var(--text-primary); }
::deep(.cb-popup .map-popup-card .popup-sub) { display:flex; align-items:center; gap:.4rem; color: var(--text-secondary); font-size:.9rem; margin-bottom:.25rem; }
::deep(.cb-popup .map-popup-card .popup-desc) { color: var(--text-secondary); font-size: 1rem; line-height:1.5; margin: 0 0 .25rem 0; }
::deep(.cb-popup .map-popup-card .popup-media-link) { display: block; text-decoration: none; color: inherit; }
::deep(.cb-popup .map-popup-card .popup-read-more) { display:inline; color: var(--primary-blue); font-weight:700; text-decoration:none; }
::deep(.cb-popup .map-popup-card .popup-read-more:hover) { text-decoration: underline; }
::deep(.cb-popup .map-popup-card .popup-actions) { display:grid; grid-template-columns: repeat(3,1fr); gap:.5rem 1rem; padding-top:.5rem; border-top: 1px solid var(--border-light); }
::deep(.cb-popup .map-popup-card .action) { display:inline-flex; align-items:center; gap:.5rem; color: var(--text-primary); background: transparent; border:none; padding:.25rem 0; text-decoration:none; font-size:.95rem; cursor:pointer; }
::deep(.cb-popup .map-popup-card .action i) { color: var(--text-secondary); }
::deep(.cb-popup .map-popup-card .action:hover i) { color: var(--primary-blue); }
</style>

<style>
/* Global overrides for Leaflet popup (ensures application even if scoped rules are bypassed) */
.cb-popup .leaflet-popup-content-wrapper { border-radius: 24px; box-shadow: 0 12px 32px rgba(0,0,0,0.18); padding: 0; overflow: visible; }
.cb-popup .leaflet-popup-content { margin: 0; padding: 0; }
.cb-popup .leaflet-popup-tip { width: 18px; height: 18px; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
.cb-popup .map-popup-card { width: 560px; max-width: 92vw; position: relative; }
.cb-popup .map-popup-card .popup-media { height: 220px; border-radius: 24px 24px 0 0; background-size: cover; background-position: center; }
.cb-popup .map-popup-card .popup-media::after { content:''; position:absolute; inset:0; background: linear-gradient(0deg, rgba(0,0,0,0.12), rgba(0,0,0,0)); }
.cb-popup .map-popup-card .popup-body { background: #fff; border-radius: 0 0 24px 24px; padding: 1rem 1.25rem 1rem; }
.cb-popup .map-popup-card .popup-title { font-size: 1.75rem; font-weight: 400; letter-spacing: -0.02em; margin: .25rem 0; color: var(--text-primary); }
.cb-popup .map-popup-card .popup-sub { display:flex; align-items:center; gap:.4rem; color: var(--text-secondary); font-size:.9rem; margin-bottom:.25rem; }
.cb-popup .map-popup-card .popup-desc { color: var(--text-secondary); font-size: 1rem; line-height:1.5; margin: 0 0 .25rem 0; }
.cb-popup .map-popup-card .popup-media-link { display: block; text-decoration: none; color: inherit; }
.cb-popup .map-popup-card .popup-read-more { display:inline; color: var(--primary-blue); font-weight:700; text-decoration:none; }
.cb-popup .map-popup-card .popup-read-more:hover { text-decoration: underline; }
.cb-popup .map-popup-card .popup-actions { display:grid; grid-template-columns: repeat(3,1fr); gap:.5rem 1rem; padding-top:.5rem; border-top: 1px solid var(--border-light); }
.cb-popup .map-popup-card .action { display:inline-flex; align-items:center; gap:.5rem; color: var(--text-primary); background: transparent; border:none; padding:.25rem 0; text-decoration:none; font-size:.95rem; cursor:pointer; }
.cb-popup .map-popup-card .action i { color: var(--text-secondary); }
.cb-popup .map-popup-card .action:hover i { color: var(--primary-blue); }
</style> 
