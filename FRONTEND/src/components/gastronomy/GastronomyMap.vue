<template>
  <section class="gastronomy-map my-4">
    <div class="card p-3">
      <h3 class="mb-3">Ubicación del Restaurante</h3>
      <div v-if="restaurantLocation" class="map-container">
        <SimpleMap 
          :center="restaurantCoordinates"
          :zoom="restaurantZoom"
          :lugares="restaurantMarkers"
          height="300px"
        />
        <div class="location-info mt-3">
          <div class="d-flex align-items-center mb-2">
            <i class="fas fa-map-marker-alt text-primary me-2"></i>
            <strong>{{ restaurantLocation.name }}</strong>
          </div>
          <p class="text-muted mb-1">{{ restaurantLocation.address }}</p>
          <p class="text-muted mb-0">
            <i class="fas fa-clock me-1"></i>
            {{ restaurantLocation.opening_hours }}
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

// Props para recibir el restaurante
const props = defineProps<{
  restaurant?: any | null
}>()

// Computed para obtener la información de ubicación
const restaurantLocation = computed(() => {
  if (!props.restaurant?.place) return null
  
  return {
    name: props.restaurant.place.name,
    address: props.restaurant.place.address,
    opening_hours: props.restaurant.place.opening_hours,
    contact: props.restaurant.place.contact_info
  }
})

// Computed para las coordenadas del restaurante
const restaurantCoordinates = computed(() => {
  if (!props.restaurant?.latitude || !props.restaurant?.longitude) {
    return [-16.2902, -63.5887] // Coordenadas por defecto de Bolivia
  }
  
  return [
    parseFloat(props.restaurant.latitude),
    parseFloat(props.restaurant.longitude)
  ]
})

// Computed para los marcadores del mapa
const restaurantMarkers = computed(() => {
  if (!props.restaurant) return []
  
  return [{
    id: `restaurant-${props.restaurant.id}`,
    nombre: props.restaurant.name,
    coordenadas: restaurantCoordinates.value,
    descripcion: props.restaurant.description,
    region: props.restaurant.place?.name || 'Bolivia',
    tipo: 'restaurante',
    destacado: true
  }]
})

// Computed para el zoom dinámico
const restaurantZoom = computed(() => {
  if (!props.restaurant?.place?.name) return 10
  
  const placeName = props.restaurant.place.name.toLowerCase()
  
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