<template>
  <section class="cultural-regions-section">
    <div class="container">
      <h2 class="section-title">Territorios culturales de Bolivia</h2>
      <p class="section-subtitle">Conoce la riqueza y diversidad de las macroregiones, cada región muestra una identidad propia que contribuye al mosaico cultural boliviano.</p>
      <div class="interests-container">
        <!-- Loading state -->
        <div v-if="loading" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Cargando regiones...</p>
    </div>
    
        <!-- Error state -->
        <div v-else-if="error" class="error-container">
          <p class="error-message">{{ error }}</p>
          <button @click="loadRegions" class="retry-button">Reintentar</button>
          </div>
        
        <!-- Categories grid -->
        <div
          v-else
          class="interests-grid"
          ref="interestsGridRef"
          @mousedown="handleMouseDown"
          @scroll="updateScrollProgress"
          @touchstart="handleTouchStart"
          @touchmove="handleTouchMove"
          @touchend="handleTouchEnd"
        >
          <InterestCard
            v-for="region in culturalRegions"
            :key="region.id"
            :icon="''"
            :color="region.color"
            :title="region.name"
            :description="region.description"
            :cover="region.image"
            :categorySlug="getRegionSlug(region.name)"
            @click="navigateToRegion(region)"
          />
        </div>
        <!-- Indicador de scroll + flechas -->
        <div class="scroll-indicator">
          <div class="scroll-track">
            <div class="scroll-progress" ref="scrollProgress"></div>
          </div>
          <div class="scroll-arrows">
            <button class="scroll-arrow scroll-left" @click="scrollInterests('left')">
            <i class="fas fa-chevron-left"></i>
          </button>
            <button class="scroll-arrow scroll-right" @click="scrollInterests('right')">
            <i class="fas fa-chevron-right"></i>
          </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import InterestCard from '@/components/home/InterestCard.vue'

export default {
  name: 'CulturalRegionsSection',
  components: {
    InterestCard
  },
  data() {
    return {
      culturalRegions: [],
      loading: true,
      error: null,
      // Estado drag
      isDragging: false,
      startX: 0,
      scrollLeft: 0
    }
  },
  async mounted() {
    await this.loadRegions()
    this.$nextTick(() => {
      this.updateScrollProgress()
      document.addEventListener('mousemove', this.handleMouseMove)
      document.addEventListener('mouseup', this.handleMouseUp)
    })
  },
  beforeUnmount() {
    document.removeEventListener('mousemove', this.handleMouseMove)
    document.removeEventListener('mouseup', this.handleMouseUp)
  },
  methods: {
    async loadRegions() {
      try {
        this.loading = true
        this.error = null
        
        // Simular carga de datos
        await new Promise(resolve => setTimeout(resolve, 500))
        
        this.culturalRegions = [
          {
            id: 1,
          name: 'Conoce el Chaco boliviano',
          description: 'La macro región del Chaco boliviano se caracteriza por su riqueza cultural y la presencia de pueblos indígenas como los guaraníes, tapietes y weenhayek, que conservan sus lenguas y tradiciones ancestrales. Esta diversidad se refleja en su gastronomía típica, basada en productos locales y platos como el chancho a la cruz. La ganadería es una de las principales actividades económicas de la región, aportando a su identidad y desarrollo.',
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/22.jpg',
            color: '#1e40af'
        },
        {
            id: 2,
          name: 'Yungas bolivianos',
          description: 'Los Yungas bolivianos conforman una macroregión cultural, situada entre los Andes y la Amazonía. Reconocida por su biodiversidad y cultivos como la coca, el café y el cacao, la región también destaca por su valioso patrimonio afroboliviano. En este territorio, la naturaleza y la cultura conviven y la presencia afrodescendiente enriquece la identidad local a través de expresiones como la saya, los saberes agrícolas y las tradiciones comunitarias.',
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/5.jpg',
            color: '#059669'
        },
        {
            id: 3,
          name: 'Cultura y Patrimonio de los Valles Interandinos de Bolivia',
          description: 'Los valles interandinos de Bolivia abarca áreas de Cochabamba, Sucre y Tarija. Más allá de su riqueza natural y biodiversidad, destacan por su cultura: Las comunidades locales aún conservan prácticas y saberes reflejados en sus festividades, artesanías y actividades agrícolas. A la vez, se combina con la influencia colonial, visible en iglesias, casonas antiguas y sitios históricos, mientras que la gastronomía local integra productos locales con técnicas indígenas, coloniales y europeas adaptadas al clima templado del valle.',
          image: "http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/31.png",
            color: '#16A34A'
        },
        {
            id: 4,
          name: 'Territorios culturales de Bolivia',
          description: 'Conoce la riqueza y diversidad de las macroregiones, cada región muestra una identidad propia que contribuye al mosaico cultural boliviano.',
          image: "http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/25.jpg",
            color: '#D97706'
        },
        {
            id: 5,
          name: ' Raíces y Expresiones de los Andes',
          description: 'La Macroregión Andina de Bolivia combina la presencia de pueblos originarios como los aimara y quechua, cuya cosmovisión andina, centrada en la relación con la Pachamama y en principios de reciprocidad y vida comunitaria, se refleja en festividades, música, danzas, artesanías, textiles, lenguas originarias, gastronomía y sitios arqueológicos. Esta riqueza cultural, presente en la vida cotidiana y en la transmisión de saberes, convierte a la región en un espacio donde tradición, espiritualidad, arte y comunidad se entrelazan, ofreciendo a los visitantes experiencias auténticas y únicas de la identidad boliviana.',
          image: "http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/29.jpg",
            color: '#7C3AED'
          },
        {
            id: 6,
          name: 'Cultura y Patrimonio de las Tierras Bajas de Bolivia',
          description: 'Grandes Llanuras, Chiquitanía y Pantanal: La Chiquitanía y las grandes llanuras de Bolivia destacan por su riqueza cultural e histórica. La influencia de los pueblos chiquitanos y otras comunidades indígenas se refleja en las misiones jesuíticas barrocas, en la música y danzas locales, elaboración de tejidos, cerámica y tallados en madera.',
          image: "http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/27.jpg",
            color: '#7C3AED'
          }
        ]
      } catch (error) {
        console.error('Error loading regions:', error)
        this.error = 'No se pudieron cargar las regiones'
        this.culturalRegions = []
      } finally {
        this.loading = false
      }
    },
    
    navigateToRegion(region) {
      // Navegar a la página de la región
      const slug = this.getRegionSlug(region.name)
      this.$router.push(`/regiones/${slug}`)
    },
    getRegionSlug(regionName) {
      return regionName.toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[áàäâ]/g, 'a')
        .replace(/[éèëê]/g, 'e')
        .replace(/[íìïî]/g, 'i')
        .replace(/[óòöô]/g, 'o')
        .replace(/[úùüû]/g, 'u')
        .replace(/ñ/g, 'n')
    },
    scrollInterests(direction) {
      const el = this.$refs.interestsGridRef
      if (!el) return
      const amount = 300
      const left = direction === 'left' ? -amount : amount
      el.scrollBy({ left, behavior: 'smooth' })
      setTimeout(() => this.updateScrollProgress(), 350)
    },
    updateScrollProgress() {
      const el = this.$refs.interestsGridRef
      if (!el) return
      const scrollLeft = el.scrollLeft
      const scrollWidth = el.scrollWidth - el.clientWidth
      const progress = scrollWidth > 0 ? (scrollLeft / scrollWidth) * 100 : 0
      const bar = this.$refs.scrollProgress
      if (bar) bar.style.width = `${progress}%`
    },
    handleMouseDown(e) {
      const el = this.$refs.interestsGridRef
      if (!el) return
      this.isDragging = true
      el.classList.add('dragging')
      this.startX = e.pageX - el.offsetLeft
      this.scrollLeft = el.scrollLeft
      e.preventDefault()
      e.stopPropagation()
    },
    handleMouseMove(e) {
      const el = this.$refs.interestsGridRef
      if (!this.isDragging || !el) return
      e.preventDefault()
      const x = e.pageX - el.offsetLeft
      const walk = (x - this.startX) * 1.5
      el.scrollLeft = this.scrollLeft - walk
      this.updateScrollProgress()
    },
    handleMouseUp() {
      if (!this.isDragging) return
      this.isDragging = false
      const el = this.$refs.interestsGridRef
      if (el) el.classList.remove('dragging')
    },
    handleTouchStart(e) {
      const el = this.$refs.interestsGridRef
      if (!el) return
      this.isDragging = true
      el.classList.add('dragging')
      this.startX = e.touches[0].pageX - el.offsetLeft
      this.scrollLeft = el.scrollLeft
      e.preventDefault()
    },
    handleTouchMove(e) {
      const el = this.$refs.interestsGridRef
      if (!this.isDragging || !el) return
      e.preventDefault()
      const x = e.touches[0].pageX - el.offsetLeft
      const walk = (x - this.startX) * 1.5
      el.scrollLeft = this.scrollLeft - walk
      this.updateScrollProgress()
    },
    handleTouchEnd() {
      if (!this.isDragging) return
      this.isDragging = false
      const el = this.$refs.interestsGridRef
      if (el) el.classList.remove('dragging')
    }
  }
}
</script>

<style scoped>
/* Intereses */
.cultural-regions-section {
  padding: 4rem 0;
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.section-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  /* text-align: center; */
  margin-bottom: 3rem;
  letter-spacing: -0.01em;
}

.section-subtitle {
  /* text-align: center; */
  color: var(--text-secondary);
  margin-top: -1rem;
  margin-bottom: 2rem;
  font-size: 1.5rem;
}

.interests-container {
  position: relative;
}


.interests-grid {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: minmax(280px, 420px);
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

.interests-grid::-webkit-scrollbar { display: none; }

.interests-grid > * {
  scroll-snap-align: start; /* opcional, para que los items se alineen */
}

.interests-grid:active {
  cursor: grabbing;
}

.interests-grid.dragging { cursor: grabbing; user-select: none; scroll-snap-type: none; }
.interests-grid.dragging > * { transform: scale(0.98); transition: transform 0.1s ease; }

/* Indicador de scroll y flechas (alineado con EventsSection) */
.scroll-indicator { display: flex; align-items: center; gap: 1rem; margin-top: 1rem; }
.scroll-track { flex: 1; height: 2px; background: var(--border-light); border-radius: 1px; position: relative; overflow: hidden; }
.scroll-progress { position: absolute; top: 0; left: 0; height: 100%; background: var(--primary-blue); border-radius: 1px; width: 25%; transition: width 0.3s ease; }
.scroll-arrows { display: flex; gap: 0.5rem; }
.scroll-arrow { width: 40px; height: 40px; border: 1px solid var(--border-light); background: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; color: var(--text-secondary); font-size: 0.875rem; }
.scroll-arrow:hover { background: var(--primary-blue); color: var(--white); border-color: var(--primary-blue); transform: scale(1.05); }
.scroll-arrow:active { transform: scale(0.95); }

/* Loading and Error States */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--bg-secondary);
  border-top: 4px solid var(--primary-blue);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-container p {
  color: var(--text-secondary);
  font-size: 1rem;
}

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.error-message {
  color: var(--error-color, #EF4444);
  font-size: 1rem;
  margin-bottom: 1rem;
}

.retry-button {
  background: var(--primary-blue);
  color: var(--white);
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.retry-button:hover {
  background: var(--primary-blue-dark, #1E40AF);
  transform: translateY(-2px);
}

/* Ajustar indicador en móviles */
@media (max-width: 768px) {
}
</style>
