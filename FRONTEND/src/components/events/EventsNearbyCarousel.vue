<template>
  <section class="events-nearby my-4">
    <h2 class="mb-3">Servicios y experiencias cercanas</h2>
  <div class="carousel">
      <!-- <button class="nav prev" type="button" @click.stop.prevent="scroll(-1)" aria-label="Anterior">
        <i class="fas fa-chevron-left"></i>
      </button> -->
      
      <!-- Estado de carga -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando eventos...</p>
      </div>
      
      <!-- Estado de error -->
      <div v-else-if="error" class="error-state">
        <i class="fas fa-exclamation-triangle"></i>
        <p>{{ error }}</p>
        <button @click="fetchEvents" class="retry-btn">Reintentar</button>
      </div>
      
      <!-- Contenido principal -->
      <div v-else-if="events.length > 0" class="events-grid" ref="track" @mousedown="handleMouseDown" @scroll="updateScrollProgress" @touchstart="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd">
        <InterestCard
          v-for="(card, idx) in eventCards"
          :key="idx"
          :icon="card.icon"
          :color="card.color"
          :title="card.title"
          :description="card.description"
          :cover="card.cover"
          :to="card.to || null"
          @click="!card.to ? handleCardClick(events[idx]) : null"
        />
      </div>
      
      <!-- Estado vacío -->
      <div v-else class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <p>No hay eventos disponibles en este momento</p>
      </div>
      
      <!-- <button class="nav next" type="button" @click.stop.prevent="scroll(1)" aria-label="Siguiente">
        <i class="fas fa-chevron-right"></i>
      </button> -->
      
      <!-- Indicador de scroll -->
      <div v-if="events.length > 0" class="scroll-indicator">
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
import { ref, onMounted, onBeforeUnmount, nextTick, computed } from 'vue'
import { useRouter } from 'vue-router'
import { eventsService } from '@/services/eventsService'
import type { EventCardData } from '@/types/events'
import InterestCard from '@/components/home/InterestCard.vue'

export default {
  name: 'EventsNearbyCarousel',
  components: { InterestCard },
  props: {
    excludeEventId: { type: [String, Number], default: null }
  },
  setup(props: { excludeEventId: string | number | null }) {
    const router = useRouter()
    
    // Variables para datos dinámicos
    const events = ref<EventCardData[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)
    
    // Variables para drag scroll
    const isDragging = ref(false)
    const startX = ref(0)
    const scrollLeft = ref(0)
    const track = ref<HTMLDivElement | null>(null)
    const scrollProgress = ref<HTMLDivElement | null>(null)

    // Función para cargar eventos dinámicamente
    const fetchEvents = async () => {
      loading.value = true
      error.value = null
      
      try {
        console.log('EventsNearbyCarousel: Fetching events...')
        const response = await eventsService.getAllLocationEvents()
        console.log('EventsNearbyCarousel: Response:', response)
        
        if (response.success && response.data) {
          // Transformar los datos de la API al formato esperado
          let allEvents = response.data.map(event => 
            eventsService.transformLocationToEventCard(event)
          )
          
          // Excluir el evento seleccionado si se proporciona
          const excludeId = props.excludeEventId != null ? String(props.excludeEventId) : null
          if (excludeId !== null) {
            allEvents = allEvents.filter(event => 
              event.id.toString() !== excludeId
            )
          }
          
          events.value = allEvents
          console.log('EventsNearbyCarousel: Loaded events:', allEvents.length)
        } else {
          throw new Error('Error en la respuesta de la API')
        }
      } catch (err) {
        console.error('EventsNearbyCarousel: Error fetching events:', err)
        error.value = 'Error al cargar los eventos'
        events.value = []
      } finally {
        loading.value = false
      }
    }

    // Mapeo a InterestCard props
    const mapEventToCard = (event: EventCardData) => ({
      title: event.title,
      subtitle: event.date,
      description: event.location,
      cover: event.image,
      badge: 'Evento',
      to: { name: 'EventDetail', params: { id: event.id } },
      icon: 'far fa-calendar-alt',
      color: '#2563eb'
    })

    // Computed para las tarjetas de eventos
    const eventCards = computed(() => {
      return events.value.map(event => mapEventToCard(event))
    })

    // Función para manejar click en tarjeta
    const handleCardClick = (event: EventCardData) => {
      router.push({ name: 'EventDetail', params: { id: event.id } })
    }

    // Función para navegar al detalle del evento
    const navigateToEvent = (eventId: string | number) => {
      router.push({ name: 'EventDetail', params: { id: eventId } })
    }

    const scroll = (direction: number) => {
      const el = track.value as HTMLDivElement | null
      if (!el) return
      
      console.log('EventsNearbyCarousel: Scrolling', direction)
      console.log('EventsNearbyCarousel: Current scrollLeft:', el.scrollLeft)
      console.log('EventsNearbyCarousel: ScrollWidth:', el.scrollWidth)
      console.log('EventsNearbyCarousel: ClientWidth:', el.clientWidth)
      
      const scrollAmount = 300 // Fixed scroll amount
      const currentScroll = el.scrollLeft
      const targetScroll = currentScroll + (direction * scrollAmount)
      
      // Ensure we don't scroll beyond bounds
      const maxScroll = el.scrollWidth - el.clientWidth
      const finalScroll = Math.max(0, Math.min(targetScroll, maxScroll))
      
      console.log('EventsNearbyCarousel: Target scroll:', finalScroll)
      
      el.scrollTo({
        left: finalScroll,
        behavior: 'smooth'
      })
      
      // Update progress after scroll
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
      
      e.preventDefault()
    }

    const handleTouchMove = (e: TouchEvent) => {
      if (!isDragging.value || !track.value) return
      
      e.preventDefault()
      
      const x = e.touches[0].pageX - track.value.offsetLeft
      const walk = (x - startX.value) * 1.5
      track.value.scrollLeft = scrollLeft.value - walk
      
      updateScrollProgress()
    }

    const handleTouchEnd = () => {
      if (!isDragging.value) return
      
      isDragging.value = false
      
      if (track.value) {
        track.value.classList.remove('dragging')
      }
    }

    onMounted(() => {
      // Cargar eventos dinámicamente
      fetchEvents()
      
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
      events,
      eventCards,
      loading,
      error,
      fetchEvents,
      track,
      scrollProgress,
      scroll,
      updateScrollProgress,
      navigateToEvent,
      handleCardClick,
      handleMouseDown,
      handleMouseMove,
      handleMouseUp,
      handleTouchStart,
      handleTouchMove,
      handleTouchEnd,
      mapEventToCard
    }
  }
}
</script>

<style scoped>
.events-nearby h2 { font-weight: 300; letter-spacing: -0.02em; }
.carousel { position: relative; overflow: visible; }

/* Igualar la grilla y comportamiento de EventsSection */
.events-grid {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: minmax(280px, 1fr);
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
  width: 100%;
  min-width: 0;
}
.events-grid::-webkit-scrollbar { display: none; }
.events-grid > * { scroll-snap-align: start; }
.events-grid:active { cursor: grabbing; }
.events-grid.dragging { cursor: grabbing; user-select: none; scroll-snap-type: none; }
.events-grid.dragging > * { transform: scale(0.98); transition: transform 0.1s ease; }

/* Scroll indicator */
.scroll-indicator { display: flex; align-items: center; gap: 1rem; margin-top: 1rem; }
.scroll-track { flex: 1; height: 2px; background: var(--border-light); border-radius: 1px; position: relative; overflow: hidden; }
.scroll-progress { position: absolute; top: 0; left: 0; height: 100%; background: var(--primary-blue); border-radius: 1px; width: 25%; transition: width 0.3s ease; }
.scroll-arrows { display: flex; gap: 0.5rem; }
.scroll-arrow { width: 40px; height: 40px; border: 1px solid var(--border-light); background: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; color: var(--text-secondary); font-size: 0.875rem; }
.scroll-arrow:hover { background: var(--primary-blue); color: var(--white); border-color: var(--primary-blue); transform: scale(1.05); }
.scroll-arrow:active { transform: scale(0.95); }

/* Estados de carga, error y vacío */
.loading-state, .error-state, .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 1rem; text-align: center; color: var(--text-secondary); min-height: 300px; }
.spinner { width: 40px; height: 40px; border: 3px solid var(--border-light); border-top: 3px solid var(--primary-blue); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 1rem; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.error-state i, .empty-state i { font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem; }
.error-state i { color: var(--error-red); }
.retry-btn { background: var(--primary-blue); color: var(--white); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; margin-top: 1rem; }
.retry-btn:hover { background: var(--primary-blue-dark); transform: translateY(-1px); }

/* Responsive */
@media (max-width: 768px) {
  .scroll-arrow { width: 36px; height: 36px; font-size: 0.75rem; }
  .loading-state, .error-state, .empty-state { padding: 2rem 1rem; min-height: 250px; }
  .spinner { width: 30px; height: 30px; }
  .error-state i, .empty-state i { font-size: 2rem; }
}
</style>

