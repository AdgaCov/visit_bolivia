<template>
  <main class="events-page container py-4">
    <!-- Estado de carga -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando evento...</p>
    </div>
    
    <!-- Estado de error -->
    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-triangle"></i>
      <p>{{ error }}</p>
      <button @click="fetchEvent" class="retry-btn">Reintentar</button>
    </div>
    
    <!-- Contenido principal -->
    <div v-else-if="currentEvent">
      <EventsHero 
        :featured="{ 
          ...currentEvent, 
          regionImage: getMainImage(currentEvent)
        }" 
      />
      <EventsHeader 
        :title="currentEvent.name" 
        :subtitle="currentEvent.description" 
      />
      <EventsDetails :event="currentEvent" />

      <!-- <EventsList :events="[currentEvent]" /> -->
      <EventsMap :event="currentEvent" />
      <!-- <EventsRegion /> -->
      <EventsNearbyCarousel :excludeEventId="currentEvent.id" />
    </div>
    
    <!-- Estado vacío -->
    <div v-else class="empty-state">
      <i class="fas fa-calendar-times"></i>
      <p>Evento no encontrado</p>
    </div>
  </main>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, getCurrentInstance } from 'vue'
import { useRoute } from 'vue-router'
import EventsHero from '@/components/events/EventsHero.vue'
import EventsHeader from '@/components/events/EventsHeader.vue'
import EventsDetails from '@/components/events/EventsDetails.vue'
import EventsList from '@/components/events/EventsList.vue'
import EventsMap from '@/components/events/EventsMap.vue'
import EventsRegion from '@/components/events/EventsRegion.vue'
import EventsNearbyCarousel from '@/components/events/EventsNearbyCarousel.vue'
import { eventsService } from '@/services/eventsService'
import type { EventItem } from '@/types/events'

// Props para recibir el ID del evento desde la ruta
const props = defineProps<{
  id?: string
}>()

const route = useRoute()
const app = getCurrentInstance()
const currentEvent = ref<EventItem | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

// Función para obtener el ID del evento (desde props o route params)
const getEventId = () => {
  return props.id || route.params.id as string
}

// Función para cargar el evento específico
const getMainImage = (event: EventItem): string => {
  const buildImg = app?.appContext?.config?.globalProperties?.$buildImg
  const mainImage = event.images?.find((img: any) => img.is_main) || event.images?.[0]
  if (mainImage?.image_url) {
    return buildImg ? buildImg(mainImage.image_url) : mainImage.image_url
  }
  return 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT72hubnZuZFDGuaP7KhaWTjtgCpZ3DZwoZ5w&s'
}

const fetchEvent = async () => {
  const eventId = getEventId()
  
  if (!eventId) {
    error.value = 'ID de evento no proporcionado'
    return
  }
  
  loading.value = true
  error.value = null
  
  try {
    const response = await eventsService.getEventById(eventId)
    
    if (response.success && response.data) {
      currentEvent.value = response.data
    } else {
      throw new Error('Evento no encontrado')
    }
  } catch (err) {
    console.error('Error fetching event:', err)
    error.value = 'Error al cargar el evento'
    currentEvent.value = null
  } finally {
    loading.value = false
  }
}

// Cargar evento al montar el componente
onMounted(() => {
  fetchEvent()
})

// Observar cambios en el ID del evento
watch(() => getEventId(), () => {
  fetchEvent()
})
</script>

<style scoped>
.events-page h1 {
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
