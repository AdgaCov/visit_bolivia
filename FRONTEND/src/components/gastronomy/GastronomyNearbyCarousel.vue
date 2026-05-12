<template>
  <section class="gastronomy-nearby my-4">
    <h2 class="mb-3">{{ sectionTitle }}</h2>
    <div class="carousel">
      <!-- Estado de carga -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando...</p>
      </div>
      
      <!-- Estado de error -->
      <div v-else-if="error" class="error-state">
        <i class="fas fa-exclamation-triangle"></i>
        <p>{{ error }}</p>
        <button @click="fetchRestaurants" class="retry-btn">Reintentar</button>
      </div>
      
      <!-- Contenido principal -->
      <div v-else-if="restaurants.length > 0" class="restaurants-grid" ref="track" @mousedown="handleMouseDown" @scroll="updateScrollProgress" @touchstart="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd">
        <GastronomyCard
          v-for="restaurant in restaurants"
          :key="restaurant.id"
          :image="getMainImage(restaurant)"
          :title="restaurant.name"
          :location="restaurant.location"
          :rating="formatRating(restaurant.rating)"
          :description="restaurant.description"
          :badge="restaurant.cuisine_type"
          :to="{ name: 'GastronomyDetail', params: { id: restaurant.id } }"
        />
      </div>
      
      <!-- Estado vacío -->
      <div v-else class="empty-state">
        <i class="fas fa-utensils"></i>
        <p>No hay restaurantes disponibles en este momento</p>
      </div>
      
      <!-- Indicador de scroll -->
      <div v-if="restaurants.length > 0" class="scroll-indicator">
        <div class="scroll-track">
          <div class="scroll-progress" ref="scrollProgress"></div>
        </div>
        <div class="scroll-arrows">
          <button class="scroll-arrow scroll-left" @click="scroll(-1)">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button class="scroll-arrow scroll-right" @click="scroll(1)">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, nextTick, getCurrentInstance, watch } from 'vue'
import { gastronomyService } from '@/services/gastronomyService'
import apiClient from '@/services/api'
import API_ENDPOINTS from '@/services/endpoints'
import GastronomyCard from '@/components/home/GastronomyCard.vue'

export default {
  name: 'GastronomyNearbyCarousel',
  components: { GastronomyCard },
  props: {
    excludeRestaurantId: { type: [String, Number], default: null },
    currentLocationType: { type: String, default: 'restaurant' },
    currentLocationId: { type: [String, Number], default: null },
    userLatitude: { type: Number, default: undefined },
    userLongitude: { type: Number, default: undefined }
  },
  setup(props: { excludeRestaurantId: string | number | null; currentLocationType: string; currentLocationId: string | number | null; userLatitude: number | undefined; userLongitude: number | undefined }) {
    // Obtener $buildImg del contexto global
    const instance = getCurrentInstance()
    const $buildImg = instance?.appContext?.config?.globalProperties?.$buildImg
    // Variables para datos dinámicos
    const restaurants = ref<any[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)
    
    // Variables para drag scroll
    const isDragging = ref(false)
    const startX = ref(0)
    const scrollLeft = ref(0)
    const track = ref<HTMLDivElement | null>(null)
    const scrollProgress = ref<HTMLDivElement | null>(null)

    // Función para construir la URL de imagen (similar a InterestsSection)
    const buildImg = (url?: string): string => {
      if (!url) return ''
      return $buildImg ? $buildImg(url) : url
    }

    // Función para obtener la imagen principal (similar a InterestCard)
    const getMainImage = (restaurant: any): string => {
      const mainImage = restaurant.images?.find((img: { is_main: any }) => img.is_main)
      if (mainImage && mainImage.image_url) {
        return buildImg(mainImage.image_url)
      }
      if (restaurant.images && restaurant.images.length > 0 && restaurant.images[0].image_url) {
        return buildImg(restaurant.images[0].image_url)
      }
      // Fallback: si no hay imágenes, usar placeholder
      return 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&h=400&fit=crop'
    }

    // Formatear rating
    const formatRating = (rating: number): string => {
      if (!rating) return 'No disponible'
      return `${rating}/5 ⭐`
    }

    // Título dinámico según el tipo de lugar
    const sectionTitle = computed(() => {
      const typeLabels = {
        restaurant: 'Otros restaurantes cercanos',
        museum: 'Otros museos cercanos',
        attraction: 'Otras atracciones cercanas',
        city: 'Lugares destacados de la ciudad',
        accommodation: 'Otros alojamientos cercanos',
        event: 'Otros eventos cercanos'
      }
      return typeLabels[props.currentLocationType as keyof typeof typeLabels] || 'Otros lugares cercanos'
    })

    // Función para cargar lugares relacionados dinámicamente
    const fetchRestaurants = async () => {
      try {
        loading.value = true
        error.value = null
        console.log('🔄 GastronomyNearbyCarousel: fetchRestaurants called')
        console.log('📍 Props - userLatitude:', props.userLatitude, 'userLongitude:', props.userLongitude)
        console.log('📍 Props - currentLocationType:', props.currentLocationType, 'currentLocationId:', props.currentLocationId)
        
        let response
        let placesData = []
        const hasUserLocation = props.userLatitude != null && props.userLatitude !== undefined && 
                                props.userLongitude != null && props.userLongitude !== undefined
        const nearbyRadiusKm = 10
        
        console.log('📍 hasUserLocation:', hasUserLocation)

        if (hasUserLocation && props.userLatitude !== undefined && props.userLongitude !== undefined) {
          // Validar que las coordenadas sean números válidos
          const userLat = Number(props.userLatitude)
          const userLng = Number(props.userLongitude)
          
          if (isNaN(userLat) || isNaN(userLng) || userLat < -90 || userLat > 90 || userLng < -180 || userLng > 180) {
            console.error('❌ GastronomyNearbyCarousel: Invalid coordinates:', userLat, userLng)
            placesData = []
          } else {
            // Priorizar lugares cercanos si el usuario compartió su ubicación
            // Usar el mismo enfoque que InteractiveMapPage.vue
            console.log('📍 GastronomyNearbyCarousel: User location:', userLat, userLng)
            console.log('📍 GastronomyNearbyCarousel: Fetching nearby locations with radius:', nearbyRadiusKm, 'km')
            console.log('📍 GastronomyNearbyCarousel: Current location type:', props.currentLocationType)
            
            try {
              // Usar directamente apiClient.get como en InteractiveMapPage.vue
              const nearbyUrl = `${API_ENDPOINTS.LOCATIONS.BASE}/nearby?latitude=${userLat}&longitude=${userLng}&radius=${nearbyRadiusKm}`
              console.log('📍 GastronomyNearbyCarousel: Request URL:', nearbyUrl)
              
              const nearbyResponse = await apiClient.get<{ success: boolean; data: any[] }>(nearbyUrl)
              
              console.log('📍 GastronomyNearbyCarousel: Nearby API response:', nearbyResponse)
              
              // Verificar que la respuesta tenga el formato correcto (igual que InteractiveMapPage.vue)
              if (nearbyResponse && nearbyResponse.success && Array.isArray(nearbyResponse.data)) {
                placesData = nearbyResponse.data
                console.log('📍 GastronomyNearbyCarousel: Nearby locations found (all types):', placesData.length)
                
                if (placesData.length > 0) {
                  // Mostrar las distancias para debugging
                  placesData.forEach((place: any) => {
                    const distance = place.distance != null ? parseFloat(place.distance) : null
                    const distanceStr = distance != null ? distance.toFixed(2) : 'N/A'
                    console.log(`  - ${place.name} (${place.type}): ${distanceStr} km`, {
                      lat: place.latitude,
                      lng: place.longitude,
                      distance
                    })
                  })
                  
                  // Filtrar por distancia en el frontend (por si el backend no lo hace correctamente)
                  // Solo mantener lugares dentro del radio especificado
                  const maxDistanceKm = nearbyRadiusKm
                  placesData = placesData.filter((place: any) => {
                    const distance = place.distance != null ? parseFloat(place.distance) : null
                    if (distance === null || distance === undefined) {
                      console.warn(`⚠️ Place ${place.name} has no distance, excluding`)
                      return false
                    }
                    const isWithinRadius = distance <= maxDistanceKm
                    if (!isWithinRadius) {
                      console.log(`  ✗ ${place.name} excluded: ${distance.toFixed(2)} km > ${maxDistanceKm} km`)
                    }
                    return isWithinRadius
                  })
                  
                  console.log('📍 GastronomyNearbyCarousel: After distance filter:', placesData.length)
                }
                
                // Filtrar por tipo de lugar (solo mostrar el mismo tipo que el lugar actual)
                if (placesData.length > 0 && props.currentLocationType) {
                  const beforeFilter = placesData.length
                  placesData = placesData.filter((place: any) => 
                    place.type === props.currentLocationType
                  )
                  console.log('📍 GastronomyNearbyCarousel: Filtered by type', props.currentLocationType, `: ${beforeFilter} -> ${placesData.length}`)
                  
                  if (placesData.length > 0) {
                    placesData.forEach((place: any) => {
                      const distance = place.distance != null ? parseFloat(place.distance).toFixed(2) : 'N/A'
                      console.log(`  ✓ ${place.name}: ${distance} km`)
                    })
                  }
                }
              } else {
                console.warn('⚠️ GastronomyNearbyCarousel: Invalid response format from nearby API', nearbyResponse)
                placesData = []
              }
            } catch (nearbyError) {
              console.error('❌ GastronomyNearbyCarousel: Error fetching nearby locations:', nearbyError)
              // Si falla, continuar con el fallback de mostrar todos los lugares por tipo
              placesData = []
            }
          }
        }
        
        // Si no hay ubicación o la respuesta vino vacía después de filtrar, usar el listado completo por tipo
        if (!placesData.length) {
          console.log('GastronomyNearbyCarousel: No nearby locations found, falling back to all locations by type')
          if (props.currentLocationType === 'restaurant') {
            response = await gastronomyService.getAllRestaurants()
            placesData = (response as any).data || []
          } else {
            response = await apiClient.get(API_ENDPOINTS.LOCATIONS.BY_TYPE(props.currentLocationType))
            placesData = (response as any).data || []
          }
        }
        
        console.log('GastronomyNearbyCarousel: API Response:', response)
        console.log('GastronomyNearbyCarousel: Places data (before exclude):', placesData.length)
        
        // Excluir el lugar actual si se proporciona
        const excludeId = props.currentLocationId != null ? String(props.currentLocationId) : 
                         (props.excludeRestaurantId != null ? String(props.excludeRestaurantId) : null)
        
        let filteredPlaces = placesData
        
        if (excludeId !== null) {
          filteredPlaces = placesData.filter((place: any) => 
            place.id.toString() !== excludeId
          )
          console.log('GastronomyNearbyCarousel: After excluding current location:', filteredPlaces.length)
        }
        
        // Limitar a los primeros 8 lugares para el carrusel
        filteredPlaces = filteredPlaces.slice(0, 8)
        
        console.log('GastronomyNearbyCarousel: Filtered places:', filteredPlaces)
        restaurants.value = filteredPlaces
      } catch (err) {
        console.error('GastronomyNearbyCarousel: Error fetching places:', err)
        error.value = 'Error al cargar los lugares cercanos'
        restaurants.value = []
      } finally {
        loading.value = false
      }
    }

    const scroll = (direction: number) => {
      const el = track.value as HTMLDivElement | null
      if (!el) return
      const firstCard = el.querySelector('.gastronomy-card') as HTMLElement | null
      const computedStyle = window.getComputedStyle(el)
      const gapPx = parseFloat(computedStyle.getPropertyValue('column-gap') || computedStyle.getPropertyValue('gap') || '0') || 0
      const cardRectWidth = firstCard ? firstCard.offsetWidth : el.clientWidth * 0.8
      const step = cardRectWidth + gapPx
      const current = el.scrollLeft
      const nearestIndex = Math.round(current / step)
      const nextIndex = Math.max(0, Math.min(nearestIndex + direction, Math.ceil(el.scrollWidth / step)))
      const rawTarget = nextIndex * step
      const maxLeft = Math.max(0, el.scrollWidth - el.clientWidth)
      const target = Math.max(0, Math.min(rawTarget, maxLeft))
      if (typeof el.scrollTo === 'function') {
        el.scrollTo({ left: target, behavior: 'smooth' })
      } else {
        el.scrollLeft = target
      }
      
      // Actualizar progreso después del scroll
      setTimeout(() => {
        updateScrollProgress()
      }, 350)
    }

    const updateScrollProgress = () => {
      const el = track.value as HTMLDivElement | null
      if (!el) return
      
      const scrollLeft = el.scrollLeft
      const scrollWidth = el.scrollWidth - el.clientWidth
      const progress = scrollWidth > 0 ? (scrollLeft / scrollWidth) * 100 : 0
      
      const progressBar = scrollProgress.value as HTMLDivElement | null
      if (progressBar) {
        progressBar.style.width = `${progress}%`
      }
    }

    // Funciones para drag scroll
    const handleMouseDown = (e: MouseEvent) => {
      if (!track.value) return
      
      isDragging.value = true
      track.value.classList.add('dragging')
      
      startX.value = e.pageX - track.value.offsetLeft
      scrollLeft.value = track.value.scrollLeft
      
      e.preventDefault()
      e.stopPropagation()
    }

    const handleMouseMove = (e: MouseEvent) => {
      if (!isDragging.value || !track.value) return
      
      e.preventDefault()
      
      const x = e.pageX - track.value.offsetLeft
      const walk = (x - startX.value) * 1.5
      track.value.scrollLeft = scrollLeft.value - walk
      
      updateScrollProgress()
    }

    const handleMouseUp = () => {
      if (!isDragging.value) return
      
      isDragging.value = false
      
      if (track.value) {
        track.value.classList.remove('dragging')
      }
    }

    // Funciones para touch events (móviles)
    const handleTouchStart = (e: TouchEvent) => {
      if (!track.value) return
      
      isDragging.value = true
      track.value.classList.add('dragging')
      
      startX.value = e.touches[0].pageX - track.value.offsetLeft
      scrollLeft.value = track.value.scrollLeft
      
      // No prevenir el evento por defecto aquí para permitir clicks
    }

    const handleTouchMove = (e: TouchEvent) => {
      if (!isDragging.value || !track.value) return
      
      const x = e.touches[0].pageX - track.value.offsetLeft
      const walk = (x - startX.value) * 1.5
      
      // Solo prevenir el evento si hay movimiento significativo
      if (Math.abs(walk) > 10) {
        e.preventDefault()
      track.value.scrollLeft = scrollLeft.value - walk
      updateScrollProgress()
      }
    }

    const handleTouchEnd = () => {
      if (!isDragging.value) return
      
      isDragging.value = false
      
      if (track.value) {
        track.value.classList.remove('dragging')
      }
      
      // Resetear dragMoved después de un pequeño delay para permitir clicks
      setTimeout(() => {
        // Aquí podríamos agregar lógica adicional si fuera necesario
      }, 100)
    }

    // Watch individual para cada prop que puede cambiar
    watch(
      () => props.userLatitude,
      (newLat, oldLat) => {
        console.log('📍 GastronomyNearbyCarousel: userLatitude changed:', oldLat, '->', newLat)
        if (newLat != null && newLat !== undefined && props.userLongitude != null && props.userLongitude !== undefined) {
          console.log('📍 GastronomyNearbyCarousel: Triggering fetchRestaurants due to user location change')
          fetchRestaurants()
        }
      }
    )
    
    watch(
      () => props.userLongitude,
      (newLng, oldLng) => {
        console.log('📍 GastronomyNearbyCarousel: userLongitude changed:', oldLng, '->', newLng)
        if (newLng != null && newLng !== undefined && props.userLatitude != null && props.userLatitude !== undefined) {
          console.log('📍 GastronomyNearbyCarousel: Triggering fetchRestaurants due to user location change')
          fetchRestaurants()
        }
      }
    )
    
    watch(
      () => [props.currentLocationType, props.currentLocationId],
      () => {
        console.log('📍 GastronomyNearbyCarousel: Location type or ID changed, fetching restaurants')
        fetchRestaurants()
      }
    )

    onMounted(() => {
      // Cargar restaurantes dinámicamente
      fetchRestaurants()
      
      // Inicializar progreso del scroll
      nextTick(() => {
        updateScrollProgress()
        
        // Event listeners globales para mouse move y mouse up
        document.addEventListener('mousemove', handleMouseMove)
        document.addEventListener('mouseup', handleMouseUp)
      })
    })

    onBeforeUnmount(() => {
      // Limpiar event listeners globales
      document.removeEventListener('mousemove', handleMouseMove)
      document.removeEventListener('mouseup', handleMouseUp)
    })

    return {
      restaurants,
      loading,
      error,
      fetchRestaurants,
      getMainImage,
      formatRating,
      sectionTitle,
      track,
      scrollProgress,
      scroll,
      updateScrollProgress,
      handleMouseDown,
      handleMouseMove,
      handleMouseUp,
      handleTouchStart,
      handleTouchMove,
      handleTouchEnd
    }
  }
}
</script>

<style scoped>
.gastronomy-nearby h2 { 
  font-weight: 300; 
  letter-spacing: -0.02em; 
}

.carousel { 
  position: relative; 
  overflow: visible; 
}

/* Igualar la grilla y comportamiento de EventsSection */
.restaurants-grid {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: minmax(280px, 320px);
  gap: 1.5rem;
  overflow-x: auto;
  padding-bottom: 2rem;
  scroll-snap-type: x mandatory;
  scrollbar-width: none;
  -ms-overflow-style: none;
  -webkit-overflow-scrolling: touch;
  cursor: grab;
  user-select: none;
  transition: cursor 0.2s ease;
}

.restaurants-grid::-webkit-scrollbar { 
  display: none; 
}

.restaurants-grid > * { 
  scroll-snap-align: start; 
}

.restaurants-grid:active { 
  cursor: grabbing; 
}

.restaurants-grid.dragging { 
  cursor: grabbing; 
  user-select: none; 
  scroll-snap-type: none; 
}

.restaurants-grid.dragging > * { 
  transform: scale(0.98); 
  transition: transform 0.1s ease; 
}

/* Scroll indicator */
.scroll-indicator { 
  display: flex; 
  align-items: center; 
  gap: 1rem; 
  margin-top: 1rem; 
}

.scroll-track { 
  flex: 1; 
  height: 2px; 
  background: var(--border-light); 
  border-radius: 1px; 
  position: relative; 
  overflow: hidden; 
}

.scroll-progress { 
  position: absolute; 
  top: 0; 
  left: 0; 
  height: 100%; 
  background: var(--primary-blue); 
  border-radius: 1px; 
  width: 25%; 
  transition: width 0.3s ease; 
}

.scroll-arrows { 
  display: flex; 
  gap: 0.5rem; 
}

.scroll-arrow { 
  width: 40px; 
  height: 40px; 
  border: 1px solid var(--border-light); 
  background: var(--white); 
  border-radius: 50%; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  cursor: pointer; 
  transition: all 0.2s ease; 
  color: var(--text-secondary); 
  font-size: 0.875rem; 
}

.scroll-arrow:hover { 
  background: var(--primary-blue); 
  color: var(--white); 
  border-color: var(--primary-blue); 
  transform: scale(1.05); 
}

.scroll-arrow:active { 
  transform: scale(0.95); 
}

/* Estados de carga, error y vacío */
.loading-state, .error-state, .empty-state { 
  display: flex; 
  flex-direction: column; 
  align-items: center; 
  justify-content: center; 
  padding: 3rem 1rem; 
  text-align: center; 
  color: var(--text-secondary); 
  min-height: 300px; 
}

.spinner { 
  width: 40px; 
  height: 40px; 
  border: 3px solid var(--border-light); 
  border-top: 3px solid var(--primary-blue); 
  border-radius: 50%; 
  animation: spin 1s linear infinite; 
  margin-bottom: 1rem; 
}

@keyframes spin { 
  0% { transform: rotate(0deg); } 
  100% { transform: rotate(360deg); } 
}

.error-state i, .empty-state i { 
  font-size: 3rem; 
  color: var(--text-muted); 
  margin-bottom: 1rem; 
}

.error-state i { 
  color: var(--error-red); 
}

.retry-btn { 
  background: var(--primary-blue); 
  color: var(--white); 
  border: none; 
  padding: 0.75rem 1.5rem; 
  border-radius: 8px; 
  font-weight: 500; 
  cursor: pointer; 
  transition: all 0.3s ease; 
  margin-top: 1rem; 
}

.retry-btn:hover { 
  background: var(--primary-blue-dark); 
  transform: translateY(-1px); 
}

/* Responsive */
@media (max-width: 768px) {
  .scroll-arrow { 
    width: 36px; 
    height: 36px; 
    font-size: 0.75rem; 
  }
  
  .loading-state, .error-state, .empty-state { 
    padding: 2rem 1rem; 
    min-height: 250px; 
  }
  
  .spinner { 
    width: 30px; 
    height: 30px; 
  }
  
  .error-state i, .empty-state i { 
    font-size: 2rem; 
  }
}
</style>