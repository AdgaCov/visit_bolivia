<template>
  <section class="how-to-arrive my-4">
    <div class="card p-4">
      <h3 class="h5 mb-3">
        <i class="fas fa-route me-2"></i>Cómo llegar
      </h3>

      <!-- Mapa integrado -->
      <div v-if="lat && lng" class="mb-4">
        <div ref="mapContainer" class="map-container" style="height: 350px; border-radius: 8px; overflow: hidden; background: #e9ecef;"></div>
      </div>

      <div class="row g-4">
        <!-- Transports -->
        <div class="col-12 col-md-6">
          <h4 class="section-subtitle">Transporte</h4>
          <ul class="info-list">
            <li
              v-for="(item, i) in computedTransport"
              :key="i"
              class="info-item"
            >
              <i class="fas fa-bus icon"></i>
              <span class="text">{{ item }}</span>
            </li>
          </ul>
        </div>

        <!-- POI + Navigation -->
        <div class="col-12 col-md-6">
          <h4 class="section-subtitle">Puntos de interés</h4>
          <ul class="info-list">
            <!-- <li 
              v-for="(p, i) in computedPointsOfInterest" 
              :key="i"
              class="info-item"
            >
              <i class="fas fa-map-marker-alt icon"></i>
              <span class="text">
                <strong>{{ p.name }}</strong>
                <span class="distance">— {{ formatDistance(p.distanceKm) }}</span>
              </span>
            </li> -->

            <li v-if="userDistanceKm" class="info-item highlight">
              <i class="fas fa-walking icon"></i>
              <span class="text">
                <strong>Estás a {{ formatDistance(userDistanceKm) }}</strong>
                <span v-if="estimatedTime" class="time">{{ estimatedTime }}</span>
              </span>
            </li>
          </ul>

          <!-- Action buttons -->
          <div class="action-buttons">
            <button 
              @click="getUserLocation"
              class="btn-location"
              :disabled="loadingLocation"
            >
              <span v-if="loadingLocation" class="spinner"></span>
              <i v-else class="fas fa-map-marker-alt"></i>
              <span>{{ loadingLocation ? 'Obteniendo ubicación...' : 'Usar mi ubicación' }}</span>
            </button>

            <div class="external-links">
              <a
                v-if="osmUrl"
                :href="osmUrl"
                target="_blank"
                rel="noopener"
                class="link-external"
              >
                <i class="fas fa-external-link-alt"></i>
                <span>OpenStreetMap</span>
              </a>

              <a
                :href="googleUrl"
                target="_blank"
                rel="noopener"
                class="link-external"
              >
                <i class="fas fa-external-link-alt"></i>
                <span>Google Maps</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { patchLeafletForVueLifecycle } from '@/utils/leafletSafety'

interface POI { name: string; distanceKm: number; latitude?: number; longitude?: number }

const emit = defineEmits<{
  (e: 'user-location', payload: { latitude: number; longitude: number }): void
  (e: 'user-location-error', error?: GeolocationPositionError | null): void
}>()

const props = defineProps<{
  latitude?: string | number | null
  longitude?: string | number | null
  address?: string | null
  transport?: string[]
  pointsOfInterest?: POI[]
}>()

/* ----------------------
   Convert latitude/longitude
---------------------- */
const lat = computed(() =>
  props.latitude ? parseFloat(String(props.latitude)) : null
)
const lng = computed(() =>
  props.longitude ? parseFloat(String(props.longitude)) : null
)

/* ----------------------
   Default values
---------------------- */
const transport = computed(() =>
  props.transport || ['Taxi desde el centro', 'Bus urbano cercano']
)

const pointsOfInterest = computed<POI[]>(() =>
  props.pointsOfInterest || []
)

/* ----------------------
   User Location
---------------------- */
const userLat = ref<number | null>(null)
const userLng = ref<number | null>(null)
const userDistanceKm = ref<number | null>(null)
const estimatedTime = ref<string | null>(null)
const loadingLocation = ref(false)

// Distancias calculadas desde la ubicación del usuario
const computedTransport = computed(() => {
  if (!userLat.value || !userLng.value || !lat.value || !lng.value) {
    return transport.value
  }

  const distance = userDistanceKm.value || 0
  const timeMinutes = Math.round(distance * 1.5) // Aproximación: 1.5 min/km en taxi
  
  return [
    `A ${timeMinutes} min del centro en taxi`,
    ...transport.value.slice(1) // Mantener otros transportes
  ]
})

const computedPointsOfInterest = computed(() => {
  if (!userLat.value || !userLng.value) {
    return pointsOfInterest.value.map(p => ({
      ...p,
      distanceKm: p.distanceKm
    }))
  }

  // Calcular distancias desde la ubicación del usuario
  return pointsOfInterest.value.map(poi => {
    if (poi.latitude && poi.longitude) {
      const distance = haversineDistance(
        userLat.value!,
        userLng.value!,
        poi.latitude,
        poi.longitude
      )
      return {
        ...poi,
        distanceKm: distance
      }
    }
    return poi
  })
})

const getUserLocation = () => {
  if (!navigator.geolocation) {
    alert("Tu navegador no soporta geolocalización.")
    return
  }

  loadingLocation.value = true
  navigator.geolocation.getCurrentPosition(
    async (pos) => {
      userLat.value = pos.coords.latitude
      userLng.value = pos.coords.longitude
      emit('user-location', { latitude: userLat.value, longitude: userLng.value })

      if (lat.value && lng.value) {
        userDistanceKm.value = haversineDistance(
          userLat.value,
          userLng.value,
          lat.value,
          lng.value
        )
        
        // Calcular tiempo estimado (aproximación: velocidad promedio 40 km/h en ciudad)
        const timeMinutes = Math.round(userDistanceKm.value * 1.5)
        estimatedTime.value = `~${timeMinutes} min en auto`
        
        // Actualizar mapa con ruta
        await nextTick()
        updateMapWithRoute()
      }
      loadingLocation.value = false
    },
    (err) => {
      alert("No se pudo obtener tu ubicación.")
      emit('user-location-error', err)
      loadingLocation.value = false
    }
  )
}

/* ----------------------
   Google Maps fallback
---------------------- */
const googleUrl = computed(() => {
  if (lat.value != null && lng.value != null)
    return `https://www.google.com/maps?q=${lat.value},${lng.value}`

  if (props.address)
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(props.address)}`

  return 'https://www.google.com/maps'
})

/* ----------------------
   OpenStreetMap URL
---------------------- */
const osmUrl = computed(() => {
  if (!lat.value || !lng.value) return null

  if (userLat.value && userLng.value) {
    // Ruta desde ubicación del usuario
    return `https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=${userLat.value},${userLng.value};${lat.value},${lng.value}`
  }

  // Solo punto del destino
  return `https://www.openstreetmap.org/?mlat=${lat.value}&mlon=${lng.value}#map=17/${lat.value}/${lng.value}`
})

/* ----------------------
   Distance calculator (Haversine)
---------------------- */
function haversineDistance(lat1: number, lon1: number, lat2: number, lon2: number) {
  const R = 6371 // km
  const dLat = (lat2 - lat1) * Math.PI / 180
  const dLon = (lon2 - lon1) * Math.PI / 180

  const a =
    Math.sin(dLat/2) ** 2 +
    Math.cos(lat1 * Math.PI / 180) *
    Math.cos(lat2 * Math.PI / 180) *
    Math.sin(dLon/2) ** 2

  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
}

/* ----------------------
   Format distance
---------------------- */
function formatDistance(km: number | null): string {
  if (km === null) return '-'
  if (km < 1) return `${(km * 1000).toFixed(0)} m`
  return `${km.toFixed(2)} km`
}

/* ----------------------
   Map integration (Leaflet)
---------------------- */
const mapContainer = ref<HTMLElement | null>(null)
let mapInstance: any = null
let destinationMarker: any = null
let userMarker: any = null
let routeLayer: any = null

const loadLeaflet = (): Promise<void> => {
  return new Promise((resolve) => {
    if ((window as any).L) {
      patchLeafletForVueLifecycle((window as any).L)
      resolve()
      return
    }

    // Cargar CSS
    const cssId = 'leaflet-css'
    if (!document.getElementById(cssId)) {
      const link = document.createElement('link')
      link.id = cssId
      link.rel = 'stylesheet'
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
      document.head.appendChild(link)
    }

    // Cargar JS
    const scriptId = 'leaflet-js'
    if (document.getElementById(scriptId)) {
      const existing = document.getElementById(scriptId) as HTMLScriptElement
      existing.addEventListener('load', () => resolve(), { once: true })
      if ((window as any).L) {
        patchLeafletForVueLifecycle((window as any).L)
        resolve()
      }
      return
    }

    const script = document.createElement('script')
    script.id = scriptId
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
    script.async = true
    script.onload = () => {
      patchLeafletForVueLifecycle((window as any).L)
      resolve()
    }
    document.body.appendChild(script)
  })
}

const initMap = async () => {
  if (!mapContainer.value || !lat.value || !lng.value) return

  await loadLeaflet()
  const L = (window as any).L
  if (!L) return
  patchLeafletForVueLifecycle(L)

  // Limpiar mapa anterior si existe
  if (mapInstance) {
    destroyMap()
  }

  // Crear mapa centrado en el destino
  mapInstance = L.map(mapContainer.value, {
    zoomControl: false,
    scrollWheelZoom: false,
    touchZoom: false,
    doubleClickZoom: false,
    boxZoom: false,
    keyboard: false,
    zoomAnimation: false,
    markerZoomAnimation: false,
    fadeAnimation: false
  }).setView([lat.value, lng.value], 14, { animate: false, noMoveStart: true })

  // Agregar capa de tiles
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(mapInstance)

  // Marcar destino
  destinationMarker = L.marker([lat.value, lng.value])
    .addTo(mapInstance)
    .bindPopup(props.address || 'Destino')
    .openPopup()

  // Si hay ubicación del usuario, mostrar ruta
  if (userLat.value && userLng.value) {
    updateMapWithRoute()
  }
}

const updateMapWithRoute = async () => {
  if (!mapInstance || !userLat.value || !userLng.value || !lat.value || !lng.value) return

  const L = (window as any).L
  if (!L) return

  // Remover marcador y ruta anteriores
  if (userMarker) {
    try { userMarker.closePopup && userMarker.closePopup() } catch {}
    try { userMarker.unbindPopup && userMarker.unbindPopup() } catch {}
    try { userMarker.off && userMarker.off() } catch {}
    try { mapInstance.removeLayer(userMarker) } catch {}
    userMarker = null
  }
  if (routeLayer) {
    try { routeLayer.off && routeLayer.off() } catch {}
    try { mapInstance.removeLayer(routeLayer) } catch {}
    routeLayer = null
  }

  // Agregar marcador de usuario
  userMarker = L.marker([userLat.value, userLng.value], {
    icon: L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    })
  })
    .addTo(mapInstance)
    .bindPopup('Tu ubicación')

  // Obtener ruta usando OSRM
  try {
    const response = await fetch(
      `https://router.project-osrm.org/route/v1/driving/${userLng.value},${userLat.value};${lng.value},${lat.value}?overview=full&geometries=geojson`
    )
    const data = await response.json()

    if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
      const route = data.routes[0]
      const geometry = route.geometry

      // Crear polyline de la ruta
      routeLayer = L.geoJSON(geometry as any, {
        style: {
          color: '#007bff',
          weight: 4,
          opacity: 0.7
        }
      }).addTo(mapInstance)

      // Ajustar vista para mostrar toda la ruta
      const bounds = routeLayer.getBounds()
      mapInstance.fitBounds(bounds, { padding: [50, 50], animate: false })

      // Actualizar tiempo estimado con datos reales
      const durationSeconds = route.duration
      const durationMinutes = Math.round(durationSeconds / 60)
      estimatedTime.value = `~${durationMinutes} min en auto`
    }
  } catch (error) {
    console.error('Error obteniendo ruta:', error)
    // Dibujar línea recta como fallback
    routeLayer = L.polyline(
      [[userLat.value, userLng.value], [lat.value, lng.value]],
      { color: '#007bff', weight: 3, opacity: 0.5, dashArray: '10, 10' }
    ).addTo(mapInstance)
  }
}

const destroyMap = () => {
  try {
    if (destinationMarker) {
      try { destinationMarker.closePopup && destinationMarker.closePopup() } catch {}
      try { destinationMarker.unbindPopup && destinationMarker.unbindPopup() } catch {}
      try { destinationMarker.off && destinationMarker.off() } catch {}
      try { destinationMarker.remove && destinationMarker.remove() } catch {}
      destinationMarker = null
    }
    if (userMarker) {
      try { userMarker.closePopup && userMarker.closePopup() } catch {}
      try { userMarker.unbindPopup && userMarker.unbindPopup() } catch {}
      try { userMarker.off && userMarker.off() } catch {}
      try { userMarker.remove && userMarker.remove() } catch {}
      userMarker = null
    }
    if (routeLayer) {
      try { routeLayer.off && routeLayer.off() } catch {}
      try { routeLayer.remove && routeLayer.remove() } catch {}
      routeLayer = null
    }
    if (mapInstance) {
      try { mapInstance.closePopup && mapInstance.closePopup() } catch {}
      try { mapInstance.closeTooltip && mapInstance.closeTooltip() } catch {}
      try { mapInstance.off && mapInstance.off() } catch {}
      try { mapInstance._handlers && mapInstance._handlers.forEach((handler: any) => handler.disable && handler.disable()) } catch {}
      try { mapInstance.stop && mapInstance.stop() } catch {}
      try { mapInstance.remove && mapInstance.remove() } catch {}
      mapInstance = null
    }
  } catch {}
}

// Observar cambios en lat/lng para actualizar mapa
watch([lat, lng], () => {
  if (lat.value && lng.value) {
    nextTick(() => initMap())
  }
}, { immediate: true })

// Observar cambios en ubicación del usuario
watch([userLat, userLng], () => {
  if (userLat.value && userLng.value && mapInstance) {
    updateMapWithRoute()
  }
})

onMounted(() => {
  if (lat.value && lng.value) {
    nextTick(() => initMap())
  }
})

onBeforeUnmount(() => {
  destroyMap()
})
</script>

<style scoped>
.how-to-arrive .card {
  background: var(--bg-secondary);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  padding: 2rem !important;
}

.how-to-arrive h3 {
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
}

.section-subtitle {
  font-size: 0.875rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 1rem;
}

.info-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--border-light);
  transition: all 0.2s ease;
}

.info-item:last-child {
  border-bottom: none;
}

.info-item:hover {
  padding-left: 0.25rem;
}

.info-item.highlight {
  background: linear-gradient(135deg, rgba(30, 64, 175, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid rgba(30, 64, 175, 0.1);
  margin-top: 0.5rem;
}

.info-item .icon {
  color: var(--primary-blue);
  font-size: 1rem;
  margin-top: 0.125rem;
  flex-shrink: 0;
  width: 1.25rem;
  text-align: center;
}

.info-item.highlight .icon {
  color: var(--primary-blue-light);
}

.info-item .text {
  flex: 1;
  font-size: 0.9375rem;
  line-height: 1.6;
  color: var(--text-primary);
}

.info-item .text strong {
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
}

.info-item .distance {
  color: var(--text-secondary);
  font-weight: var(--font-weight-regular);
  margin-left: 0.25rem;
}

.info-item .time {
  display: block;
  font-size: 0.8125rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
  font-weight: var(--font-weight-regular);
}

/* Botón de ubicación */
.action-buttons {
  margin-top: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.btn-location {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  background: var(--primary-blue);
  color: var(--white);
  border: none;
  border-radius: 8px;
  font-size: 0.9375rem;
  font-weight: var(--font-weight-medium);
  font-family: var(--font-family-base);
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: var(--shadow-sm);
}

.btn-location:hover:not(:disabled) {
  background: var(--primary-blue-dark);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.btn-location:active:not(:disabled) {
  transform: translateY(0);
  box-shadow: var(--shadow-sm);
}

.btn-location:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-location .spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: var(--white);
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.btn-location i {
  font-size: 0.875rem;
}

/* Enlaces externos */
.external-links {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.link-external {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1rem;
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: var(--font-weight-medium);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: var(--white);
  transition: all 0.2s ease;
  font-family: var(--font-family-base);
}

.link-external:hover {
  color: var(--primary-blue);
  border-color: var(--primary-blue);
  background: var(--light-gray);
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

.link-external i {
  font-size: 0.75rem;
  opacity: 0.7;
}

.link-external:hover i {
  opacity: 1;
}

/* Mapa */
.map-container {
  border: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
}

/* Responsive */
@media (max-width: 768px) {
  .how-to-arrive .card {
    padding: 1.5rem !important;
  }

  .action-buttons {
    margin-top: 1.25rem;
  }

  .external-links {
    flex-direction: column;
  }

  .link-external {
    width: 100%;
    justify-content: center;
  }
}
</style>
