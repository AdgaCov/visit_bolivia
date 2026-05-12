<template>
  <main class="gastronomy-page container py-4">
    <!-- Estado de carga -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando lugar...</p>
    </div>
    
    <!-- Estado de error -->
    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-triangle"></i>
      <p>{{ error }}</p>
      <button @click="fetchRestaurant" class="retry-btn">Reintentar</button>
    </div>
    
    <!-- Contenido principal -->
    <div v-else-if="currentRestaurant">
      <GastronomyHero 
        :featured="{ 
          ...currentRestaurant, 
          regionImage: getMainImage(currentRestaurant)
        }" 
      />
      <GastronomyHeader 
        :title="currentRestaurant.name" 
        :subtitle="currentRestaurant.description" 
        :location="currentRestaurant.location"
        :rating="currentRestaurant.rating"
        :badge="currentRestaurant.cuisine_type"
        :favourite-text="favouriteButtonText"
        :favorite-active="isFavorite"
        :favorite-loading="favoriteLoading"
        @toggle-favorite="handleFavoriteToggle"
      />
      <!-- Servicios clave - Solo para gastronomía, eventos y alojamientos -->
      <GastronomyAmenitiesSection 
        v-if="shouldShowAmenities" 
        :amenities="gAmenities" 
      />
      <GastronomyDetails :restaurant="currentRestaurant" />
      <!-- Menú destacado -->
      <GastronomyMenuSection :items="gMenu" :page-size="8" />
      <!-- <GastronomyMap :restaurant="currentRestaurant" /> -->
      <!-- Cómo llegar -->
      <GastronomyHowToArriveSection 
        :latitude="currentRestaurant.latitude"
        :longitude="currentRestaurant.longitude"
        :address="currentRestaurant.location?.address || null"
        :transport="gTransport"
        :points-of-interest="gPOIs"
        @user-location="handleUserLocation"
      />
      <!-- Reseñas -->
      <!-- <GastronomyReviewsSection :reviews="gReviews" :summary="gReviewSummary" /> -->
      <!-- Políticas - Solo para restaurantes, eventos y alojamientos -->
      <GastronomyPoliciesSection 
        v-if="shouldShowPolicies && hasLocationPolicies" 
        :location-id="currentRestaurant.id"
        :policies="gPolicies" 
        :payments="gPayments" 
        @policies-loaded="handlePoliciesLoaded"
      />
      <!-- Artículos relacionados -->
      <!-- <LocationArticlesSection :locationId="currentRestaurant.id" /> -->
      <GastronomyNearbyCarousel 
        :excludeRestaurantId="currentRestaurant.id" 
        :currentLocationType="currentRestaurant.type || 'restaurant'"
        :currentLocationId="currentRestaurant.id"
        :userLatitude="userLatitude"
        :userLongitude="userLongitude"
      />
      <!-- CTA pegajosa -->
      <GastronomyStickyCTA :title="currentRestaurant.name" :phone-or-whatsapp="currentRestaurant.whatsapp || null" />
    </div>
    
    <!-- Estado vacío -->
    <div v-else class="empty-state">
      <i class="fas fa-map-marker-alt"></i>
      <p>Lugar no encontrado</p>
    </div>
  </main>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed, getCurrentInstance } from 'vue'
import { useRoute } from 'vue-router'
import GastronomyHero from '@/components/gastronomy/GastronomyHero.vue'
import GastronomyHeader from '@/components/gastronomy/GastronomyHeader.vue'
import GastronomyDetails from '@/components/gastronomy/GastronomyDetails.vue'
import GastronomyMap from '@/components/gastronomy/GastronomyMap.vue'
import GastronomyNearbyCarousel from '@/components/gastronomy/GastronomyNearbyCarousel.vue'
import GastronomyAmenitiesSection from '@/components/gastronomy/GastronomyAmenitiesSection.vue'
import GastronomyMenuSection from '@/components/gastronomy/GastronomyMenuSection.vue'
import GastronomyReviewsSection from '@/components/gastronomy/GastronomyReviewsSection.vue'
import GastronomyPoliciesSection from '@/components/gastronomy/GastronomyPoliciesSection.vue'
import GastronomyHowToArriveSection from '@/components/gastronomy/GastronomyHowToArriveSection.vue'
import GastronomyStickyCTA from '@/components/gastronomy/GastronomyStickyCTA.vue'
import LocationArticlesSection from '@/components/gastronomy/LocationArticlesSection.vue'
import { gastronomyService } from '@/services/gastronomyService'
import { getLocationById, mapLocationToGastronomyLike } from '@/services/locations.service'
import favoritesService, { type FavoriteCard } from '@/services/favorites.service'
import localFavoritesService from '@/services/localFavorites.service'
import { useAuthStore } from '@/store/auth'

// Props para recibir el ID del restaurante desde la ruta
const props = defineProps<{
  id?: string
}>()

const route = useRoute()
const app = getCurrentInstance()
const currentRestaurant = ref<any | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const userLatitude = ref<number | undefined>(undefined)
const userLongitude = ref<number | undefined>(undefined)

const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.authenticated)
const favoriteLoading = ref(false)
const isFavorite = ref(false)
const favoriteId = ref<number | string | null>(null)
const favouriteButtonText = computed(() => isFavorite.value ? 'EN FAVORITOS' : 'AGREGAR A FAVORITOS')
const hasLocationPolicies = ref(true) // Por defecto true para mostrar durante carga inicial

// Función para obtener el ID del lugar (desde props o route params)
const getRestaurantId = () => {
  return props.id || route.params.id as string
}

// Función para obtener la imagen principal
const getMainImage = (restaurant: any): string => {
  const buildImg = app?.appContext?.config?.globalProperties?.$buildImg
  const mainImage = restaurant.images?.find((img: { is_main: any }) => img.is_main)
  if (mainImage) {
    return buildImg ? buildImg(mainImage.image_url) : mainImage.image_url
  }
  if (restaurant.images && restaurant.images.length > 0) {
    const url = restaurant.images[0].image_url
    return buildImg ? buildImg(url) : url
  }
  return 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&h=400&fit=crop'
}

// Función para convertir tipo de item a etiqueta legible
const getItemTypeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    'room': 'Habitación',
    'service': 'Servicio',
    'offer': 'Oferta',
    'food': 'Comida',
    'drink': 'Bebida'
  }

  return labels[type] || 'Servicio'
}

const buildFavoriteCardFromRestaurant = (restaurant: any): FavoriteCard => ({
  id: restaurant.id,
  type: 'gastronomy',
  title: restaurant.name,
  description: restaurant.description || '',
  image: getMainImage(restaurant),
  location: restaurant.location?.department?.name || restaurant.location?.name || restaurant.city || '',
  category: restaurant.cuisine_type || '',
  badge: restaurant.badge || restaurant.cuisine_type || '',
  link: `/location/${restaurant.id}`,
  locationId: restaurant.id,
  favoritesCount: restaurant.favorites_count || restaurant.favoritesCount || 0
})

const refreshFavoriteState = async () => {
  if (!currentRestaurant.value) {
    isFavorite.value = false
    favoriteId.value = null
    return
  }

  const restaurantId = currentRestaurant.value.id

  if (isAuthenticated.value) {
    try {
      const response = await favoritesService.getUserFavorites()
      if (response.success && response.data) {
        const match = response.data.find(fav => String(fav.location_id) === String(restaurantId))
        if (match) {
          isFavorite.value = true
          favoriteId.value = match.id
          return
        }
      }
      isFavorite.value = false
      favoriteId.value = null
    } catch (error) {
      console.warn('No se pudieron obtener favoritos del servidor, usando locales como fallback.')
      const localMatch = localFavoritesService.findByLocationId(restaurantId)
      isFavorite.value = !!localMatch
      favoriteId.value = localMatch?.id ?? null
    }
  } else {
    const localMatch = localFavoritesService.findByLocationId(restaurantId)
    isFavorite.value = !!localMatch
    favoriteId.value = localMatch?.id ?? null
  }
}

const handleFavoriteToggle = async () => {
  if (!currentRestaurant.value || favoriteLoading.value) return

  favoriteLoading.value = true
  const restaurant = currentRestaurant.value

  try {
    if (isFavorite.value) {
      if (isAuthenticated.value) {
        await favoritesService.removeFavorite(restaurant.id)
      } else if (favoriteId.value) {
        localFavoritesService.remove(favoriteId.value)
      }
      isFavorite.value = false
      favoriteId.value = null
    } else {
      if (isAuthenticated.value) {
        const response = await favoritesService.addFavorite(restaurant.id)
        if (response.success && response.data) {
          isFavorite.value = true
          favoriteId.value = response.data.id
        }
      } else {
        const updated = localFavoritesService.add(buildFavoriteCardFromRestaurant(restaurant))
        const stored = updated.find(item => String(item.locationId) === String(restaurant.id))
        isFavorite.value = true
        favoriteId.value = stored?.id ?? null
      }
    }
  } catch (error) {
    console.error('Error al actualizar favorito:', error)
  } finally {
    favoriteLoading.value = false
  }
}

// Función para cargar el lugar específico (restaurante, museo, ciudad, etc.)
const fetchRestaurant = async () => {
  const locationId = getRestaurantId()
  
  if (!locationId) {
    error.value = 'ID de lugar no proporcionado'
    return
  }
  
  loading.value = true
  error.value = null
  
  try {
    // Primero intentar con el servicio de gastronomía (para restaurantes)
    try {
      const response = await gastronomyService.getRestaurantById(locationId)
      if (response.success && response.data) {
        currentRestaurant.value = response.data
        return
      }
    } catch (gastroError) {
      console.log('No es un restaurante, intentando con servicio de ubicaciones...')
    }
    
    // Si no es un restaurante, usar el servicio de ubicaciones genérico
    const location = await getLocationById(locationId)
    if (location) {
      currentRestaurant.value = mapLocationToGastronomyLike(location)
    } else {
      throw new Error('Lugar no encontrado')
    }
  } catch (err) {
    console.error('Error fetching location:', err)
    error.value = 'Error al cargar el lugar'
    currentRestaurant.value = null
  } finally {
    loading.value = false
  }
}

// Cargar lugar al montar el componente
onMounted(() => {
  fetchRestaurant()
})

// Observar cambios en el ID del lugar
watch(() => getRestaurantId(), () => {
  fetchRestaurant()
})

watch([currentRestaurant, isAuthenticated], () => {
  refreshFavoriteState()
})

// Resetear estado de políticas cuando cambia el restaurante
watch(() => currentRestaurant.value?.id, () => {
  hasLocationPolicies.value = true // Resetear a true para mostrar durante carga
})

// Derivados para nuevas secciones (placeholder)
const gAmenities = computed(() => {
  return [
    { title: 'Servicios', items: ['Delivery', 'Para llevar', 'Reservas'] },
    { title: 'Ambiente', items: ['Familiar', 'Terraza', 'Música suave'] },
    { title: 'Dietas', items: ['Vegetariano', 'Sin gluten (consultar)'] }
  ]
})

const gMenu = computed(() => {
  // SOLO usar items de la base de datos - SIN fallback estático
  if (currentRestaurant.value?.items && Array.isArray(currentRestaurant.value.items)) {
    return currentRestaurant.value.items.map((item: { id: any; name: any; description: any; price: any; type: string; review: any; image_url: any }) => ({
      id: item.id,
      name: item.name,
      description: item.description,
      price: item.price,
      category: getItemTypeLabel(item.type),
      type: item.type,
      review: item.review,
      image_url: item.image_url
    }))
  }
  
  // Si no hay items en BD, retornar array vacío
  return []
})

const gTransport = computed(() => ['A 10 min del centro en taxi', 'Estación de bus a 3 cuadras'])
const gPOIs = computed(() => [{ name: 'Plaza principal', distanceKm: 0.8 }, { name: 'Museo', distanceKm: 1.5 }])

const gReviews = computed(() => [
  { id: 1, author: 'Carla', rating: 5, date: new Date().toISOString(), text: 'Ambiente agradable.' },
  { id: 2, author: 'Luis', rating: 4, date: new Date().toISOString(), text: 'Buen servicio, volveré.' }
])

const gReviewSummary = computed(() => {
  const list = gReviews.value
  const avg = list.length ? list.reduce((a, b) => a + b.rating, 0) / list.length : 0
  return { average: avg, count: list.length }
})

const gPolicies = computed(() => ({ reservations: 'Reservas recomendadas fines de semana', cancellation: 'Hasta 24h antes', hours: '12:00 - 23:00' }))
const gPayments = computed(() => ['Efectivo', 'Tarjeta de crédito', 'Transferencia'])

// Computed para determinar si se debe mostrar la sección de políticas
const shouldShowPolicies = computed(() => {
  if (!currentRestaurant.value) return false
  
  const allowedTypes = ['restaurant', 'event', 'accommodation']
  return allowedTypes.includes(currentRestaurant.value.type)
})

// Computed para determinar si se debe mostrar la sección de amenities
const shouldShowAmenities = computed(() => {
  if (!currentRestaurant.value) return false
  
  const allowedTypes = ['restaurant', 'event', 'accommodation']
  return allowedTypes.includes(currentRestaurant.value.type)
})

// Manejar cuando las políticas se cargan
const handlePoliciesLoaded = (hasPolicies: boolean) => {
  hasLocationPolicies.value = hasPolicies
}

const handleUserLocation = (payload: { latitude: number; longitude: number }) => {
  console.log('📍 GastronomyPage: handleUserLocation called with:', payload)
  userLatitude.value = payload.latitude
  userLongitude.value = payload.longitude
  console.log('📍 GastronomyPage: Updated userLatitude:', userLatitude.value, 'userLongitude:', userLongitude.value)
}
</script>

<style scoped>
.gastronomy-page h1 {
  font-weight: 500;
}

/* Estados de carga, error y vacío */
.loading-state,
.error-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  text-align: center;
  color: var(--text-secondary);
  min-height: 50vh;
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

.error-state i,
.empty-state i {
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
  .loading-state,
  .error-state,
  .empty-state {
    padding: 2rem 1rem;
    min-height: 40vh;
  }
  
  .spinner {
    width: 30px;
    height: 30px;
  }
  
  .error-state i,
  .empty-state i {
    font-size: 2rem;
  }
}
</style>
