<template>
  <div class="map-page">
    <!-- Header del Mapa -->
    <section class="map-header-section">
      <div class="container">
        <div class="map-header-content">
          <h1 class="map-title">Mapa Interactivo de Bolivia</h1>
          <!-- <p class="map-description">
            Explora todos los lugares turísticos de Bolivia en un mapa interactivo. 
            Haz clic en los marcadores para ver información detallada de cada destino.
          </p>-->
          
          <!-- Filtros del Mapa -->
          <div class="map-filters">
            <div class="filter-group">
              <label class="filter-label">Categoría:</label>
              <select v-model="selectedCategory" class="filter-select">
                <option value="">Todas las categorias</option>
                <option value="Internacional">Internacionales</option>
                <option value="Nacional">Nacionales</option>
                <option value="Regional">Regionales</option>
                <option value="Local">Locales</option>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label">Destinos:</label>
              <select v-model="selectedDestination" class="filter-select destination-select">
                <option value="">Todos</option>
                <option value="destination">Solo destinos</option>
              </select>
            </div>
            
            <div class="filter-group">
              <label class="filter-label">Departamento:</label>
              <select v-model="selectedDepartment" class="filter-select">
                <option value="">Todos los departamentos</option>
                <option value="la-paz">La Paz</option>
                <option value="cochabamba">Cochabamba</option>
                <option value="santa-cruz">Santa Cruz</option>
                <option value="oruro">Oruro</option>
                <option value="potosi">Potosí</option>
                <option value="tarija">Tarija</option>
                <option value="beni">Beni</option>
                <option value="pando">Pando</option>
              </select>
            </div>
            <div class="filter-group search-filter-group">
              <label class="filter-label">Buscar lugar:</label>
              <div class="map-search-control">
                <input
                  v-model="searchDraft"
                  type="text"
                  class="filter-select map-search-input"
                  placeholder="Nombre o departamento"
                />
                <i class="fas fa-search map-search-icon"></i>
                <button
                  v-if="searchDraft"
                  class="map-search-clear"
                  type="button"
                  aria-label="Limpiar búsqueda"
                  @click="clearSearch"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section>
        <!-- Filtros de Categorías - Estilo pill -->
          <!-- <div class="category-filters">
            <button 
              class="filter-pill"
              :class="{ active: currentTab === 'alojamiento' }"
              @click="setTab('alojamiento')"
            >
              <i class="fas fa-bed"></i>
              Alojamiento
            </button>
            <button 
              class="filter-pill"
              :class="{ active: currentTab === 'eventos' }"
              @click="setTab('eventos')"
            >
              <i class="fas fa-calendar-alt"></i>
              Eventos
            </button>
            <button 
              class="filter-pill"
              :class="{ active: currentTab === 'gastronomia' }"
              @click="setTab('gastronomia')"
            >
              <i class="fas fa-utensils"></i>
              Gastronomía
            </button>
            <button 
              class="filter-pill"
              :class="{ active: currentTab === 'naturaleza' }"
              @click="setTab('naturaleza')"
            >
              <i class="fas fa-leaf"></i>
              Naturaleza
            </button>
            <button 
              class="filter-pill"
              :class="{ active: currentTab === 'historia' }"
              @click="setTab('historia')"
            >
              <i class="fas fa-theater-masks"></i>
              Historia & Cultura
            </button>
            <button 
              class="filter-pill"
              :class="{ active: currentTab === 'bienestar' }"
              @click="setTab('bienestar')"
            >
              <i class="fas fa-hot-tub"></i>
              Termas & Bienestar
            </button>
          </div> -->
    </section>

    <!-- Zona principal: Sidebar + Mapa -->
    <section class="map-main-section">

      



      <div class="map-experience">
        <!-- Sidebar de resultados -->
        <aside class="results-sidebar">
          <div class="sidebar-inner">
            <div class="results-meta">
              <span class="results-count">
                <template v-if="loading">Cargando lugares...</template>
                <template v-else>{{ displayedPlaces.length }} resultados</template>
              </span>
            </div>
            <div v-if="loading" class="results-loading">
              <div class="spinner"></div>
              <p>Cargando ubicaciones...</p>
            </div>
            <div v-else class="results-list">
              <div
                v-for="place in displayedPlaces"
                :key="`${place.tipo}-${place.id}`"
                class="result-card"
                @click="selectPlace(place)"
              >
                <div class="card-media" :class="{ placeholder: !(place.imagenes && place.imagenes[0]) }">
                  <img v-if="place.imagenes && place.imagenes[0]" :src="place.imagenes[0]" :alt="place.nombre">
                  <div v-else class="media-fallback">
                    <i class="fas fa-map-marked-alt"></i>
                  </div>
                  <span v-if="place.locType === 'destination'" class="badge badge-destination">Destino</span>
                  <span v-else-if="place.tipo === 'city'" class="badge badge-city">Ciudad</span>
                  <span v-else class="badge badge-dept">Lugar</span>
                </div>
                <div class="card-body">
                  <h4 class="card-title">{{ place.nombre }}</h4>
                  <div class="card-sub" v-if="place.tipo === 'place'">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ place.region }}</span>
                  </div>
                  <p class="card-desc">{{ place.descripcion }}</p>
                </div>
                <div class="card-actions">
                  <button class="btn btn-sm btn-outline-primary" @click.stop="viewOnMap(place)">
                    Ver en mapa
                  </button>
                  <router-link v-if="getDetailTo(place)" :to="getDetailTo(place)" class="btn btn-sm btn-primary">
                    Detalles
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <!-- Contenedor del Mapa -->
        <div class="map-area">
          
          <div class="map-wrapper" ref="mapWrapperRef">
            <InteractiveMap 
              :lugares="displayedPlaces"
              :center="[-16.5, -68.15]"
              :zoom="6"
              :lugarActivo="selectedPlace || undefined"
              height="600px"
              @place-clicked="handlePlaceClick"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Panel de Información del Lugar -->
    <div v-if="selectedPlace" class="place-info-panel">
      <div class="place-info-content">
        <button class="close-button" @click="closePlaceInfo">
          <i class="fas fa-times"></i>
        </button>
        
        <div class="place-info-header">
          <div class="place-image">
            <template v-if="getDetailTo(selectedPlace)">
              <router-link :to="getDetailTo(selectedPlace)" style="display:block; height:100%;">
                <img v-if="selectedPlace.imagenes?.[0]" :src="selectedPlace.imagenes?.[0]" :alt="selectedPlace.nombre">
              </router-link>
            </template>
            <template v-else>
              <img v-if="selectedPlace.imagenes?.[0]" :src="selectedPlace.imagenes?.[0]" :alt="selectedPlace.nombre">
            </template>
            <div class="place-badge" :style="{ backgroundColor: getCategoryColor(selectedPlace.categoria) }">
              {{ selectedPlace.categoria || 'Turístico' }}
            </div>
          </div>
          <div class="place-details">
            <template v-if="getDetailTo(selectedPlace)">
              <router-link :to="getDetailTo(selectedPlace)" class="place-name" style="text-decoration:none;">
                {{ selectedPlace.nombre }}
              </router-link>
            </template>
            <template v-else>
              <h3 class="place-name">{{ selectedPlace.nombre }}</h3>
            </template>
            <p class="place-location">
              <i class="fas fa-map-marker-alt"></i>
              {{ selectedPlace.region }}
            </p>
            <div class="place-rating" v-if="selectedPlace.rating">
              <i class="fas fa-star"></i>
              <span>{{ selectedPlace.rating }}</span>
            </div>
          </div>
        </div>
        
        <div class="place-description">
          <p>{{ selectedPlace.descripcion }}</p>
        </div>
      <transition name="fade">
        <div v-if="shareToast.visible" class="share-toast-panel" role="status" aria-live="polite">
          {{ shareToast.message }}
        </div>
      </transition>
      </div>
    </div>

    <!-- Leyenda del Mapa -->
    <!-- <div class="map-legend-fixed">
      <div class="legend-title">Leyenda</div>
      <div class="legend-items">
        <div class="legend-item">
          <div class="legend-icon cultura"></div>
          <span>Cultura</span>
        </div>
        <div class="legend-item">
          <div class="legend-icon naturaleza"></div>
          <span>Naturaleza</span>
        </div>
        <div class="legend-item">
          <div class="legend-icon aventura"></div>
          <span>Aventura</span>
        </div>
        <div class="legend-item">
          <div class="legend-icon gastronomia"></div>
          <span>Gastronomía</span>
        </div>
        <div class="legend-item">
          <div class="legend-icon relax"></div>
          <span>Relax</span>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script lang="ts">
import { ref, computed, onMounted, watch, getCurrentInstance } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import InteractiveMap from '@/components/InteractiveMap.vue'
import apiClient from '@/services/api'
import API_ENDPOINTS from '@/services/endpoints'

type MapPlace = {
  id: number
  nombre: string
  descripcion?: string
  imagenes?: string[]
  categoria?: string
  region?: string
  department?: string
  rating?: number
  coordenadas: [number, number]
  destacado?: boolean
  tipo?: 'department' | 'city' | 'place'
  locType?: string // type desde API locations (event, restaurant, accommodation, etc.)
  detailUrl?: string
}

function slugify(value: string): string {
  return value
    .toLowerCase()
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '')
    .replace(/\s+/g, '-')
}

export default {
  name: 'InteractiveMapPage',
  components: {
    InteractiveMap
  },
  setup() {


    const instance = getCurrentInstance()
    const $buildImg = instance?.appContext.config.globalProperties.$buildImg
    const route = useRoute()
    const router = useRouter()
    const selectedCategory = ref<string>('')
    const selectedDestination = ref<string>('')
    const selectedDepartment = ref<string>('')
    const selectedPlace = ref<MapPlace | null>(null)
    const searchDraft = ref<string>('')
    const mapWrapperRef = ref<HTMLElement | null>(null)
    const places = ref<MapPlace[]>([])
    const loading = ref<boolean>(false)
    const error = ref<string>('')

    const normalizeText = (value?: string | null) => {
      return String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .trim()
    }

    const mapLocationsToPlaces = (locations: any[]): MapPlace[] => {
  return locations.filter(Boolean).map((loc: any) => {
    const lat = parseFloat(loc.latitude)
    const lng = parseFloat(loc.longitude)
    const images = Array.isArray(loc.images) ? loc.images : []
    
    // ✅ Procesar cada imagen_url con $buildImg
    const mainImages = images
      .map((img: any) => img?.image_url)
      .filter((url: any) => !!url)
      .map((url: string) => $buildImg ? $buildImg(url) : url) // ✅ Aquí se procesa
    
    const coords: [number, number] = (!isNaN(lat) && !isNaN(lng)) ? [lat, lng] as [number, number] : [0, 0]
    const tipo: 'city' | 'place' = loc.type === 'city' ? 'city' : 'place'
    return {
      id: loc.id,
      nombre: loc.name,
      descripcion: loc.description,
      imagenes: mainImages,
      categoria: loc.type,
      region: loc.department?.name || loc.address || undefined,
      department: loc.department?.name || undefined,
      rating: undefined,
      coordenadas: coords,
      destacado: false,
      tipo,
      locType: loc.type
    }
  }).filter(p => !!p && !!p.nombre)
}
    // const mapLocationsToPlaces = (locations: any[]): MapPlace[] => {
    //   return locations.filter(Boolean).map((loc: any) => {
    //     const lat = parseFloat(loc.latitude)
    //     const lng = parseFloat(loc.longitude)
    //     const images = Array.isArray(loc.images) ? loc.images : []
    //     const mainImages = images.map((img: any) => img?.image_url).filter((u: any) => !!u)
    //     const coords: [number, number] = (!isNaN(lat) && !isNaN(lng)) ? [lat, lng] as [number, number] : [0, 0]
    //     const tipo: 'city' | 'place' = loc.type === 'city' ? 'city' : 'place'
    //     return {
    //       id: loc.id,
    //       nombre: loc.name,
    //       descripcion: loc.description,
    //       imagenes: mainImages,
    //       categoria: loc.type,
    //       region: loc.department?.name || loc.address || undefined,
    //       department: loc.department?.name || undefined,
    //       rating: undefined,
    //       coordenadas: coords,
    //       destacado: false,
    //       tipo,
    //       locType: loc.type
    //     }
    //   }).filter(p => !!p && !!p.nombre)
    // }

    onMounted(async () => {
      // Inicializar filtro desde query (?department=slug)
      const deptParam = route.query.department as string | undefined
      selectedDepartment.value = deptParam ? String(deptParam) : ''
      loading.value = true
      try {
        const res = await apiClient.get<{ success: boolean; data: any[] }>(API_ENDPOINTS.LOCATIONS.BASE)
        const items = res && res.success === true && Array.isArray(res.data) ? res.data as any[] : []
        places.value = mapLocationsToPlaces(items)
      } catch (e: any) {
        error.value = 'No se pudieron cargar los lugares'
        console.error(e)
      } finally {
        loading.value = false
      }
    })

    // Sincronizar filtro cuando cambie el query param
    watch(
      () => route.query.department,
      (newVal) => {
        selectedDepartment.value = newVal ? String(newVal) : ''
      }
    )


    const filteredPlaces = computed<MapPlace[]>(() => {
      let filtered = places.value

      if (selectedCategory.value) {
        filtered = filtered.filter(place => place.locType === selectedCategory.value)
      }

      if (selectedDestination.value) {
        filtered = filtered.filter(place => place.locType === selectedDestination.value)
      }

      if (selectedDepartment.value) {
        filtered = filtered.filter(place => {
          const deptName = place.department || place.region || ''
          const deptSlug = slugify(deptName || '')
          return deptSlug === String(selectedDepartment.value)
        })
      }

      // Filtro por búsqueda activa
      const q = normalizeText(searchDraft.value)
      if (q) {
        filtered = filtered.filter(p =>
          normalizeText(p.nombre).includes(q) ||
          normalizeText(p.region).includes(q) ||
          normalizeText(p.department).includes(q) ||
          normalizeText(p.descripcion).includes(q)
        )
      }

      return filtered
    })

    const displayedPlaces = computed<MapPlace[]>(() => {
      const hasSearch = !!searchDraft.value.trim()
      return filteredPlaces.value.map(place => {
        const detailTo = getDetailTo(place)
        const resolved = detailTo ? router.resolve(detailTo).href : undefined
        return {
          ...place,
          detailUrl: resolved,
          destacado: hasSearch ? true : place.destacado
        }
      })
    })

    const clearSearch = () => {
      searchDraft.value = ''
    }

    const getCategoryColor = (category?: string) => {
      const colors: Record<string, string> = {
        Internacional: '#dc2626',
        Nacional: '#16a34a',
        Regional: '#f97316',
        Local: '#06b6d4',
        destination: '#0f766e',
        cultura: 'var(--category-cultura)',
        naturaleza: 'var(--category-naturaleza)',
        aventura: 'var(--category-aventura)',
        gastronomia: 'var(--category-gastronomia)',
        relax: 'var(--category-relax)'
      }
      return category ? (colors[category] || 'var(--text-muted)') : 'var(--text-muted)'
    }

    const shareToast = ref({ visible: false, message: '' })
    let shareToastTimeout: number | undefined

    const showShareToast = (message: string) => {
      shareToast.value.message = message
      shareToast.value.visible = true
      if (shareToastTimeout) {
        window.clearTimeout(shareToastTimeout)
      }
      shareToastTimeout = window.setTimeout(() => {
        shareToast.value.visible = false
      }, 2200)
    }

    const hideShareToast = () => {
      shareToast.value.visible = false
      if (shareToastTimeout) {
        window.clearTimeout(shareToastTimeout)
        shareToastTimeout = undefined
      }
    }

    const resolvePlaceUrl = (place: MapPlace): string => {
      try {
        const detailRoute = getDetailTo(place)
        if (detailRoute) {
          const resolved = router.resolve(detailRoute)
          return `${window.location.origin}${resolved.href}`
        }
      } catch (error) {
        console.warn('No se pudo resolver la ruta del lugar', error)
      }
      return window.location.href
    }

    const shareSelectedPlace = async () => {
      if (!selectedPlace.value) return
      const place = selectedPlace.value
      const url = resolvePlaceUrl(place)
      const description = place.descripcion ?? 'Explora este destino de Bolivia Turismo.'
      const shareData = {
        title: place.nombre,
        text: `${place.nombre} – ${description}`,
        url
      }

      try {
        const nav: any = navigator
        if (nav?.share) {
          await nav.share(shareData)
          showShareToast('Enlace compartido')
          return
        }

        if (navigator.clipboard?.writeText) {
          await navigator.clipboard.writeText(url)
          showShareToast('Enlace copiado al portapapeles')
          return
        }

        showShareToast('Comparte el enlace manualmente')
      } catch (error) {
        console.warn('No se pudo compartir el enlace', error)
        showShareToast('No se pudo compartir el enlace')
      }
    }

    const resolveGoogleMapsLink = (place: MapPlace | null): string => {
      if (!place) return 'https://maps.google.com'
      if (place.coordenadas && Array.isArray(place.coordenadas)) {
        const [lat, lng] = place.coordenadas
        if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
          return `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`
        }
      }
      const query = encodeURIComponent(place.nombre || '')
      return `https://www.google.com/maps/search/?api=1&query=${query}`
    }

    const handlePlaceClick = (place: MapPlace) => {
      selectedPlace.value = place
      hideShareToast()
    }

    const closePlaceInfo = () => {
      selectedPlace.value = null
      hideShareToast()
    }

    const selectPlace = (place: MapPlace) => {
      selectedPlace.value = place
      hideShareToast()
    }

    const viewOnMap = (place: MapPlace) => {
      selectedPlace.value = place
      hideShareToast()
      // Desplazar suavemente hacia el mapa
      if (mapWrapperRef.value) {
        mapWrapperRef.value.scrollIntoView({ behavior: 'smooth', block: 'center' })
      }
    }

    const getDetailTo = (place: MapPlace) => {
      if (!place || !place.id) return null as any
      if (place.locType === 'event') return { name: 'GastronomyDetail', params: { id: place.id } }
      if (place.locType === 'restaurant') return { name: 'GastronomyDetail', params: { id: place.id } }
      if (place.locType === 'accommodation') return { name: 'GastronomyDetail', params: { id: place.id } }
      if (place.locType === 'museum') return { name: 'GastronomyDetail', params: { id: place.id } }
      if (place.locType === 'city') return { name: 'GastronomyDetail', params: { id: place.id } }
      if (place.locType === 'attraction') return { name: 'GastronomyDetail', params: { id: place.id } }
      if (place.locType === 'destination') return { name: 'GastronomyDetail', params: { id: place.id } }
      if (place.tipo === 'place') return { name: 'GastronomyDetail', params: { id: place.id } }
      return null as any
    }

    const nearbyPlaces = ref<MapPlace[]>([])
    const loadingNearby = ref<boolean>(false)
    const showNearby = ref<boolean>(false)

    const getNearbyPlaces = async (latitude: number, longitude: number, radius: number = 25) => {
      try {
        loadingNearby.value = true
        const response = await apiClient.get<{ success: boolean; data: any[] }>(
          `${API_ENDPOINTS.LOCATIONS.BASE}/nearby?latitude=${latitude}&longitude=${longitude}&radius=${radius}`
        )
        
        if (response.success && Array.isArray(response.data)) {
          nearbyPlaces.value = mapLocationsToPlaces(response.data)
          showNearby.value = true
        } else {
          nearbyPlaces.value = []
        }
      } catch (error) {
        console.error('Error loading nearby places:', error)
        nearbyPlaces.value = []
      } finally {
        loadingNearby.value = false
      }
    }

    const handleNearbyClick = () => {
      if (selectedPlace.value && selectedPlace.value.coordenadas) {
        const [lat, lng] = selectedPlace.value.coordenadas
        getNearbyPlaces(lat, lng, 25)
      }
    }

    const closeNearby = () => {
      showNearby.value = false
      nearbyPlaces.value = []
    }

    return {
      selectedCategory,
      selectedDestination,
      selectedDepartment,
      selectedPlace,
      places,
      filteredPlaces,
      displayedPlaces,
      searchDraft,
      clearSearch,
      getCategoryColor,
      handlePlaceClick,
      closePlaceInfo,
      selectPlace,
      viewOnMap,
      getDetailTo,
      mapWrapperRef,
      loading,
      error,
      nearbyPlaces,
      loadingNearby,
      showNearby,
      handleNearbyClick,
      closeNearby,
      shareSelectedPlace,
      shareToast,
      resolveGoogleMapsLink
    }
  }
}
</script>

<style scoped>
.map-page {
  background: var(--bg-primary);
  min-height: 100vh;
}

/* Header del Mapa */
.map-header-section {
  padding: 3rem 0 2rem;
  background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
  border-bottom: 1px solid var(--border-light);
}

.map-header-content {
  text-align: center;
  max-width: 1180px;
  margin: 0 auto;
}

.map-title {
  font-size: 3rem;
  font-weight: 300;
  color: var(--text-primary);
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}

.map-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  margin-bottom: 2rem;
  line-height: 1.6;
}

/* Filtros del Mapa */
.map-filters {
  display: flex;
  flex-wrap: nowrap;
  gap: 1rem;
  justify-content: center;
  align-items: flex-end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  text-align: left;
}

.filter-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.filter-select {
  padding: 0.5rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: var(--white);
  color: var(--text-primary);
  font-size: 0.875rem;
  min-width: 180px;
  transition: all 0.2s ease;
}

.destination-select {
  min-width: 160px;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary-blue);
  box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

.search-filter-group {
  min-width: 360px;
}

.map-search-control {
  display: flex;
  align-items: center;
  position: relative;
}

.map-search-input {
  min-width: 280px;
  padding-left: 2.45rem;
  padding-right: 2.45rem;
}

.map-search-icon {
  position: absolute;
  left: 0.95rem;
  color: var(--text-muted);
  pointer-events: none;
}

.map-search-clear {
  position: absolute;
  right: 0.45rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border: none;
  border-radius: 50%;
  background: transparent;
  color: var(--text-muted);
  cursor: pointer;
  transition: all 0.2s ease;
}

.map-search-clear:hover {
  background: var(--light-gray);
  color: var(--text-primary);
}

.filter-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-checkbox input[type="checkbox"] {
  width: 16px;
  height: 16px;
  accent-color: var(--primary-blue);
}

.filter-checkbox label {
  font-size: 0.875rem;
  color: var(--text-primary);
  cursor: pointer;
}

/* Contenedor del Mapa */
.map-container-section {
  padding: 2rem 0;
}

.map-wrapper {
  background: var(--white);
  border-radius: 16px;
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  min-height: 600px;
}
/* Main layout */
.map-main-section {
  padding: 0 0 2rem;
}

.map-experience {
  display: grid;
  grid-template-columns: 560px 1fr;
  gap: 1.25rem;
  align-items: stretch;
  padding: 0 1rem;
}

.results-sidebar {
  background: var(--white);
  border-radius: 16px;
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  height: 600px;
  overflow-y: auto;
}

.sidebar-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.search-box {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1rem 0.5rem;
  border-bottom: 1px solid var(--border-light);
}

.search-box i {
  color: var(--text-muted);
}

.search-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  font-size: 0.95rem;
}

.results-meta {
  padding: 0.5rem 1rem;
  color: var(--text-secondary);
  font-size: 0.85rem;
}

.results-list {
  overflow: visible;
  padding: 0.75rem 1rem 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.results-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2.5rem 1rem;
  color: var(--text-secondary);
  text-align: center;
  min-height: 260px;
}

.results-loading .spinner {
  width: 42px;
  height: 42px;
  border: 3px solid var(--border-light);
  border-top: 3px solid var(--primary-blue);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

/* Card estilo InterestCard */
.result-card {
  position: relative;
  background: transparent;
  border: none;
  border-radius: 0;
  overflow: visible;
  box-shadow: none;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.result-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--border-light) 0%, var(--border-color) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.result-card:hover::before { opacity: 1; }

.card-media {
  position: relative;
  height: 200px;
  background: var(--light-gray);
  border-radius: 24px;
  overflow: hidden;
  transition: border-radius 300ms ease;
}

.card-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scale(1);
  transition: transform 400ms ease;
  will-change: transform;
}

.card-media::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(0deg, rgba(0,0,0,0.12), rgba(0,0,0,0.0));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.media-fallback {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  font-size: 1.25rem;
}

.result-card:hover .card-media img { transform: scale(1.05); }
.result-card:hover .card-media { border-radius: 24px 50px 24px 24px; }
.result-card:hover .card-media::after { opacity: 1; }

.badge {
  position: absolute;
  bottom: 10px;
  left: 10px;
  font-size: 0.7rem;
  padding: 0.35rem 0.65rem;
  border-radius: 999px;
  color: #fff;
  backdrop-filter: saturate(120%) blur(2px);
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.badge-city { background: #198754; }
.badge-dept { background: #0d6efd; }
.badge-destination { background: #0f766e; }

.card-body {
  padding: 0.75rem 0 0.25rem;
}

.card-title {
  font-size: 1.5rem;
  font-weight: 400;
  color: var(--text-primary);
  margin: 0.75rem 0 0.25rem;
  letter-spacing: -0.02em;
  line-height: 1.25;
}

.card-sub {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin-bottom: 0.4rem;
}

.card-desc {
  color: var(--text-secondary);
  font-size: 1rem;
  margin: 0 0 0.25rem 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.5;
}

.card-actions {
  display: flex;
  gap: 1rem;
  padding: 0.4rem 0 0.6rem;
}

.btn.btn-sm {
  padding: 0.25rem 0.25rem;
  font-size: 0.9rem;
  background: transparent;
  border: none;
  color: var(--text-secondary);
}

.btn.btn-sm i { color: var(--text-secondary); }
.btn.btn-sm:hover { color: var(--text-primary); transform: translateY(-1px); }
.btn.btn-sm:hover i { color: var(--primary-blue); }

.map-area {
  min-height: 600px;
}

/* Panel de Información del Lugar */
.place-info-panel {
  position: fixed;
  top: 50%;
  right: 2rem;
  transform: translateY(-50%);
  width: 520px;
  max-height: 80vh;
  background: var(--white);
  border-radius: 16px;
  box-shadow: var(--shadow-xl);
  z-index: 1000;
  overflow: hidden;
  animation: slideInRight 0.3s ease-out;
}

/* Panel de Lugares Cercanos */
.nearby-panel {
  position: fixed;
  top: 50%;
  left: 2rem;
  transform: translateY(-50%);
  width: 480px;
  max-height: 80vh;
  background: var(--white);
  border-radius: 16px;
  box-shadow: var(--shadow-xl);
  z-index: 1000;
  overflow: hidden;
  animation: slideInLeft 0.3s ease-out;
}

.nearby-content {
  padding: 1.5rem;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.nearby-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-light);
}

.nearby-title {
  font-size: 1.5rem;
  font-weight: 400;
  color: var(--text-primary);
  margin: 0;
  letter-spacing: -0.02em;
}

.loading-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  color: var(--text-muted);
  text-align: center;
}

.loading-state i,
.empty-state i {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: var(--text-muted);
}

.nearby-list {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.nearby-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid var(--border-light);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.nearby-item:hover {
  border-color: var(--primary-blue);
  box-shadow: var(--shadow-sm);
  transform: translateY(-1px);
}

.nearby-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.nearby-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.nearby-fallback {
  width: 100%;
  height: 100%;
  background: var(--light-gray);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  font-size: 1.5rem;
}

.nearby-info {
  flex: 1;
  min-width: 0;
}

.nearby-name {
  font-size: 1.125rem;
  font-weight: 500;
  color: var(--text-primary);
  margin: 0 0 0.25rem 0;
  line-height: 1.3;
}

.nearby-location {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.nearby-description {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin: 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.nearby-actions {
  display: flex;
  align-items: flex-start;
  padding-top: 0.25rem;
}

/* Punta inferior para estilo de bocadillo */
.place-info-panel::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
  border-top: 10px solid var(--white);
  filter: drop-shadow(0 -1px 1px rgba(0,0,0,0.06));
}

.place-info-content {
  padding: 1.5rem;
  position: relative;
}

.close-button {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: var(--white);
  border: 1px solid var(--border-color);
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  z-index: 10;
}

.close-button:hover {
  background: var(--light-gray);
  border-color: var(--primary-blue);
  color: var(--primary-blue);
}

.place-info-header {
  margin-bottom: 1.5rem;
}

.place-image {
  position: relative;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.place-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.place-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.place-name {
  font-size: 1.75rem;
  font-weight: 400;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  letter-spacing: -0.02em;
}

.place-location {
  color: var(--text-secondary);
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.place-rating {
  color: var(--warning-orange);
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.place-description {
  margin-bottom: 1.5rem;
}

.place-description p {
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.place-actions-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem 2rem;
  padding-top: 0.5rem;
  border-top: 1px solid var(--border-light);
  margin-top: 0.75rem;
}

.action-item {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  color: var(--text-primary);
  text-decoration: none;
  background: transparent;
  border: none;
  padding: 0.25rem 0;
  cursor: pointer;
  font-size: 0.95rem;
}

.action-item i {
  font-size: 1.1rem;
  color: var(--text-secondary);
}

.action-item:hover {
  color: var(--text-primary);
  transform: translateY(-1px);
}

.action-item:hover i { color: var(--primary-blue); }

.share-toast-panel {
  position: absolute;
  top: 1rem;
  right: 1.5rem;
  background: rgba(0, 0, 0, 0.85);
  color: #fff;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  font-size: 0.85rem;
  box-shadow: var(--shadow-lg);
  z-index: 5;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  text-decoration: none;
  text-align: center;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-primary {
  background: var(--primary-blue);
  color: var(--white);
}

.btn-primary:hover {
  background: var(--primary-blue-dark);
  transform: translateY(-1px);
}

.btn-outline-primary {
  background: transparent;
  color: var(--primary-blue);
  border: 1px solid var(--primary-blue);
}

.btn-outline-primary:hover {
  background: var(--primary-blue);
  color: var(--white);
}

/* Leyenda Fija */
.map-legend-fixed {
  position: fixed;
  bottom: 2rem;
  left: 2rem;
  background: var(--white);
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
  padding: 1rem;
  z-index: 1000;
  min-width: 200px;
}

.legend-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.legend-items {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.legend-icon {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid var(--white);
  box-shadow: var(--shadow-sm);
}

.legend-icon.cultura {
  background: var(--category-cultura);
}

.legend-icon.naturaleza {
  background: var(--category-naturaleza);
}

.legend-icon.aventura {
  background: var(--category-aventura);
}

.legend-icon.gastronomia {
  background: var(--category-gastronomia);
}

.legend-icon.relax {
  background: var(--category-relax);
}

/* Animaciones */
@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(100%) translateY(-50%);
  }
  to {
    opacity: 1;
    transform: translateX(0) translateY(-50%);
  }
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-100%) translateY(-50%);
  }
  to {
    opacity: 1;
    transform: translateX(0) translateY(-50%);
  }
}

/* Responsive */
@media (max-width: 1200px) {
  .map-experience {
    grid-template-columns: 480px 1fr;
  }
  .results-list {
    grid-template-columns: 1fr;
  }
  .place-info-panel {
    right: 1rem;
    width: 300px;
  }
  
  .nearby-panel {
    left: 1rem;
    width: 300px;
  }
  
  .map-legend-fixed {
    left: 1rem;
    bottom: 1rem;
  }
}

@media (max-width: 768px) {
  .map-experience {
    grid-template-columns: 1fr;
  }
  .results-sidebar {
    order: 2;
    min-height: 360px;
  }
  .map-area {
    order: 1;
    min-height: 400px;
  }
  .results-list {
    grid-template-columns: 1fr;
  }
  .map-title {
    font-size: 2rem;
  }
  
  .map-description {
    font-size: 1rem;
  }
  
  .map-filters {
    flex-direction: column;
    gap: 1rem;
  }

  .filter-group,
  .search-filter-group,
  .filter-select,
  .map-search-control,
  .map-search-input {
    width: 100%;
  }
  
  .place-info-panel {
    position: fixed;
    top: auto;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    transform: none;
    border-radius: 16px 16px 0 0;
    max-height: 60vh;
  }
  
  .nearby-panel {
    position: fixed;
    top: auto;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    transform: none;
    border-radius: 16px 16px 0 0;
    max-height: 60vh;
  }
  
  .map-legend-fixed {
    position: static;
    margin: 1rem;
    width: auto;
  }
  
  .map-wrapper {
    min-height: 400px;
  }
}
</style>
