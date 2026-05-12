<template>
  <section class="events-details my-4">
    <div class="row g-4">
      <div class="col-12 col-lg-7">
        <h2 class="visually-hidden">Descripción del evento</h2>
        <p class="lead">{{ eventDescription }}</p>
        <div class="event-info">
          <div class="info-item mb-3">
            <h4 class="h6 mb-2">
              <i class="fas fa-info-circle me-2"></i>Información del Evento
            </h4>
            <p class="mb-2">
              <strong>Tipo:</strong> {{ eventType }}
            </p>
            <p class="mb-2">
              <strong>Precio:</strong> {{ eventPrice }}
            </p>
            <p class="mb-0">
              <strong>Recurrente:</strong> {{ isRecurring }}
            </p>
          </div>
          
          <div class="info-item mb-3">
            <h4 class="h6 mb-2">
              <i class="fas fa-map-marker-alt me-2"></i>Ubicación
            </h4>
            <p class="mb-2">
              <strong>Lugar:</strong> {{ eventLocation }}
            </p>
            <p class="mb-2">
              <strong>Dirección:</strong> {{ eventAddress }}
            </p>
            <p class="mb-0">
              <strong>Horarios:</strong> {{ eventHours }}
            </p>
          </div>
          
          <div class="info-item">
            <h4 class="h6 mb-2">
              <i class="fas fa-phone me-2"></i>Contacto
            </h4>
            <p class="mb-0">
              <strong>Teléfono:</strong> {{ eventContact }}
            </p>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-5">
        <div class="card p-4 mb-3" style="background: var(--bg-secondary);">
          <h3 class="h5 mb-3">
            <i class="fas fa-calendar-alt me-2"></i>Fechas del Evento
          </h3>
          <div class="date-info">
            <p class="mb-2">
              <!-- <strong>Inicio:</strong><br> -->
              <span class="text-primary">{{ startDate }}</span> - <span class="text-primary">{{ endDate }}</span>
            </p>
            <p class="mb-0">
              <!-- <strong>Fin:</strong><br> -->
              <!-- <span class="text-primary">{{ endDate }}</span> -->
            </p>
          </div>
        </div>

        <div class="card p-4 mb-3">
          <h3 class="h5 mb-3">
            <i class="fas fa-map-marker-alt me-2"></i>Ubicación
          </h3>
          <p class="mb-2">
            <strong>{{ eventLocation }}</strong>
          </p>
          <p class="mb-0 text-muted">
            <i class="fas fa-map-pin me-1"></i>{{ eventAddress }}
          </p>
        </div>

        <div class="card p-4">
          <h3 class="h5 mb-3">
            <i class="fas fa-info-circle me-2"></i>Información Adicional
          </h3>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-tag me-2"></i>
            <span><strong>Tipo:</strong> {{ eventType }}</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-dollar-sign me-2"></i>
            <span><strong>Precio:</strong> {{ eventPrice }}</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-phone me-2"></i>
            <span><strong>Contacto:</strong> {{ eventContact }}</span>
          </div>
          <div class="d-flex align-items-center">
            <i class="fas fa-clock me-2"></i>
            <span><strong>Horarios:</strong> {{ eventHours }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { EventItem } from '@/types/events'

// Props para recibir el evento
const props = defineProps<{
  event: EventItem
}>()

// Computed properties para mostrar los datos reales del evento
const eventDescription = computed(() => {
  return props.event?.description || 'Descripción no disponible'
})

const eventType = computed(() => {
  const type = props.event?.event_type || 'evento'
  return type.charAt(0).toUpperCase() + type.slice(1)
})

const eventPrice = computed(() => {
  const price = props.event?.price
  if (!price || price === '0.00') {
    return 'Gratuito'
  }
  return `$${price} BOB`
})

const isRecurring = computed(() => {
  return props.event?.is_recurring ? 'Sí' : 'No'
})

const eventLocation = computed(() => {
  return (
    props.event?.place?.name ||
    props.event?.address ||
    props.event?.department?.name ||
    'Ubicación no disponible'
  )
})

const eventAddress = computed(() => {
  return (
    props.event?.place?.address ||
    props.event?.address ||
    'Dirección no disponible'
  )
})

const eventHours = computed(() => {
  return props.event?.place?.opening_hours || 'Horarios no disponibles'
})

const eventContact = computed(() => {
  return props.event?.place?.contact_info || 'Contacto no disponible'
})

const startDate = computed(() => {
  if (!props.event?.start_date) return 'Fecha no disponible'
  
  const date = new Date(props.event.start_date)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})

const endDate = computed(() => {
  if (!props.event?.end_date) return 'Fecha no disponible'
  
  const date = new Date(props.event.end_date)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})
</script>

<style scoped>
/* Estilo minimalista usando variables CSS del sistema */
.lead {
  font-size: 1.125rem;
  font-weight: var(--font-weight-regular);
  line-height: 1.6;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
}

.visually-hidden {
  position: absolute !important;
  height: 1px; width: 1px; overflow: hidden; clip: rect(1px, 1px, 1px, 1px);
}

.event-info {
  margin-top: 2rem;
}

.info-item {
  background: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid var(--border-light);
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.info-item:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-color: var(--border-color);
}

.info-item h4 {
  color: var(--text-primary);
  font-weight: var(--font-weight-semibold);
  font-size: 1rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
}

.info-item h4 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
  color: var(--text-muted);
}

.info-item p {
  color: var(--text-secondary);
  font-weight: var(--font-weight-regular);
  margin-bottom: 0.5rem;
}

.info-item p strong {
  color: var(--text-primary);
  font-weight: var(--font-weight-medium);
}

.date-info {
  background: var(--white);
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
}

.date-info p {
  margin-bottom: 0.75rem;
}

.date-info .text-primary {
  color: var(--primary-blue) !important;
  font-weight: var(--font-weight-medium);
}

/* Cards minimalistas */
.card {
  background: var(--card-bg);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
  overflow: hidden;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: var(--border-color);
}

.card h3 {
  color: var(--text-primary);
  font-weight: var(--font-weight-semibold);
  font-size: 1.1rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
}

.card h3 i {
  margin-right: 0.5rem;
  color: var(--text-muted);
}

.card p {
  color: var(--text-secondary);
  font-weight: var(--font-weight-regular);
  margin-bottom: 0.5rem;
}

.card p strong {
  color: var(--text-primary);
  font-weight: var(--font-weight-medium);
}

.card .text-muted {
  color: var(--text-muted) !important;
  font-size: 0.9rem;
}

/* Iconos con colores neutros */
.card .d-flex i {
  color: var(--text-muted);
  font-size: 0.9rem;
}

.card .text-muted i {
  color: var(--text-muted);
}

/* Colores del sistema */
.text-primary { color: var(--primary-blue) !important; }

/* Responsive minimalista */
@media (max-width: 768px) {
  .info-item {
    padding: 1rem;
    margin-bottom: 0.75rem;
  }
  
  .date-info {
    padding: 0.75rem;
  }
  
  .card {
    margin-bottom: 1rem;
  }
  
  .lead {
    font-size: 1rem;
    margin-bottom: 1rem;
  }
  
  .info-item h4 {
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
  }
}
</style>



