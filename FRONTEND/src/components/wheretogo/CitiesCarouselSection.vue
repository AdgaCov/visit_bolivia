<template>
  <section class="cities-section">
    <div class="container">
      <div class="section-header">
        <h2 class="cities-title">{{ title || 'Despierta tu curiosidad en un viaje por las ciudades de Bolivia' }}</h2>
        <p class="cities-subtitle">{{ subtitle || 'Experimenta la historia y cultura local más allá de las páginas de un libro, y descubre que las ciudades de cuento de hadas no son solo ficción.' }}</p>
      </div>
      
      <div class="cities-container">
        <div
          class="cities-grid"
          ref="citiesGrid"
          @mousedown="handleMouseDown"
          @scroll="updateScrollProgress"
          @touchstart="handleTouchStart"
          @touchmove="handleTouchMove"
          @touchend="handleTouchEnd"
        >
          <div class="city-card" v-for="dept in departments" :key="dept.id" @click="handleDepartmentClick(dept)">
            <div class="city-image">
              <img :src="dept.image" :alt="dept.name" class="city-img">
            </div>
            <h3 class="city-name">{{ dept.name }}</h3>
          </div>
        </div>
        
        <!-- Indicador de scroll -->
        <div class="scroll-indicator">
          <div class="scroll-track">
            <div class="scroll-progress" ref="scrollProgress"></div>
          </div>
          <div class="scroll-arrows">
            <button class="scroll-arrow scroll-left" @click="scrollCities('left')">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button class="scroll-arrow scroll-right" @click="scrollCities('right')">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import apiClient from '@/services/api'
import API_ENDPOINTS from '@/services/endpoints'
export default {
  name: 'CitiesCarouselSection',
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
  data() {
    return {
      departments: []
    }
  },
  mounted() {
    // Inicializar progreso del scroll
    this.$nextTick(() => {
      this.updateScrollProgress();
      
      // Agregar event listener para scroll manual
      const citiesGrid = this.$refs.citiesGrid;
      if (citiesGrid) {
        citiesGrid.addEventListener('scroll', this.updateScrollProgress);
      }

      // Listeners globales para arrastre con mouse
      document.addEventListener('mousemove', this.handleMouseMove)
      document.addEventListener('mouseup', this.handleMouseUp)
    });
    // Cargar departamentos
    this.loadDepartments()
  },
  beforeUnmount() {
    // Limpiar event listener
    const citiesGrid = this.$refs.citiesGrid;
    if (citiesGrid) {
      citiesGrid.removeEventListener('scroll', this.updateScrollProgress);
    }
    document.removeEventListener('mousemove', this.handleMouseMove)
    document.removeEventListener('mouseup', this.handleMouseUp)
  },
  methods: {
    async loadDepartments() {
      try {
        const resp = await apiClient.get(API_ENDPOINTS.DEPARTMENTS.BASE)
        const list = (resp && (resp.data || resp)) || []
        const mapped = Array.isArray(list)
          ? list.map(dep => {
              const image = dep?.image_url || '/images/placeholder.jpg'
              return {
                id: Number(dep.id),
                name: String(dep.name || ''),
                image
              }
            })
          : []
        this.departments = mapped
      } catch (e) {
        console.error('Error cargando departamentos:', e)
        this.departments = []
      }
    },
    handleDepartmentClick(dept) {
      if (!dept || !dept.name) return
      const slug = String(dept.name)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
      this.$router.push({ name: 'InteractiveMap', query: { department: slug } })
    },
    scrollCities(direction) {
      const citiesGrid = this.$refs.citiesGrid;
      if (citiesGrid) {
        const scrollAmount = 300;
        if (direction === 'left') {
          citiesGrid.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
          });
        } else {
          citiesGrid.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
          });
        }
        
        // Actualizar progreso después del scroll
        setTimeout(() => {
          this.updateScrollProgress();
        }, 350);
      }
    },
    updateScrollProgress() {
      const citiesGrid = this.$refs.citiesGrid;
      if (citiesGrid) {
        const scrollLeft = citiesGrid.scrollLeft;
        const scrollWidth = citiesGrid.scrollWidth - citiesGrid.clientWidth;
        const progress = scrollWidth > 0 ? (scrollLeft / scrollWidth) * 100 : 0;
        
        const progressBar = this.$refs.scrollProgress;
        if (progressBar) {
          progressBar.style.width = `${progress}%`;
        }
      }
    },

    // Drag scroll - similar a InterestsSection
    handleMouseDown(e) {
      const el = this.$refs.citiesGrid
      if (!el) return
      this.isDragging = true
      this.dragMoved = false
      el.classList.add('dragging')
      this.startX = e.pageX - el.offsetLeft
      this.scrollLeft = el.scrollLeft
      e.preventDefault()
      e.stopPropagation()
    },
    handleMouseMove(e) {
      const el = this.$refs.citiesGrid
      if (!this.isDragging || !el) return
      e.preventDefault()
      const x = e.pageX - el.offsetLeft
      const walk = (x - this.startX) * 1.5
      el.scrollLeft = this.scrollLeft - walk
      if (Math.abs(walk) > 5) this.dragMoved = true
      this.updateScrollProgress()
    },
    handleMouseUp() {
      if (!this.isDragging) return
      this.isDragging = false
      const el = this.$refs.citiesGrid
      if (el) el.classList.remove('dragging')
    },
    handleTouchStart(e) {
      const el = this.$refs.citiesGrid
      if (!el) return
      this.isDragging = true
      el.classList.add('dragging')
      this.startX = e.touches[0].pageX - el.offsetLeft
      this.scrollLeft = el.scrollLeft
      e.preventDefault()
    },
    handleTouchMove(e) {
      const el = this.$refs.citiesGrid
      if (!this.isDragging || !el) return
      e.preventDefault()
      const x = e.touches[0].pageX - el.offsetLeft
      const walk = (x - this.startX) * 1.5
      el.scrollLeft = this.scrollLeft - walk
      if (Math.abs(walk) > 5) this.dragMoved = true
      this.updateScrollProgress()
    },
    handleTouchEnd() {
      if (!this.isDragging) return
      this.isDragging = false
      const el = this.$refs.citiesGrid
      if (el) el.classList.remove('dragging')
    }
  }
}
</script>

<style scoped>
/* Cities section - Estilo Estonia */
.cities-section {
  padding: 4rem 0;
  background: var(--white);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.cities-title {
  font-size: 2.5rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  font-family: var(--font-family-base);
  letter-spacing: -0.02em;
  line-height: 1.2;
}

.cities-subtitle {
  color: var(--text-secondary);
  font-size: 1.125rem;
  font-family: var(--font-family-base);
  margin-bottom: 3rem;
  line-height: 1.6;
}

.cities-container {
  position: relative;
}

.cities-grid {
  display: grid;
  grid-auto-flow: column;
  /* Mostrar exactamente 4 tarjetas en el ancho visible */
  grid-auto-columns: calc((100% - (3 * 1.5rem)) / 4);
  gap: 1.5rem;
  overflow-x: auto;
  padding-bottom: 2rem;
  scroll-snap-type: x mandatory;
  scrollbar-width: none;
  -ms-overflow-style: none;
  cursor: grab;
  user-select: none;
}

.cities-grid::-webkit-scrollbar {
  display: none;
}

.cities-grid > * {
  scroll-snap-align: start;
}

.cities-grid:active {
  cursor: grabbing;
}

.cities-grid.dragging { cursor: grabbing; user-select: none; scroll-snap-type: none; }
.cities-grid.dragging > * { transform: scale(0.98); transition: transform 0.1s ease; }

.city-card {
  background: transparent;
  border: none;
  border-radius: 0;
  overflow: visible;
  transition: all 0.3s ease;
  box-shadow: none;
  cursor: pointer;
  position: relative;
}

.city-card::before {
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

.city-card:hover::before { opacity: 1; }

.city-image {
  position: relative;
  height: 260px;
  overflow: hidden;
  border-radius: 24px;
  transition: border-radius 300ms ease;
}

.city-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scale(1);
  transition: transform 400ms ease;
  will-change: transform;
}

.city-image::after {
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

.city-card:hover .city-img { transform: scale(1.05); }
.city-card:hover .city-image { border-radius: 24px 50px 24px 24px; }
.city-card:hover .city-image::after { opacity: 1; }

.city-name {
  font-size: 1.5rem;
  font-weight: 400;
  color: var(--text-primary);
  margin: 0.75rem 0 0.25rem;
  text-align: center;
  font-family: var(--font-family-base);
  letter-spacing: -0.02em;
  line-height: 1.25;
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

/* Responsive para ciudades */
@media (max-width: 768px) {
  .cities-section {
    padding: 3rem 0;
  }
  
  .cities-title {
    font-size: 2rem;
  }
  
  .cities-subtitle {
    font-size: 1rem;
  }
  
  .cities-grid {
    grid-auto-columns: minmax(250px, 1fr);
    gap: 1rem;
  }
  
  .city-card {}
  
  .scroll-arrow {
    width: 36px;
    height: 36px;
    font-size: 0.75rem;
  }
}
</style>

