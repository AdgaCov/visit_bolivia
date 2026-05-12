<template>
  <section class="events-map my-4">
    <div class="card p-3">
      <h3 class="mb-3">Ubicación del Evento</h3>
      <div v-if="eventLocation" class="map-container">
        <SimpleMap 
          :center="eventCoordinates"
          :zoom="eventZoom"
          :lugares="eventMarkers"
          height="300px"
        />
        <div class="location-info mt-3">
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-map-marker-alt text-primary me-2"></i>
            <strong>{{ eventLocation.name }}</strong>
          </div>
          <p class="text-muted mb-1">{{ eventLocation.address }}</p>
          <p class="text-muted mb-0">
            <i class="fas fa-clock me-1"></i>
            {{ eventLocation.opening_hours }}
          </p>
        </div>
      </div>
      <div v-else class="no-location">
        <i class="fas fa-map-marked-alt text-muted mb-2"></i>
        <p class="text-muted">Ubicación no disponible</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import SimpleMap from '@/components/SimpleMap.vue'
import type { EventItem } from '@/types/events'

// Props para recibir el evento
const props = defineProps<{
  event?: EventItem | null
}>()

// Computed para obtener la información de ubicación (con fallbacks)
const eventLocation = computed(() => {
  if (!props.event) return null

  const place = props.event.place || null
  const name = place?.name || props.event.name || props.event.department?.name || null
  const address = place?.address || props.event.address || null
  const openingHours = place?.opening_hours || props.event.opening_hours || null
  const contact = place?.contact_info || props.event.contact_info || null

  if (!name && !address) return null
  
  return {
    name: name || 'Ubicación no disponible',
    address: address || 'Dirección no disponible',
    opening_hours: openingHours,
    contact
  }
})

// Computed para las coordenadas del evento
const eventCoordinates = computed(() => {
  if (!props.event?.latitude || !props.event?.longitude) {
    return [-16.2902, -63.5887] // Coordenadas por defecto de Bolivia
  }
  
  return [
    parseFloat(props.event.latitude),
    parseFloat(props.event.longitude)
  ]
})

// Computed para los marcadores del mapa
const eventMarkers = computed(() => {
  if (!props.event) return []
  
  return [{
    id: `event-${props.event.id}`,
    nombre: props.event.name,
    coordenadas: eventCoordinates.value,
    descripcion: props.event.description,
    region: props.event.place?.name || 'Bolivia',
    tipo: 'evento',
    destacado: true
  }]
})

// Computed para el zoom dinámico
const eventZoom = computed(() => {
  const placeName = (props.event?.place?.name || props.event?.department?.name || '').toLowerCase()
  if (!placeName) return 10
  
  // Para lugares remotos como el Salar de Uyuni, usar zoom más bajo
  if (placeName.includes('salar') || placeName.includes('uyuni')) {
    return 8
  }
  
  // Para lugares en ciudades, usar zoom medio
  if (placeName.includes('oruro') || placeName.includes('la paz') || 
      placeName.includes('cochabamba') || placeName.includes('santa cruz')) {
    return 12
  }
  
  // Para otros lugares, zoom por defecto
  return 10
})
</script>

<style scoped>
.map-container {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}

.location-info {
  background: var(--light-gray);
  padding: 1rem;
  border-radius: 8px;
  border-left: 4px solid var(--primary-blue);
}

.no-location {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
  background: var(--light-gray);
  border-radius: 8px;
}

.no-location i {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .location-info {
    padding: 0.75rem;
  }
  
  .no-location {
    padding: 1.5rem;
  }
  
  .no-location i {
    font-size: 1.5rem;
  }
}
</style>



