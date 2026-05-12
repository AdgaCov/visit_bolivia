<template>
  <section class="accommodation-experiences">
    <div class="container">
      <div class="section-header">
        <h2 class="experience-title">{{ title || 'Estos lugares para hospedarte en Bolivia ofrecen mucho más que solo un lugar para dormir' }}</h2>
        <p class="experience-subtitle">{{ subtitle || '¿Sueñas con escapar a una cabaña en los Andes o disfrutar de un retiro lujoso? Intenta planificar tu viaje alrededor de una de estas experiencias únicas de alojamiento.' }}</p>
      </div>
      
      <div class="carousel">
        <!-- Estado de carga -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Cargando experiencias de alojamiento...</p>
        </div>
        
        <!-- Estado de error -->
        <div v-else-if="error" class="error-state">
          <i class="fas fa-exclamation-triangle"></i>
          <p>{{ error }}</p>
          <button @click="loadAccommodations" class="retry-btn">Reintentar</button>
        </div>
        
        <!-- Contenido principal -->
        <div v-else-if="accommodationExperiences.length > 0" class="experiences-grid" ref="track" @mousedown="handleMouseDown" @scroll="updateScrollProgress" @touchstart="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd">
          <AccommodationCard
            v-for="experience in accommodationExperiences"
            :key="experience.id"
            :image="getMainImage(experience)"
            :title="experience.title"
            :location="experience.location"
            :price="formatPrice(experience.price_per_night)"
            :description="experience.description"
            :badge="experience.badge"
            :to="{ name: 'AccommodationDetail', params: { id: experience.id } }"
          />
        </div>
        
        <!-- Estado vacío -->
        <div v-else class="empty-state">
          <i class="fas fa-bed"></i>
          <p>No hay experiencias de alojamiento disponibles en este momento</p>
        </div>
        
        <!-- Indicador de scroll -->
        <div v-if="accommodationExperiences.length > 0" class="scroll-indicator">
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
    </div>
  </section>
</template>

<script lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { fetchAccommodations } from '@/services/accommodations.service'
import type { AccommodationApi } from '@/types'
import AccommodationCard from '@/components/home/AccommodationCard.vue'

export default {
  name: 'AccommodationExperiencesSection',
  components: {
    AccommodationCard
  },
  props: {
    title: {
      type: String,
      default: ''
    },
    subtitle: {
      type: String,
      default: ''
    }
  },
  setup() {
    // Variables para datos dinámicos
    const accommodationExperiences = ref<AccommodationApi[]>([])
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
      const mainImage = accommodation.images?.find(img => (img as any).is_main)
      if (mainImage) {
        return (mainImage as any).image_url
      }
      if (accommodation.images && accommodation.images.length > 0) {
        return (accommodation.images[0] as any).image_url
      }
      return 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=600&h=400&fit=crop'
    }

    // Normalizar y formatear el precio (evitar negativos)
    const formatPrice = (price: string): string => {
      const numPrice = Math.abs(parseFloat(price))
      if (isNaN(numPrice)) return 'Consultar precio'
      return `Desde $${numPrice.toFixed(0)}/noche`
    }

    // Cargar acomodaciones de la API
    const loadAccommodations = async () => {
      try {
        loading.value = true
        error.value = null
        const accommodations = await fetchAccommodations()
        accommodationExperiences.value = accommodations
      } catch (err) {
        console.error('Error loading accommodations:', err)
        error.value = 'No se pudieron cargar las experiencias de alojamiento'
        accommodationExperiences.value = []
      } finally {
        loading.value = false
      }
    }

    const scroll = (direction: number) => {
      const el = track.value as HTMLDivElement | null
      if (!el) return
      const firstCard = el.querySelector('.experience-card') as HTMLElement | null
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
      // Cargar acomodaciones dinámicamente
      loadAccommodations()
      
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
      accommodationExperiences,
      loading,
      error,
      loadAccommodations,
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
/* Experiences section - Minimalista */
.accommodation-experiences {
  padding: 4rem 0;
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.experience-title {
  font-size: 2.5rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  font-family: var(--font-family-base);
  letter-spacing: -0.02em;
  line-height: 1.2;
  text-align:left ;
}

.experience-subtitle {
  color: var(--text-secondary);
  font-size: 1.125rem;
  font-family: var(--font-family-base);
  margin-bottom: 0;
  line-height: 1.6;
  text-align:left ;
}

/* Carrusel container */
.carousel {
  position: relative;
  overflow: visible;
}

/* Grid de experiencias como carrusel - Estilo InterestCard */
.experiences-grid {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: calc(25% - 1.5rem);
  gap: 2rem;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
  padding-right: 2px;
  padding-bottom: 1rem;
  -ms-overflow-style: none;
  cursor: grab;
  user-select: none;
}

.experiences-grid::-webkit-scrollbar {
  display: none;
}

.experiences-grid > * {
  scroll-snap-align: start;
}

.experiences-grid:active {
  cursor: grabbing;
}

.experiences-grid.dragging {
  cursor: grabbing;
  user-select: none;
  scroll-snap-type: none;
}

.experiences-grid.dragging > * {
  transform: scale(0.98);
  transition: transform 0.1s ease;
}

/* Cards de experiencia - Estilo InterestCard */
.experience-card {
  background: transparent;
  border: none;
  border-radius: 0;
  overflow: visible;
  box-shadow: none;
  transition: all 0.3s ease;
  position: relative;
  cursor: pointer;
  display: block;
  text-decoration: none;
  color: inherit;
}

.experience-card::before {
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

.experience-card:hover::before {
  opacity: 1;
}

.experience-image {
  position: relative;
  height: 260px;
  overflow: hidden;
  border-radius: 24px;
  transition: border-radius 300ms ease;
  background: var(--light-gray);
}

.experience-image-media {
  position: absolute;
  inset: 0;
  background-size: cover !important;
  background-position: center !important;
  background-repeat: no-repeat !important;
  transform: scale(1);
  transition: transform 400ms ease;
  will-change: transform;
}

.experience-card:hover .experience-image-media {
  transform: scale(1.05);
}

.experience-card:hover .experience-image {
  border-radius: 24px 50px 24px 24px;
}

.experience-image::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(0deg, rgba(0,0,0,0.1), rgba(0,0,0,0.0));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.experience-card:hover .experience-image::after {
  opacity: 1;
}

.experience-badge {
  position: absolute !important;
  top: 1rem !important;
  right: 1rem !important;
  background: var(--white) !important;
  color: var(--text-primary) !important;
  padding: 0.5rem 1rem !important;
  border-radius: 50px !important;
  font-size: 0.75rem !important;
  font-weight: var(--font-weight-medium) !important;
  font-family: var(--font-family-base) !important;
  border: 1px solid var(--border-light) !important;
  box-shadow: var(--shadow-sm) !important;
  backdrop-filter: blur(10px) !important;
  z-index: 2;
}

.experience-content {
  padding: 0.75rem 0 0;
}

.experience-icon {
  font-size: 1.125rem;
  margin-bottom: 0.75rem;
  color: var(--text-secondary);
  opacity: 0.8;
  transition: all 0.3s ease;
}

.experience-card:hover .experience-icon {
  color: var(--text-primary);
  opacity: 1;
}

.experience-card-title {
  margin: 0.75rem 0 0.25rem;
  font-size: 1.5rem;
  font-weight: 400;
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.25;
}

.experience-description {
  color: var(--text-muted);
  margin: 0 0 0.75rem 0;
  font-size: 1.2rem;
  line-height: 1.4;
  font-weight: 400;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.experience-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-family: var(--font-family-base);
  font-weight: var(--font-weight-medium);
}

.experience-location i {
  color: var(--primary-blue);
  margin-right: 0.5rem;
}

.experience-price {
  font-weight: var(--font-weight-semibold);
  color: var(--primary-blue);
  font-family: var(--font-family-base);
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

/* Responsive - Alineado con InterestsSection */
@media (max-width: 1200px) {
  .experiences-grid {
    grid-auto-columns: calc(33.333% - 1.33rem);
  }
}

@media (max-width: 768px) {
  .accommodation-experiences {
    padding: 3rem 0;
  }
  
  .experience-title {
    font-size: 2rem;
  }
  
  .experience-subtitle {
    font-size: 1rem;
  }
  
  .experiences-grid {
    grid-auto-columns: calc(50% - 0.75rem);
    gap: 1.5rem;
  }
  
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

@media (max-width: 480px) {
  .experiences-grid {
    grid-auto-columns: calc(100% - 0rem);
    gap: 1rem;
  }
}
</style>

