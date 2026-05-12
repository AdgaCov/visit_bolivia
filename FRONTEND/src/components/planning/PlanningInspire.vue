<template>
  <!-- Sección: Inspírate -->
  <section class="inspire-section">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h2 class="section-title mb-0">Inspírate</h2>
        <p class="section-subtitle">Descubre experiencias únicas y lugares increíbles que despertarán tu espíritu aventurero en Bolivia.</p>
      </div>
      
      <div class="inspire-container">
        <div class="inspire-grid" ref="inspireGridRef" @mousedown="handleMouseDown" @scroll="updateScrollProgress" @touchstart="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd">
          <div 
            v-for="(item, index) in inspireItems" 
            :key="index"
            class="inspire-card"
          >
            <div class="inspire-image">
              <div class="inspire-image-media" :style="{ backgroundImage: `url(${item.image})` }" />
            </div>
            <div class="inspire-content">
              <h3 class="inspire-title-card">{{ item.title }}</h3>
            </div>
          </div>
        </div>
        
        <!-- Indicador de scroll -->
        <div class="scroll-indicator">
          <div class="scroll-track">
            <div class="scroll-progress" ref="scrollProgress"></div>
          </div>
          <div class="scroll-arrows">
            <button class="scroll-arrow scroll-left" @click="scrollInspire('left')">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button class="scroll-arrow scroll-right" @click="scrollInspire('right')">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'

export default {
  name: 'PlanningInspire',
  setup() {
    // Variables para drag scroll
    const isDragging = ref(false)
    const startX = ref(0)
    const scrollLeft = ref(0)
    const inspireGridRef = ref(null)

    const inspireItems = ref([
      {
        image: 'https://www.hotellavillachiquitana.com/wp-content/uploads/2023/02/guembe.jpg',
        title: 'Mejores senderos para niños en Bolivia'
      },
      {
        image: '/images/mas_destinos/6.png',
        title: 'Una caminata por el lado salvaje de Bolivia'
      },
      {
        image: 'https://www.opinion.com.bo/asset/thumbnail,992,558,center,center/media/opinion/images/2019/09/15/2019091518423725913.jpg',
        title: 'Una introducción a la comida boliviana'
      },
      {
        image: '/images/mas_destinos/8.png',
        title: 'Aventuras emocionantes al aire libre en Bolivia'
      },
      {
        image: '/images/mas_destinos/9.png',
        title: 'Descubre la cultura ancestral boliviana'
      },
      {
        image: '/images/mas_destinos/1.png',
        title: 'Explora los paisajes únicos del altiplano'
      }
    ])

    const scrollInspire = (direction) => {
      const inspireGrid = document.querySelector('.inspire-grid')
      if (inspireGrid) {
        const scrollAmount = 300
        if (direction === 'left') {
          inspireGrid.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
          })
        } else {
          inspireGrid.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
          })
        }
        
        // Actualizar progreso después del scroll
        setTimeout(() => {
          updateScrollProgress()
        }, 350)
      }
    }

    const updateScrollProgress = () => {
      const inspireGrid = document.querySelector('.inspire-grid')
      if (inspireGrid) {
        const scrollLeft = inspireGrid.scrollLeft
        const scrollWidth = inspireGrid.scrollWidth - inspireGrid.clientWidth
        const progress = scrollWidth > 0 ? (scrollLeft / scrollWidth) * 100 : 0
        
        const progressBar = document.querySelector('.scroll-progress')
        if (progressBar) {
          progressBar.style.width = `${progress}%`
        }
      }
    }

    // Funciones para drag scroll
    const handleMouseDown = (e) => {
      if (!inspireGridRef.value) return
      
      isDragging.value = true
      inspireGridRef.value.classList.add('dragging')
      
      startX.value = e.pageX - inspireGridRef.value.offsetLeft
      scrollLeft.value = inspireGridRef.value.scrollLeft
      
      e.preventDefault()
      e.stopPropagation()
    }

    const handleMouseMove = (e) => {
      if (!isDragging.value || !inspireGridRef.value) return
      
      e.preventDefault()
      
      const x = e.pageX - inspireGridRef.value.offsetLeft
      const walk = (x - startX.value) * 1.5
      inspireGridRef.value.scrollLeft = scrollLeft.value - walk
      
      updateScrollProgress()
    }

    const handleMouseUp = () => {
      if (!isDragging.value) return
      
      isDragging.value = false
      
      if (inspireGridRef.value) {
        inspireGridRef.value.classList.remove('dragging')
      }
    }

    // Funciones para touch events (móviles)
    const handleTouchStart = (e) => {
      if (!inspireGridRef.value) return
      
      isDragging.value = true
      inspireGridRef.value.classList.add('dragging')
      
      startX.value = e.touches[0].pageX - inspireGridRef.value.offsetLeft
      scrollLeft.value = inspireGridRef.value.scrollLeft
      
      e.preventDefault()
    }

    const handleTouchMove = (e) => {
      if (!isDragging.value || !inspireGridRef.value) return
      
      e.preventDefault()
      
      const x = e.touches[0].pageX - inspireGridRef.value.offsetLeft
      const walk = (x - startX.value) * 1.5
      inspireGridRef.value.scrollLeft = scrollLeft.value - walk
      
      updateScrollProgress()
    }

    const handleTouchEnd = () => {
      if (!isDragging.value) return
      
      isDragging.value = false
      
      if (inspireGridRef.value) {
        inspireGridRef.value.classList.remove('dragging')
      }
    }

    onMounted(() => {
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
      inspireItems,
      inspireGridRef,
      scrollInspire,
      updateScrollProgress,
      handleMouseDown,
      handleTouchStart,
      handleTouchMove,
      handleTouchEnd
    }
  }
}
</script>

<style scoped>
/* Inspiración */
.inspire-section { 
  padding: 4rem 0; 
}

.section-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: 3rem;
  letter-spacing: -0.01em;
}

.section-subtitle {
  color: var(--text-secondary);
  font-size: 1rem;
  text-align: center;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.inspire-container {
  position: relative;
}

.inspire-grid {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: minmax(280px, 1fr);
  gap: 1.5rem;
  overflow-x: auto;
  padding-bottom: 2rem;
  scroll-snap-type: x mandatory;
  scrollbar-width: none;
  -ms-overflow-style: none;
  cursor: grab;
  user-select: none;
  transition: cursor 0.2s ease;
}

.inspire-grid::-webkit-scrollbar {
  display: none;
}

.inspire-grid > * {
  scroll-snap-align: start;
}

.inspire-grid:active {
  cursor: grabbing;
}

.inspire-grid.dragging {
  cursor: grabbing;
  user-select: none;
  scroll-snap-type: none; /* Desactivar snap durante drag */
}

.inspire-grid.dragging > * {
  transform: scale(0.98); /* Efecto sutil de escala durante drag */
  transition: transform 0.1s ease;
}

/* Tarjetas de inspiración */
.inspire-card { 
  background: transparent; 
  border: none; 
  border-radius: 0; 
  overflow: visible; 
  box-shadow: none; 
  display: block; 
  text-decoration: none; 
  color: inherit; 
  cursor: pointer;
}

.inspire-image { 
  height: 260px; 
  position: relative; 
  border-radius: 24px; 
  overflow: hidden; 
  background: var(--light-gray); 
  transition: border-radius 300ms ease; 
}

.inspire-image-media { 
  position: absolute; 
  inset: 0; 
  background-size: cover; 
  background-position: center; 
  background-repeat: no-repeat; 
  transform: scale(1); 
  transition: transform 400ms ease; 
  will-change: transform; 
}

.inspire-card:hover .inspire-image-media { 
  transform: scale(1.05); 
}

.inspire-card:hover .inspire-image { 
  border-radius: 24px 12px 24px 24px; 
}

.inspire-content { 
  padding: 0.75rem 0 0; 
}

.inspire-title-card { 
  margin: 0.75rem 0 0.25rem; 
  font-size: 1.5rem; 
  font-weight: 400; 
  letter-spacing: -0.02em; 
  line-height: 1.25; 
  color: var(--text-primary); 
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

/* Responsive */
@media (max-width: 768px) {
  .section-title {
    font-size: 2rem;
  }
  
  .inspire-grid {
    grid-auto-columns: minmax(250px, 1fr);
    gap: 1rem;
  }
  
  .scroll-arrow {
    width: 36px;
    height: 36px;
    font-size: 0.75rem;
  }
  
  .inspire-image {
    height: 220px;
  }
  
  .inspire-title-card {
    font-size: 1.25rem;
  }
}
</style>
