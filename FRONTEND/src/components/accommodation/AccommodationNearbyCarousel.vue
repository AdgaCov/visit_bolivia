<template>
  <section class="accommodation-nearby my-4">
    <h2 class="mb-3">Otros hoteles cercanos</h2>
    <div class="carousel">
      <!-- Estado de carga -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando hoteles...</p>
      </div>
      
      <!-- Estado de error -->
      <div v-else-if="error" class="error-state">
        <i class="fas fa-exclamation-triangle"></i>
        <p>{{ error }}</p>
        <button @click="fetchAccommodations" class="retry-btn">Reintentar</button>
      </div>
      
      <!-- Contenido principal -->
      <div v-else-if="accommodations.length > 0" class="accommodations-grid" ref="track" @mousedown="handleMouseDown" @scroll="updateScrollProgress" @touchstart="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd">
        <AccommodationCard
          v-for="accommodation in accommodations"
          :key="accommodation.id"
          :image="getMainImage(accommodation)"
          :title="accommodation.title"
          :location="accommodation.location"
          :price="formatPrice(accommodation.price_per_night)"
          :description="accommodation.description"
          :badge="accommodation.badge"
          :to="{ name: 'AccommodationDetail', params: { id: accommodation.id } }"
        />
      </div>
      
      <!-- Estado vacío -->
      <div v-else class="empty-state">
        <i class="fas fa-bed"></i>
        <p>No hay hoteles disponibles en este momento</p>
      </div>
      
      <!-- Indicador de scroll -->
      <div v-if="accommodations.length > 0" class="scroll-indicator">
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
import { ref, onMounted, onBeforeUnmount, nextTick, getCurrentInstance } from 'vue'
import { fetchAccommodations } from '@/services/accommodations.service'
import type { AccommodationApi } from '@/types'
import AccommodationCard from '@/components/home/AccommodationCard.vue'

export default {
  name: 'AccommodationNearbyCarousel',
  components: { AccommodationCard },
  props: {
    excludeAccommodationId: { type: [String, Number], default: null }
  },
  setup(props: { excludeAccommodationId: string | number | null }) {
    const app = getCurrentInstance()
    // Variables para datos dinámicos
    const accommodations = ref<AccommodationApi[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)
    
    // Variables para drag scroll
    const isDragging = ref(false)
    const startX = ref(0)
    const scrollLeft = ref(0)
    const track = ref<HTMLDivElement | null>(null)
    const scrollProgress = ref<HTMLDivElement | null>(null)

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
      return `Desde $${numPrice.toFixed(0)}/noche`
    }

    // Función para cargar hoteles dinámicamente
    const fetchAccommodationsData = async () => {
      try {
        loading.value = true
        error.value = null
        const accommodationsData = await fetchAccommodations()
        
        // Excluir el hotel actual si se proporciona
        const excludeId = props.excludeAccommodationId != null ? String(props.excludeAccommodationId) : null
        let filteredAccommodations = accommodationsData
        
        if (excludeId !== null) {
          filteredAccommodations = accommodationsData.filter(acc => 
            acc.id.toString() !== excludeId
          )
        }
        
        accommodations.value = filteredAccommodations
      } catch (err) {
        console.error('Error fetching accommodations:', err)
        error.value = 'Error al cargar los hoteles'
        accommodations.value = []
      } finally {
        loading.value = false
      }
    }

    const scroll = (direction: number) => {
      const el = track.value as HTMLDivElement | null
      if (!el) return
      const firstCard = el.querySelector('.accommodation-card') as HTMLElement | null
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
      // Cargar hoteles dinámicamente
      fetchAccommodationsData()
      
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
      accommodations,
      loading,
      error,
      fetchAccommodations: fetchAccommodationsData,
      getMainImage,
      formatPrice,
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
.accommodation-nearby h2 { 
  font-weight: 300; 
  letter-spacing: -0.02em; 
}

.carousel { 
  position: relative; 
  overflow: visible; 
}

/* Igualar la grilla y comportamiento de EventsSection */
.accommodations-grid {
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
}

.accommodations-grid::-webkit-scrollbar { 
  display: none; 
}

.accommodations-grid > * { 
  scroll-snap-align: start; 
}

.accommodations-grid:active { 
  cursor: grabbing; 
}

.accommodations-grid.dragging { 
  cursor: grabbing; 
  user-select: none; 
  scroll-snap-type: none; 
}

.accommodations-grid.dragging > * { 
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
