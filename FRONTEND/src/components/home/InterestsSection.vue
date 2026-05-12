<template>
  <section v-if="!bare" id="interests" class="interests-section">
    <div class="container">
      <div v-if="showHeader" class="section-header">
        <h2 class="section-title">{{ dynamicSectionTitle }}</h2>
        <div v-if="dynamicSectionSubtitle || (ctaLabel && ctaTo)" class="header-content">
          <p v-if="dynamicSectionSubtitle" class="section-subtitle">{{ dynamicSectionSubtitle }}</p>
          <RouterLink v-if="ctaLabel && ctaTo" :to="ctaTo" class="minimal-btn">{{ ctaLabel }}</RouterLink>
        </div>
      </div>
      <div class="interests-container">
        <!-- Loading state -->
        <div v-if="loading" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Cargando categorías...</p>
        </div>
        
        <!-- Error state -->
        <div v-else-if="error" class="error-container">
          <p class="error-message">{{ error }}</p>
          <button @click="loadCategories" class="retry-button">Reintentar</button>
        </div>
        
        <!-- Categories grid -->
        <div
          v-else
          :class="displayMode === 'grid' ? 'interests-grid-layout' : 'interests-grid'"
          ref="interestsGridRef"
          @mousedown="handleMouseDown"
          @scroll="updateScrollProgress"
          @touchstart="handleTouchStart"
          @touchmove="handleTouchMove"
          @touchend="handleTouchEnd"
        >
          <InterestCard
            v-for="(card, idx) in cards"
            :key="idx"
            :icon="card.icon"
            :color="card.color"
            :title="card.title"
            :description="card.description"
            :cover="card.cover"
            :to="card.to || null"
            @click="!card.to ? handleCardClick(getItemByIndex(idx)) : null"
          />
        </div>
        <!-- Indicador de scroll + flechas (solo para carousel) -->
        <div v-if="displayMode === 'carousel'" class="scroll-indicator">
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
  <div v-else class="interests-bare">
    <div class="interests-container">
      <div v-if="loading" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Cargando...</p>
      </div>
      <div v-else-if="error" class="error-container">
        <p class="error-message">{{ error }}</p>
      </div>
      <div
        v-else
        :class="displayMode === 'grid' ? 'interests-grid-layout' : 'interests-grid'"
        ref="interestsGridRef"
        @mousedown="handleMouseDown"
        @scroll="updateScrollProgress"
        @touchstart="handleTouchStart"
        @touchmove="handleTouchMove"
        @touchend="handleTouchEnd"
      >
        <InterestCard
          v-for="(card, idx) in cards"
          :key="idx"
          :icon="card.icon"
          :color="card.color"
          :title="card.title"
          :description="card.description"
          :cover="card.cover"
          :to="card.to || null"
          @click="!card.to ? handleCardClick(getItemByIndex(idx)) : null"
        />
      </div>
      <div v-if="displayMode === 'carousel'" class="scroll-indicator">
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
</template>
<!-- esa misma pagina podria ser CulturaPage.vue para histori,aventura, dependiendo el contenido que carge desde la base de datos verdad?  -->

<script>
import InterestCard from '@/components/home/InterestCard.vue'
import categoriesService from '@/services/categories.service'
import { RouterLink } from 'vue-router'

export default {
  name: 'InterestsSection',
  components: {
    InterestCard,
    RouterLink
  },
  props: {
    // Lista genérica externa (productos, ciudades, etc.)
    items: { type: Array, default: null },
    // Función para mapear cada item al formato del card
    // (item) => ({ title, description, cover, to?, icon?, color? })
    mapItemToCard: { type: Function, default: null },
    // Callback de click cuando el card no tiene `to`
    // (item) => void
    onItemClick: { type: Function, default: null },
    // Control de encabezados y modo bare
    showHeader: { type: Boolean, default: true },
    sectionTitle: { type: String, default: '¿Cuáles son tus intereses?' },
    sectionSubtitle: { type: String, default: 'Elige una categoría para empezar tu aventura' },
    bare: { type: Boolean, default: false },
    // CTA opcional
    ctaLabel: { type: String, default: '' },
    ctaTo: { type: [String, Object], default: null },
    // Modo de visualización: 'carousel' (horizontal) o 'grid' (grilla)
    displayMode: { type: String, default: 'carousel', validator: (value) => ['carousel', 'grid'].includes(value) },
    // Artículo dinámico para template_id = 21
    article: { type: Object, default: null }
  },
  data() {
    return {
      categories: [],
      loading: true,
      error: null,
      // Estado drag
      isDragging: false,
      startX: 0,
      scrollLeft: 0,
      dragMoved: false
    }
  },
  async mounted() {
    await this.loadCategories()
    this.$nextTick(() => {
      this.updateScrollProgress()
      // Listeners globales como en FactsSection para arrastre fluido
      document.addEventListener('mousemove', this.handleMouseMove)
      document.addEventListener('mouseup', this.handleMouseUp)
    })
  },
  beforeUnmount() {
    document.removeEventListener('mousemove', this.handleMouseMove)
    document.removeEventListener('mouseup', this.handleMouseUp)
  },
  computed: {
    // Título dinámico basado en el artículo
    dynamicSectionTitle() {
      return this.article?.title || this.sectionTitle
    },
    // Subtítulo dinámico basado en el artículo
    dynamicSectionSubtitle() {
      return this.article?.subtitle || this.sectionSubtitle
    },
    // Base URL para imágenes (expuesta por plugin como $apiImgBase)
    apiImgBase() {
      return this.$apiImgBase || ''
    },
    cards() {
      // Modo dinámico por props
      if (this.items && this.mapItemToCard) {
        return this.items.map(item => this.mapItemToCard(item))
      }
      // Fallback: categorías actuales
      const cards = this.categories.map(category => {
        const slug = this.getCategorySlug(category.name)
        return {
          title: category.name,
          description: category.description,
          cover: this.buildImg(category.image),
          to: `/que-hacer/${slug}`,
          icon: category.icon,
          color: category.color
        }
      })
      console.log('Generated cards:', cards)
      return cards
    }
  },
  methods: {
    async loadCategories() {
      try {
        // Si vienen items externos, no cargamos categorías
        if (this.items && this.mapItemToCard) {
          this.loading = false
          this.error = null
          return
        }

        this.loading = true
        this.error = null
        
        const response = await categoriesService.getAllCategories()
        
        if (response.success && response.data) {
          // Transformar las categorías para que sean compatibles con InterestCard
          this.categories = response.data.map(category => 
            categoriesService.transformToInterestCard(category)
          )
        } else {
          throw new Error('Error al cargar las categorías')
        }
      } catch (error) {
        console.error('Error loading categories:', error)
        this.error = 'No se pudieron cargar las categorías'
        // Fallback a categorías vacías o mostrar mensaje de error
        this.categories = []
      } finally {
        this.loading = false
      }
    },
    buildImg(url) {
      return this.$buildImg ? this.$buildImg(url) : url || ''
    },
    
    navigateToCategory(category) {
      // Todas las categorías navegan a CulturaPage con contenido dinámico
      const slug = this.getCategorySlug(category.name)
      console.log('Navigating to category:', category.name, 'with slug:', slug, 'category ID:', category.id)
      this.$router.push(`/que-hacer/${slug}`)
    },
    getCategorySlug(categoryName) {
      return categoriesService.getCategorySlug(categoryName)
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
      if (this.displayMode !== 'carousel') return
      const el = this.$refs.interestsGridRef
      if (!el) return
      const scrollLeft = el.scrollLeft
      const scrollWidth = el.scrollWidth - el.clientWidth
      const progress = scrollWidth > 0 ? (scrollLeft / scrollWidth) * 100 : 0
      const bar = this.$refs.scrollProgress
      if (bar) bar.style.width = `${progress}%`
    },
    handleMouseDown(e) {
      if (this.displayMode !== 'carousel') return
      const el = this.$refs.interestsGridRef
      if (!el) return
      this.isDragging = true
      this.dragMoved = false
      // Igual que FactsSection: no tocar scrollBehavior, confiar en scrollBy/scrollLeft
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
      if (Math.abs(walk) > 5) this.dragMoved = true
      this.updateScrollProgress()
    },
    handleMouseUp() {
      if (!this.isDragging) return
      this.isDragging = false
      const el = this.$refs.interestsGridRef
      if (el) el.classList.remove('dragging')
    },
    cancelClickOnDrag(e) {
      if (this.dragMoved) {
        e.preventDefault()
        e.stopPropagation()
        this.dragMoved = false
      }
    },
    handleTouchStart(e) {
      if (this.displayMode !== 'carousel') return
      const el = this.$refs.interestsGridRef
      if (!el) return
      this.isDragging = true
      this.dragMoved = false
      el.classList.add('dragging')
      this.startX = e.touches[0].pageX - el.offsetLeft
      this.scrollLeft = el.scrollLeft
      // No prevenir el evento por defecto aquí para permitir clicks
    },
    handleTouchMove(e) {
      const el = this.$refs.interestsGridRef
      if (!this.isDragging || !el) return
      const x = e.touches[0].pageX - el.offsetLeft
      const walk = (x - this.startX) * 1.5
      
      // Solo prevenir el evento si hay movimiento significativo
      if (Math.abs(walk) > 10) {
        e.preventDefault()
        el.scrollLeft = this.scrollLeft - walk
        this.dragMoved = true
        this.updateScrollProgress()
      }
    },
    handleTouchEnd() {
      if (!this.isDragging) return
      this.isDragging = false
      const el = this.$refs.interestsGridRef
      if (el) el.classList.remove('dragging')
      
      // Resetear dragMoved después de un pequeño delay para permitir clicks
      setTimeout(() => {
        this.dragMoved = false
      }, 100)
    },
    getItemByIndex(idx) {
      return this.items ? this.items[idx] : this.categories[idx]
    },
    handleCardClick(item) {
      if (!item) return
      if (this.onItemClick) {
        this.onItemClick(item)
        return
      }
      // Fallback para categorías
      if (!this.items) {
        this.navigateToCategory(item)
      }
    }
  }
}
</script>

<style scoped>
/* Intereses */
.interests-section {
  padding: 4rem 0;

  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.section-header {
  margin-bottom: 3rem;
  text-align: left;
}

.section-title {
  font-family: var(--font-family-base);
  font-size: 2.75rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
  line-height: 1.2;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -0.5rem;
  left: 0;
  width: 50px;
  height: 3px;
  background: linear-gradient(90deg, var(--primary-blue), var(--accent-blue));
  border-radius: 2px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1.5rem;
  margin-top: 1rem;
  flex-wrap: wrap;
}

.section-subtitle {
  font-family: var(--font-family-base);
  font-size: 1.125rem;
  font-weight: var(--font-weight-regular);
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
  flex: 1;
  min-width: 200px;
}

.header-actions { margin-bottom: 1.5rem; }

/* Botón minimalista (alineado al de EventsSection) */
.minimal-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  border: 2px solid var(--border-light);
  background: transparent;
  color: var(--text-secondary);
  letter-spacing: 0.025em;
  cursor: pointer;
}

.minimal-btn:hover {
  background: var(--bg-secondary);
  color: var(--text-primary);
  border-color: var(--border-color);
  transform: translateY(-1px);
  text-decoration: none;
}

.minimal-btn:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

.interests-container {
  position: relative;
}


.interests-grid {
  display: grid;
  grid-auto-flow: column;
  /* Por defecto (desktop ancho): 4 items exactos */
  gap: 2rem;
  grid-auto-columns: calc((100% - (3 * 2rem)) / 4);
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
  padding-right: 0;
  padding-bottom: 1rem;
  -ms-overflow-style: none;
  cursor: grab;
  user-select: none;
  touch-action: pan-x;
  -webkit-user-select: none;
  -ms-user-select: none;
}

.interests-grid::-webkit-scrollbar { display: none; }

.interests-grid > * {
  scroll-snap-align: start; /* alinear cada tarjeta al inicio */
  scroll-snap-stop: always; /* evitar quedarse "a medias" entre tarjetas */
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

/* Grid layout para SearchPage */
.interests-grid-layout {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  padding: 0;
  overflow: visible;
  cursor: default;
  user-select: auto;
}

.interests-grid-layout > * {
  scroll-snap-align: none;
}

/* Ajustar indicador en móviles */
@media (max-width: 768px) {
  .interests-grid {
    /* Un item por vista en móvil */
    grid-auto-columns: 100%;
    gap: 1rem;
  }
  .interests-grid-layout {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
  }
  
  .section-header {
    margin-bottom: 2rem;
  }
  
  .section-title {
    font-size: 2.25rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    margin-top: 0.75rem;
  }
  
  .section-subtitle {
    font-size: 1rem;
    min-width: auto;
  }
}

/* 2 items por vista en tablets (769px - 1024px) */
@media (min-width: 769px) and (max-width: 1024px) {
  .interests-grid {
    gap: 1.5rem;
    grid-auto-columns: calc((100% - (1 * 1.5rem)) / 2);
  }
}

/* 3 items por vista en laptops medianas (1025px - 1399px) */
@media (min-width: 1025px) and (max-width: 1399px) {
  .interests-grid {
    gap: 1.75rem;
    grid-auto-columns: calc((100% - (2 * 1.75rem)) / 3);
  }
}

/* 4 items exactos en >= 1400px (ya definido por defecto) */

@media (max-width: 480px) {
  .section-title {
    font-size: 1.875rem;
  }
  
  .section-subtitle {
    font-size: 0.95rem;
  }
}
</style>
