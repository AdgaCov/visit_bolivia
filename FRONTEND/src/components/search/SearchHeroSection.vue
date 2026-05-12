<template>
  <section class="hero-section">
    <div class="container">
      <div class="hero-content text-center">
        <h1 class="hero-title">Tu Próximo Destino</h1>
        <!-- <p class="hero-subtitle">Los tesoros ocultos de Bolivia te esperan</p> -->
        <!-- <div class="hero-cta">
          <a href="#interests" class="btn btn-primary me-2">Comienza a explorar</a>
          <router-link to="/lugares" class="btn btn-secondary">Ver todos los lugares</router-link>
        </div> -->
        
        <!-- Filtros de Categorías - Estilo Google chips -->
        <div class="category-filters">
          <!-- Tabs fijos: Todos y Hospedaje -->
          <button 
            class="filter-pill"
            :class="{ active: currentTab === 'todos' }"
            @click="setTab('todos')"
          >
            <i class="fas fa-globe"></i>
            Todos
          </button>
          <button 
            class="filter-pill"
            :class="{ active: currentTab === 'alojamiento' }"
            @click="setTab('alojamiento')"
          >
            <i class="fas fa-bed"></i>
            Hospedaje
          </button>
          
          <!-- Tabs dinámicos desde categorías de la API -->
          <button 
            v-for="tab in categoryTabs"
            :key="tab.id"
            class="filter-pill"
            :class="{ active: currentTab === tab.id }"
            @click="setTab(tab.id)"
          >
            <i :class="tab.icon"></i>
            {{ tab.label }}
          </button>
        </div>
        
        <!-- Barra de Búsqueda -->
        <div class="container_search">
          <div class="search-container">
            <button class="decorative-btn" type="button" aria-hidden="true">
              <i class="fas fa-search"></i>
            </button>
            <input 
              type="text" 
              class="search-input" 
              placeholder="Busca destinos, ciudades o experiencias"
              v-model="localSearchQuery"
              @input="handleInput"
              @keyup.enter="handleSearch"
              @focus="localSearchQuery.length >= 2 && $emit('generate-suggestions', localSearchQuery)"
            >
            <button
              v-if="localSearchQuery"
              class="clear-input-btn"
              type="button"
              aria-label="Borrar búsqueda"
              @click="clearSearch"
            >
              <i class="fas fa-times"></i>
            </button>
            <div class="search-actions">
              <!-- <button class="decorative-btn" type="button" aria-label="Búsqueda por voz">
                <i class="fas fa-microphone"></i>
              </button>
              <button class="decorative-btn" type="button" aria-label="Búsqueda visual">
                <i class="fas fa-camera"></i>
              </button> -->
              <button class="search-button" @click="handleSearch" :disabled="isSearching">
                <i v-if="isSearching" class="fas fa-spinner fa-spin"></i>
                <span v-else>Buscar</span>
              </button>
            </div>
            
            <!-- Sugerencias de búsqueda -->
            <div v-if="showSuggestions && searchSuggestions.length > 0" class="search-suggestions">
              <div 
                v-for="(suggestion, index) in searchSuggestions" 
                :key="index"
                class="suggestion-item"
                @click="handleSuggestionClick(suggestion)"
              >
                <i class="fas fa-search suggestion-icon"></i>
                <span class="suggestion-text">{{ suggestion }}</span>
              </div>
            </div>
          </div>
        
          <!-- Tabs de Resultados -->
          <div class="results-tabs">
            <button class="tab-button active">
              Todos los resultados ({{ totalResults }})
            </button>
            <!-- <button class="tab-button">
              Lugares & Eventos ({{ placesResults }})
            </button>
            <button class="tab-button">
              Inspiración
            </button> -->
          </div>
        </div>
        
        <!-- Filtros de Refinamiento -->
        <!-- <div class="refine-filters">
          <h6 class="refine-title">Refina tu búsqueda</h6>
          <div class="filter-chips">
            <button class="filter-chip">CASA DE HUÉSPEDES</button>
            <button class="filter-chip">HOTEL</button>
            <button class="filter-chip">FAMILIAR</button>
            <button class="filter-chip">MASCOTAS</button>
            <button class="filter-chip">CAMPING</button>
            <button class="filter-chip">SALAR DE UYUNI</button>
          </div>
        </div> -->
      </div>
    </div>
  </section>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import categoriesService from '@/services/categories.service';

export default {
  name: 'SearchHeroSection',
  props: {
    currentTab: {
      type: String,
      default: 'todos'
    },
    totalResults: {
      type: Number,
      default: 679
    },
    placesResults: {
      type: Number,
      default: 651
    },
    searchQuery: {
      type: String,
      default: ''
    },
    isSearching: {
      type: Boolean,
      default: false
    },
    searchSuggestions: {
      type: Array,
      default: () => []
    },
    showSuggestions: {
      type: Boolean,
      default: false
    },
    categoryTabs: {
      type: Array,
      default: () => []
    }
  },
  emits: ['tab-changed', 'search-performed', 'generate-suggestions', 'select-suggestion'],
  setup(props, { emit }) {
    const localSearchQuery = ref(props.searchQuery || '')

    // Sincronizar con prop
    watch(() => props.searchQuery, (newQuery) => {
      localSearchQuery.value = newQuery || ''
    })

    const setTab = (tab) => {
      // Dejar que el padre maneje la navegación para que back/forward funcionen
      emit('tab-changed', tab)
    }

    const handleSearch = () => {
      if (localSearchQuery.value.trim()) {
        emit('search-performed', localSearchQuery.value.trim())
      }
    }

    const handleInput = (event) => {
      localSearchQuery.value = event.target.value
      console.log('SearchHeroSection handleInput:', localSearchQuery.value)
      
      // Generar sugerencias mientras el usuario escribe
      if (localSearchQuery.value.length >= 2) {
        emit('generate-suggestions', localSearchQuery.value)
      }
      
      // Emitir el evento de búsqueda para que el padre maneje el debounce
      emit('search-performed', localSearchQuery.value)
    }

    const clearSearch = () => {
      if (!localSearchQuery.value) return
      localSearchQuery.value = ''
      emit('search-performed', '')
      emit('generate-suggestions', '')
    }

    const handleSuggestionClick = (suggestion) => {
      emit('select-suggestion', suggestion)
    }

    onMounted(() => {
      console.log('SearchHeroSection mounted with props:', {
        currentTab: props.currentTab,
        searchQuery: props.searchQuery,
        totalResults: props.totalResults
      })
    })

    watch(() => props.currentTab, (tab) => {
      // Hook para futuras cargas de datos por tab
    })

    return {
      localSearchQuery,
      setTab,
      handleSearch,
      handleInput,
      handleSuggestionClick,
      clearSearch
    }
  }
}
</script>

<style scoped>
.hero-section {
  padding: 5rem 0 2rem;
  background: #fff;
}

.hero-content {
  max-width: 1200px;
  margin: 0 auto;
}

.category-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: center;
  margin-bottom: 2.5rem;
}

.filter-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.25rem;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
  border: 1px solid transparent;
  background: #f1f3f4;
  color: #5f6368;
  cursor: pointer;
}

.filter-pill:hover {
  background: #e8eaed;
  color: #202124;
}

.filter-pill.active {
  background: #e8f0fe;
  color: #1967d2;
  border-color: #d2e3fc;
}

.search-container {
  position: relative;
  margin: 0 auto 2rem;
  width: min(680px, 100%);
  display: flex;
  align-items: center;
  padding: 0.25rem 1rem;
  border-radius: 999px;
  background: #fff;
  box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
}

.container_search {
  position: relative;
  display: block;
  margin: 0 auto;
  padding-left: 1rem;
  padding-right: 1rem;
}

.search-input-wrapper {
  position: relative;
}

.decorative-btn {
  border: none;
  background: transparent;
  color: #5f6368;
  font-size: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  cursor: pointer;
}

.decorative-btn:hover {
  color: #202124;
}

.clear-input-btn {
  border: none;
  background: transparent;
  color: #5f6368;
  font-size: 0.9rem;
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.clear-input-btn:hover {
  color: #202124;
}

.search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 1rem;
  padding: 0.75rem 0.5rem;
  color: #202124;
}

.search-input:focus {
  outline: none;
}

.search-input::placeholder {
  color: #5f6368;
}

.search-actions {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.search-button {
  border: none;
  background: transparent;
  color: #1a73e8;
  font-weight: 500;
  padding: 0.35rem 0.85rem;
  border-radius: 999px;
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
}

.search-button:hover {
  background: rgba(26, 115, 232, 0.12);
}

.search-button:disabled {
  color: #9aa0a6;
  cursor: not-allowed;
}

.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: var(--white);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  box-shadow: var(--shadow-lg);
  z-index: 1000;
  max-height: 300px;
  overflow-y: auto;
  margin-top: 4px;
}

.suggestion-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
  border-bottom: 1px solid var(--border-light);
}

.suggestion-item:last-child {
  border-bottom: none;
}

.suggestion-item:hover {
  background-color: #f8f9fa;
}

.suggestion-icon {
  color: var(--text-muted);
  font-size: 0.875rem;
  width: 16px;
  text-align: center;
}

.suggestion-text {
  color: var(--text-primary);
  font-size: 0.875rem;
  font-weight: 400;
}

/* Tabs de Resultados */
.results-tabs {
  display: flex;
  gap: 1.5rem;
  width: min(680px, 100%);
  margin: 0 auto 1.5rem;
  padding-left: 1.5rem;
  border-bottom: 1px solid #dadce0;
}

.tab-button {
  background: none;
  border: none;
  color: #5f6368;
  font-size: 0.95rem;
  font-weight: 500;
  padding: 0.75rem 0;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.tab-button:hover {
  color: #202124;
}

.tab-button.active {
  color: #1a73e8;
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  height: 3px;
  background: #1a73e8;
}

/* Filtros de Refinamiento */
.refine-filters {
  text-align: center;
}

.refine-title {
  color: var(--text-secondary);
  font-weight: 600;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 1rem;
}

.filter-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: center;
}

.filter-chip {
  padding: 0.5rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 50px;
  background: var(--white);
  color: var(--text-primary);
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-chip:hover {
  border-color: var(--primary-blue);
  color: var(--primary-blue);
  background: var(--light-gray);
}

/* Responsive */
@media (max-width: 768px) {
  .category-filters { gap: 0.6rem; }
  .filter-pill { padding: 0.5rem 1rem; font-size: 0.8rem; }
  .search-container { width: 100%; }
  .results-tabs { width: 100%; padding-left: 0; justify-content: center; }
}

/* Tablet */
@media (max-width: 992px) {
  .container_search { max-width: 900px; }
  .results-tabs { max-width: 900px; }
}

/* Móvil pequeño */
@media (max-width: 576px) {
  .category-filters { gap: 0.5rem; }
  .results-tabs { gap: 1rem; }
  .tab-button { font-size: 0.9rem; }
}
</style>
