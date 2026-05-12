<template>
  <section class="hero-split">
    <div class="container-fluid">
      <div class="row align-items-center">
        <!-- Contenido izquierdo -->
        <div class="col-lg-6">
          <div class="hero-content">
            <h1 class="hero-title">{{ title || 'Es hora de empezar a explorar...' }}</h1>
            
            <!-- Navegación por pestañas -->
            <div class="tab-navigation">
              <button class="tab-button" :class="{ active: activeTab === 'regions' }" @click="changeTab('regions')">
                Regiones
              </button>
              <button class="tab-button" :class="{ active: activeTab === 'cities' }" @click="changeTab('cities')">
                Ciudades
              </button>
              <button class="tab-button" :class="{ active: activeTab === 'nature' }" @click="changeTab('nature')">
                Naturaleza
              </button>
            </div>
            
            <!-- Lista de destinos -->
            <div class="destinations-list" v-if="activeTab === 'regions'">
              <div v-if="loading" class="loading-state">
                <div class="spinner"></div>
                <p>Cargando regiones...</p>
              </div>
              <div v-else-if="error" class="error-state">
                <p>{{ error }}</p>
              </div>
              <div v-else>
                <!-- Debug info -->
                <div v-if="regions.length === 0" class="debug-info">
                  <p>⚠️ No se encontraron regiones. Total de ubicaciones: {{ allLocations.length }}</p>
                  <p>Tipos de ubicaciones disponibles: {{ [...new Set(allLocations.map(l => l.type))].join(', ') }}</p>
                </div>
                
              <div class="destinations-grid">
                <div class="destinations-column">
                    <router-link 
                      v-for="region in regions.slice(0, 3)" 
                      :key="region.id"
                      :to="getLocationRoute(region)"
                      class="destination-item"
                    >
                    <span class="arrow">→</span>
                    <span class="destination-name">{{ region.nombre }}</span>
                    </router-link>
                </div>
                <div class="destinations-column">
                    <router-link 
                      v-for="region in regions.slice(3, 5)" 
                      :key="region.id"
                      :to="getLocationRoute(region)"
                      class="destination-item"
                    >
                    <span class="arrow">→</span>
                    <span class="destination-name">{{ region.nombre }}</span>
                    </router-link>
                </div>
              </div>
              <router-link to="/regiones" class="all-destinations-link">
                ↓ Todas las regiones ({{ regions.length }} disponibles)
              </router-link>
              </div>
            </div>
            
            <div class="destinations-list" v-if="activeTab === 'cities'">
              <div v-if="loading" class="loading-state">
                <div class="spinner"></div>
                <p>Cargando ciudades...</p>
              </div>
              <div v-else-if="error" class="error-state">
                <p>{{ error }}</p>
              </div>
              <div v-else>
                <!-- Debug info -->
                <div v-if="cities.length === 0" class="debug-info">
                  <p>⚠️ No se encontraron ciudades. Total de ubicaciones: {{ allLocations.length }}</p>
                  <p>Ciudades encontradas: {{ allLocations.filter(l => l.type === 'city').length }}</p>
                </div>
              <div class="destinations-grid">
                <div class="destinations-column">
                    <router-link 
                      v-for="city in cities.slice(0, 3)" 
                      :key="city.id"
                      :to="getLocationRoute(city)"
                      class="destination-item"
                    >
                    <span class="arrow">→</span>
                    <span class="destination-name">{{ city.nombre }}</span>
                    </router-link>
                </div>
                <div class="destinations-column">
                    <router-link 
                      v-for="city in cities.slice(3, 6)" 
                      :key="city.id"
                      :to="getLocationRoute(city)"
                      class="destination-item"
                    >
                    <span class="arrow">→</span>
                    <span class="destination-name">{{ city.nombre }}</span>
                    </router-link>
                </div>
              </div>
              <router-link to="/ciudades" class="all-destinations-link">
                ↓ Todas las ciudades ({{ cities.length }} disponibles)
              </router-link>
              </div>
            </div>
            
            <div class="destinations-list" v-if="activeTab === 'nature'">
              <div v-if="loading" class="loading-state">
                <div class="spinner"></div>
                <p>Cargando sitios naturales...</p>
              </div>
              <div v-else-if="error" class="error-state">
                <p>{{ error }}</p>
              </div>
              <div v-else>
                <!-- Debug info -->
                <div v-if="natureSites.length === 0" class="debug-info">
                  <p>⚠️ No se encontraron sitios naturales. Total de ubicaciones: {{ allLocations.length }}</p>
                  <p>Atracciones encontradas: {{ allLocations.filter(l => l.type === 'attraction').length }}</p>
                </div>
              <div class="destinations-grid">
                <div class="destinations-column">
                    <router-link 
                      v-for="nature in natureSites.slice(0, 3)" 
                      :key="nature.id"
                      :to="getLocationRoute(nature)"
                      class="destination-item"
                    >
                    <span class="arrow">→</span>
                    <span class="destination-name">{{ nature.nombre }}</span>
                    </router-link>
                </div>
                <div class="destinations-column">
                    <router-link 
                      v-for="nature in natureSites.slice(3, 6)" 
                      :key="nature.id"
                      :to="getLocationRoute(nature)"
                      class="destination-item"
                    >
                    <span class="arrow">→</span>
                    <span class="destination-name">{{ nature.nombre }}</span>
                    </router-link>
                </div>
              </div>
              <router-link to="/naturaleza" class="all-destinations-link">
                ↓ Todos los sitios naturales ({{ natureSites.length }} disponibles)
              </router-link>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Mapa interactivo profesional -->
        <div class="col-lg-6">
          <div class="map-section">
            <div class="map-header">
              <h3 class="map-title">Empieza a planear</h3>
              <p class="map-subtitle">Usa el mapa interactivo para descubrir destinos y diseñar tu plan de viaje</p>
            </div>
            <div class="map-container">
              <InteractiveMap 
                :lugares="mapData" 
                :center="mapCenter" 
                :zoom="mapZoom" 
                height="400px" 
              />
            </div>
            <div class="map-legend">
              <div v-if="activeTab === 'regions'" class="legend-item">
                <div class="legend-icon region"></div>
                <span>Regiones</span>
              </div>
              <div v-if="activeTab === 'cities'" class="legend-item">
                <div class="legend-icon ciudad"></div>
                <span>Ciudades</span>
              </div>
              <div v-if="activeTab === 'nature'" class="legend-item">
                <div class="legend-icon naturaleza"></div>
                <span>Sitios Naturales</span>
              </div>
              <div class="legend-item">
                <div class="legend-icon destacado"></div>
                <span>Destacado</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import InteractiveMap from '@/components/InteractiveMap.vue'
import { getAllLocations, filterLocationsByTab, getLocationRoute } from '@/services/locations.service'

export default {
  name: 'WhereToGoHeroSection',
  components: {
    InteractiveMap
  },
  props: {
    title: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      activeTab: 'regions',
      allLocations: [],
      loading: false,
      error: null
    }
  },
  computed: {
    mapData() {
      const data = filterLocationsByTab(this.allLocations, this.activeTab)
      console.log('🗺️ WhereToGoHero: Map data for', this.activeTab, ':', data)
      return data
    },
    
    regions() {
      const regions = filterLocationsByTab(this.allLocations, 'regions')
      console.log('🏔️ WhereToGoHero: Regions computed:', regions)
      return regions
    },
    
    cities() {
      const cities = filterLocationsByTab(this.allLocations, 'cities')
      console.log('🏙️ WhereToGoHero: Cities computed:', cities)
      return cities
    },
    
    natureSites() {
      const nature = filterLocationsByTab(this.allLocations, 'nature')
      console.log('🌿 WhereToGoHero: Nature sites computed:', nature)
      return nature
    },
    mapCenter() {
      switch (this.activeTab) {
        case 'regions':
          return [-16.5, -68.15]; // Centro de Bolivia
        case 'cities':
          return [-17.5, -66.5]; // Centro de las ciudades principales
        case 'nature':
          return [-17.0, -67.0]; // Centro de sitios naturales
        default:
          return [-16.5, -68.15];
      }
    },
    mapZoom() {
      switch (this.activeTab) {
        case 'regions':
          return 6; // Vista general de Bolivia
        case 'cities':
          return 7; // Zoom más cercano para ciudades
        case 'nature':
          return 6; // Vista general para sitios naturales
        default:
          return 6;
      }
    }
  },
  async mounted() {
    await this.loadLocations()
  },
  methods: {
    async loadLocations() {
      try {
        this.loading = true
        this.error = null
        console.log('🌍 WhereToGoHero: Cargando ubicaciones...')
        
        const response = await getAllLocations()
        console.log('📡 WhereToGoHero: Respuesta completa de la API:', response)
        
        if (response.success && Array.isArray(response.data)) {
          this.allLocations = response.data
          console.log('✅ WhereToGoHero: Ubicaciones cargadas:', this.allLocations.length)
          console.log('📋 WhereToGoHero: Primeras 3 ubicaciones:', this.allLocations.slice(0, 3))
        } else {
          console.error('❌ WhereToGoHero: Respuesta inválida:', response)
          throw new Error('Error en la respuesta de la API')
        }
      } catch (error) {
        console.error('❌ WhereToGoHero: Error cargando ubicaciones:', error)
        this.error = 'Error al cargar las ubicaciones'
        this.allLocations = []
      } finally {
        this.loading = false
      }
    },
    
    changeTab(tab) {
      console.log('🔄 WhereToGoHero: Cambiando pestaña a:', tab)
      this.activeTab = tab
    },
    
    getLocationRoute(location) {
      return getLocationRoute(location)
    }
  }
}
</script>

<style scoped>
/* Hero section con layout dividido */
.hero-split {
  padding: 6rem 0;
  min-height: 80vh;
  display: flex;
  align-items: center;
}

.hero-content {
  padding: 2rem;
}

.hero-title { 
  font-size: 3.5rem !important; 
  font-weight: 300 !important; 
  color: var(--text-primary) !important;
  margin-bottom: 3rem;
  font-family: var(--font-family-base) !important;
  letter-spacing: -0.02em;
  line-height: 1.1;
}

/* Navegación por pestañas */
.tab-navigation {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
}

.tab-button {
  background: none;
  border: none;
  color: var(--text-secondary);
  font-size: 1.125rem;
  font-weight: 500;
  padding: 0.5rem 0;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  font-family: var(--font-family-base) !important;
}

.tab-button:hover {
  color: var(--text-primary);
}

.tab-button.active {
  color: var(--text-primary);
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: -0.5rem;
  left: 0;
  width: 120%;
  height: 3px;
  background: var(--primary-blue);
  border-radius: 2px;
}

/* Lista de destinos */
.destinations-list {
  margin-top: 2rem;
}

.destinations-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.destinations-column {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.destination-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  color: inherit;
}

.destination-item:hover {
  transform: translateX(5px);
  text-decoration: none;
}

.destination-item:visited {
  text-decoration: none;
}

.destination-item:focus {
  text-decoration: none;
  outline: none;
}

.arrow {
  color: var(--primary-blue);
  font-size: 1.25rem;
  font-weight: 500;
}

.destination-name {
  color: var(--text-primary);
  font-size: 1.125rem;
  font-weight: 500;
  font-family: var(--font-family-base) !important;
}

.all-destinations-link {
  color: var(--text-primary);
  text-decoration: none;
  font-weight: 500;
  font-size: 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
  font-family: var(--font-family-base) !important;
}

.all-destinations-link:hover {
  color: var(--primary-blue);
  transform: translateY(-2px);
  text-decoration: none;
}

.all-destinations-link:visited {
  text-decoration: none;
}

.all-destinations-link:focus {
  text-decoration: none;
  outline: none;
}

/* Mapa interactivo profesional */
.map-section {
  padding: 2rem;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.map-header {
  text-align: center;
  margin-bottom: 2rem;
}

.map-title {
  font-size: 1.5rem !important;
  font-weight: var(--font-weight-semibold) !important;
  color: var(--text-primary) !important;
  margin-bottom: 0.5rem;
  font-family: var(--font-family-base) !important;
}

.map-subtitle {
  color: var(--text-secondary) !important;
  font-size: 0.95rem !important;
  font-family: var(--font-family-base) !important;
  margin-bottom: 0;
}

.map-container {
  background: var(--white);
  border-radius: 16px;
  padding: 1rem;
  box-shadow: var(--shadow-md);
  margin-bottom: 2rem;
  min-height: 400px;
  overflow: hidden;
  flex: 1;
}

/* Leyenda del Mapa */
.map-legend {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.legend-icon {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid var(--white);
  box-shadow: var(--shadow-sm);
}

.legend-icon.region {
  background: #3b82f6;
}

.legend-icon.ciudad {
  background: #10b981;
}

.legend-icon.naturaleza {
  background: #059669;
}

.legend-icon.destacado {
  background: #f59e0b;
}

/* Estados de carga y error */
.loading-state, .error-state, .debug-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
}

.debug-info {
  background-color: #fff3cd;
  border: 1px solid #ffeaa7;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.debug-info p {
  margin: 0.5rem 0;
  color: #856404;
  font-size: 0.9rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--border-light);
  border-top: 4px solid var(--primary-blue);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-state p, .error-state p {
  color: var(--text-secondary);
  font-size: 1rem;
  margin: 0;
}

.error-state p {
  color: var(--danger);
}

/* Responsive */
@media (max-width: 991.98px) {
  .hero-split {
    padding: 4rem 0;
  }
  
  .hero-title {
    font-size: 2.5rem !important;
    margin-bottom: 2rem;
  }
  
  .tab-navigation {
    gap: 1.5rem;
  }
  
  .destinations-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .map-section {
    margin-top: 3rem;
  }
  
  .map-container {
    min-height: 300px;
  }
  
  .map-legend {
    gap: 1rem;
  }
}

@media (max-width: 767.98px) {
  .hero-split {
    padding: 3rem 0;
  }
  
  .hero-title {
    font-size: 2rem !important;
  }
  
  .tab-navigation {
    flex-direction: column;
    gap: 1rem;
  }
  
  .tab-button {
    text-align: left;
  }
}
</style>

