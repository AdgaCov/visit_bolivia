<template>
  <div class="map-wrapper">
    <!-- Contenedor del mapa con ID único -->
    <div 
      :id="uniqueMapId" 
      class="map-container"
      :style="{ height: mapHeight, width: '100%' }"
    >
      <!-- Indicador de carga -->
      <div v-if="!mapInitialized" class="map-loading-overlay">
        <div class="loading-content">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando mapa...</span>
          </div>
          <p class="mt-2 text-muted">Inicializando mapa...</p>
        </div>
      </div>
    </div>
    
    <!-- Botón de reinicio si hay error -->
    <div v-if="mapError" class="map-error-overlay">
      <div class="error-content">
        <i class="fas fa-exclamation-triangle text-warning mb-3"></i>
        <h6>Error al cargar el mapa</h6>
        <p class="text-muted">Hubo un problema al inicializar el mapa</p>
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
  name: 'SimpleMap',
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
    }
  },
  data() {
    return {
      map: null,
      markers: [],
      activeMarker: null,
      uniqueMapId: `map-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
      mapInitialized: false,
      mapError: false,
      leafletLoaded: false,
      L: null,
      retryCount: 0,
      maxRetries: 3,
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
      try { this.map && this.map.closePopup && this.map.closePopup() } catch (error) {}
      try { this.map && this.map.closeTooltip && this.map.closeTooltip() } catch (error) {}
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
        // Reintentar inicialización completa cuando el contenedor tenga tamaño
        this.schedule(() => {
          try { this.initializeMapSafely() } catch (e) { /* noop */ }
        }, 150)
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
          zoomControl: false,
          attributionControl: true,
          // Configuraciones para evitar errores
          preferCanvas: false,
          zoomSnap: 1,
          zoomDelta: 1,
          wheelPxPerZoomLevel: 60,
          scrollWheelZoom: false,
          touchZoom: false,
          doubleClickZoom: false,
          boxZoom: false,
          keyboard: false,
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

          // Agregar marcadores y centrar lugar activo de forma segura
          this.addMarkers()
          if (this.lugarActivo) {
            this.centerOnPlace(this.lugarActivo)
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
    
    addMarkers() {
      if (!this.isMapUsable()) return
      
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
              
              // Crear marcador simple
              const marker = this.L.marker([lat, lng])
                .addTo(this.map)
                .bindPopup(this.createPopupContent(lugar))
              
              // Agregar tooltip
              marker.bindTooltip(lugar.nombre, {
                permanent: false,
                direction: 'top',
                className: 'custom-marker-tooltip'
              })
              
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
      try { marker && marker.closePopup && marker.closePopup() } catch (error) {}
      try { marker && marker.unbindPopup && marker.unbindPopup() } catch (error) {}
      try { marker && marker.closeTooltip && marker.closeTooltip() } catch (error) {}
      try { marker && marker.unbindTooltip && marker.unbindTooltip() } catch (error) {}
      try { marker && marker.off && marker.off() } catch (error) {}
      try { marker && marker.remove && marker.remove() } catch (error) {}
    },

    clearMarkers() {
      this.closeMapOverlays()
      if (this.activeMarker) {
        this.disposeMarker(this.activeMarker)
        this.activeMarker = null
      }

      this.markers.forEach(marker => {
        try {
          this.disposeMarker(marker)
        } catch (error) {
          console.error('Error removing marker:', error)
        }
      })
      this.markers = []
    },
    
    createPopupContent(lugar) {
      return `
        <div class="map-popup-content">
          <h6 class="mb-2">${lugar.nombre}</h6>
          ${lugar.imagenes && lugar.imagenes[0] ? 
            `<img src="${lugar.imagenes[0]}" alt="${lugar.nombre}" 
                  style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 8px;">` : 
            ''
          }
          <p class="mb-2" style="font-size: 0.9rem;">${lugar.descripcion ? lugar.descripcion.substring(0, 80) + '...' : 'Sin descripción'}</p>
          <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-primary">${lugar.region || 'Sin región'}</span>
            <a href="/lugar/${lugar.id}" class="btn btn-sm btn-outline-primary">Ver más</a>
          </div>
        </div>
      `
    },
    
    centerOnPlace(lugar) {
      if (this.isDestroyed) return
      if (!lugar || !lugar.coordenadas || !this.map || !this.mapInitialized) return
      
      try {
        const [lat, lng] = lugar.coordenadas
        
        // Validar coordenadas
        if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
          console.warn('Invalid coordinates for centering:', lugar.coordenadas)
          return
        }
        
        // Centrar sin animación para evitar errores
        this.closeMapOverlays()
        this.map.setView([lat, lng], 12, { animate: false, noMoveStart: true })
        
        if (this.activeMarker) {
          this.disposeMarker(this.activeMarker)
          this.activeMarker = null
        }

        // Crear marcador especial para el lugar activo
        this.activeMarker = this.L.marker([lat, lng])
          .addTo(this.map)
          .bindPopup(this.createPopupContent(lugar))
        this.schedule(() => {
          if (!this.isMapUsable() || !this.activeMarker) return
          try { this.activeMarker.openPopup() } catch (error) {}
        }, 0)
        
        // Agregar tooltip especial
        this.activeMarker.bindTooltip(`📍 ${lugar.nombre}`, {
          permanent: true,
          direction: 'top',
          className: 'active-marker-tooltip'
        })
        
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
          try { this.map.off && this.map.off() } catch (error) {}
          try { this.map.stop && this.map.stop() } catch (error) {}
          
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
        if (this.map && this.mapInitialized) {
          this.schedule(() => {
            this.addMarkers()
          }, 200)
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
    }
  }
}
</script>

<style scoped>
.map-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
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
:deep(.leaflet-popup-content-wrapper) {
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

:deep(.leaflet-popup-content) {
  margin: 0;
  padding: 0;
}

:deep(.map-popup-content) {
  padding: 0;
  min-width: 200px;
}

:deep(.leaflet-popup-tip) {
  background: white;
}

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

/* Estilos para marcadores */
:deep(.leaflet-marker-icon) {
  transition: all 0.3s ease;
}

:deep(.leaflet-marker-icon:hover) {
  transform: scale(1.1);
}
</style> 
