<template>
  <main class="accommodation-page container py-4">
    <!-- Estado de carga -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando hotel...</p>
    </div>
    
    <!-- Estado de error -->
    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-triangle"></i>
      <p>{{ error }}</p>
      <button @click="fetchAccommodation" class="retry-btn">Reintentar</button>
    </div>
    
    <!-- Contenido principal -->
    <div v-else-if="currentAccommodation">
      <AccommodationHero 
        :featured="{ 
          ...currentAccommodation, 
          regionImage: getMainImage(currentAccommodation)
        }" 
      />
      <AccommodationHeader 
        :title="currentAccommodation.title" 
        :subtitle="currentAccommodation.description" 
        :price="formatPrice(currentAccommodation.price_per_night)"
        :location="currentAccommodation.location"
        :badge="currentAccommodation.badge"
      />
      <!-- Servicios clave -->
      <AmenitiesSection :amenities="computedAmenities" />
      <AccommodationDetails :accommodation="currentAccommodation" />
      <!-- Habitaciones y tarifas -->
      <RoomsRatesSection 
        :rooms="computedRooms"
        :hotel-name="currentAccommodation.title"
        :phone-or-whatsapp="currentAccommodation.whatsapp || null"
        :page-size="6"
      />
      <AccommodationMap :accommodation="currentAccommodation" />
      <!-- Cómo llegar (transporte y puntos de interés) -->
      <HowToArriveSection 
        :latitude="currentAccommodation.latitude"
        :longitude="currentAccommodation.longitude"
        :address="currentAccommodation.place?.address || null"
        :transport="computedTransport"
        :points-of-interest="computedPOIs"
      />
      <!-- Reseñas -->
      <ReviewsSection :reviews="computedReviews" :summary="computedReviewSummary" />
      <!-- Políticas -->
      <PoliciesSection :policies="computedPolicies" :payments="computedPayments" />
      <AccommodationNearbyCarousel :excludeAccommodationId="currentAccommodation.id" />
      <!-- CTA pegajosa -->
      <StickyCTA 
        :title="currentAccommodation.title"
        :price="formatPrice(currentAccommodation.price_per_night)"
        :phone-or-whatsapp="currentAccommodation.whatsapp || null"
      />
    </div>
    
    <!-- Estado vacío -->
    <div v-else class="empty-state">
      <i class="fas fa-bed"></i>
      <p>Hotel no encontrado</p>
    </div>
  </main>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed, getCurrentInstance } from 'vue'
import { useRoute } from 'vue-router'
import AccommodationHero from '@/components/accommodation/AccommodationHero.vue'
import AccommodationHeader from '@/components/accommodation/AccommodationHeader.vue'
import AccommodationDetails from '@/components/accommodation/AccommodationDetails.vue'
import AccommodationMap from '@/components/accommodation/AccommodationMap.vue'
import AccommodationNearbyCarousel from '@/components/accommodation/AccommodationNearbyCarousel.vue'
import AmenitiesSection from '@/components/accommodation/AmenitiesSection.vue'
import RoomsRatesSection from '@/components/accommodation/RoomsRatesSection.vue'
import ReviewsSection from '@/components/accommodation/ReviewsSection.vue'
import PoliciesSection from '@/components/accommodation/PoliciesSection.vue'
import HowToArriveSection from '@/components/accommodation/HowToArriveSection.vue'
import StickyCTA from '@/components/accommodation/StickyCTA.vue'
import { fetchAccommodations } from '@/services/accommodations.service'
import type { AccommodationApi } from '@/types'

// Props para recibir el ID del hotel desde la ruta
const props = defineProps<{
  id?: string
}>()

const route = useRoute()
const app = getCurrentInstance()
const currentAccommodation = ref<AccommodationApi | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

// Función para obtener el ID del hotel (desde props o route params)
const getAccommodationId = () => {
  return props.id || route.params.id as string
}

// Función para obtener la imagen principal
const getMainImage = (accommodation: AccommodationApi): string => {
  const buildImg = app?.appContext?.config?.globalProperties?.$buildImg
  const mainImage = accommodation.images?.find(img => (img as any).is_main)
  if (mainImage) {
    return buildImg ? buildImg((mainImage as any).image_url) : (mainImage as any).image_url
  }
  if (accommodation.images && accommodation.images.length > 0) {
    const url = (accommodation.images[0] as any).image_url
    return buildImg ? buildImg(url) : url
  }
  return 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=600&h=400&fit=crop'
}

// Normalizar y formatear el precio
const formatPrice = (price: string): string => {
  const numPrice = Math.abs(parseFloat(price))
  if (isNaN(numPrice)) return 'Consultar precio'
  return `$${numPrice.toFixed(0)}/noche`
}

// Datos derivados para nuevas secciones (placeholder a partir de datos disponibles)
const computedAmenities = computed(() => {
  if (!currentAccommodation.value) return []
  const base = [
    { title: 'Generales', items: ['WiFi', 'Desayuno incluido', 'Recepción 24h'] },
    { title: 'Habitación', items: ['Baño privado', 'TV', 'Ropa de cama'] },
    { title: 'Seguridad', items: ['Extintor', 'Detectores de humo'] }
  ]
  return base
})

const computedRooms = computed(() => {
  if (!currentAccommodation.value) return []
  // Si la API no provee rooms, construimos 8 ejemplos a partir del precio base
  const base = Math.max(0, Math.abs(parseFloat(currentAccommodation.value.price_per_night || '0')))
  const img = getMainImage(currentAccommodation.value)
  const capacities = [2, 2, 3, 4, 2, 3, 4, 5]
  const names = ['Estándar', 'Doble', 'Triple', 'Familiar', 'Superior', 'Deluxe', 'Suite Junior', 'Suite Master']
  const beds = ['1 cama doble', '2 camas individuales', '1 doble + 1 individual', '2 dobles', '1 queen', '1 king', '1 king + sofá cama', '2 queen']
  return names.map((n, idx) => ({
    id: `${currentAccommodation.value!.id}-r${idx + 1}`,
    name: `Habitación ${n}`,
    capacity: capacities[idx] || 2,
    beds: beds[idx] || '1 cama',
    amenities: ['WiFi', 'Baño privado', 'TV'],
    photos: [img],
    pricePerNight: String(base + idx * 15)
  }))
})

const computedTransport = computed(() => {
  return ['A 15 min del centro en taxi', 'Línea de bus 3 a 2 calles']
})

const computedPOIs = computed(() => {
  if (!currentAccommodation.value?.place?.name) return []
  return [
    { name: `Plaza principal de ${currentAccommodation.value.place.name}`, distanceKm: 1.2 },
    { name: 'Terminal de buses', distanceKm: 2.8 }
  ]
})

const computedReviews = computed(() => {
  return [
    { id: 1, author: 'María', rating: 5, date: new Date().toISOString(), text: 'Excelente atención y limpio.' },
    { id: 2, author: 'Jorge', rating: 4, date: new Date().toISOString(), text: 'Buena ubicación, volvería.' }
  ]
})

const computedReviewSummary = computed(() => {
  const list = computedReviews.value
  if (!list.length) return { average: 0, count: 0 }
  const avg = list.reduce((a, b) => a + b.rating, 0) / list.length
  return { average: avg, count: list.length }
})

const computedPolicies = computed(() => {
  return { checkIn: '14:00', checkOut: '11:00', cancellation: 'Cancelación gratuita 48h antes', children: 'Se permiten niños', pets: 'Consultar' }
})

const computedPayments = computed(() => {
  return ['Efectivo', 'Tarjeta de crédito', 'Transferencia']
})

// Función para cargar el hotel específico
const fetchAccommodation = async () => {
  const accommodationId = getAccommodationId()
  
  if (!accommodationId) {
    error.value = 'ID de hotel no proporcionado'
    return
  }
  
  loading.value = true
  error.value = null
  
  try {
    const accommodations = await fetchAccommodations()
    const accommodation = accommodations.find(acc => acc.id.toString() === accommodationId)
    
    if (accommodation) {
      currentAccommodation.value = accommodation
    } else {
      throw new Error('Hotel no encontrado')
    }
  } catch (err) {
    console.error('Error fetching accommodation:', err)
    error.value = 'Error al cargar el hotel'
    currentAccommodation.value = null
  } finally {
    loading.value = false
  }
}

// Cargar hotel al montar el componente
onMounted(() => {
  fetchAccommodation()
})

// Observar cambios en el ID del hotel
watch(() => getAccommodationId(), () => {
  fetchAccommodation()
})
</script>

<style scoped>
.accommodation-page h1 {
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
