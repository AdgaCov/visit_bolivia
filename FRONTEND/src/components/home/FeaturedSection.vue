<template>
  <section class="featured-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">{{ title }}</h2>
        <p class="section-subtitle">{{ subtitle }}</p>
      </div>
      
      <!-- Loading state -->
      <div v-if="loading" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Cargando lugares destacados...</p>
      </div>
      
      <!-- Error state -->
      <div v-else-if="error" class="error-container">
        <p class="error-message">{{ error }}</p>
        <button @click="loadFeaturedPlaces" class="retry-button">Reintentar</button>
      </div>
      
      <!-- Places grid -->
      <div v-else class="places-grid">
        <InterestCard
          v-for="place in featuredPlaces"
          :key="place.id"
          :title="place.name"
          :description="place.description"
          :cover="place.image"
          :badge="place.badge"
          :icon="''"
          :color="place.categoryColor"
          :to="place.to"
        />
      </div>
    </div>
  </section>
</template>

<script>
import InterestCard from '@/components/home/InterestCard.vue'
import { getFeaturedLocations, getLocationsWithSubcategories } from '@/services/locations.service'

export default {
  name: 'FeaturedSection',
  components: {
    InterestCard
  },
  props: {
    title: {
      type: String,
      default: 'Lugares Destacados'
    },
    subtitle: {
      type: String,
      default: 'Descubre los destinos más emblemáticos de Bolivia, desde maravillas naturales hasta joyas arquitectónicas que capturan la esencia de nuestro país.'
    },
    offset: {
      type: Number,
      default: 0
    },
    limit: {
      type: Number,
      default: 4
    }
  },
  data() {
    return {
      featuredPlaces: [],
      loading: false,
      error: null
    }
  },
  async mounted() {
    await this.loadFeaturedPlaces()
  },
  methods: {
    async loadFeaturedPlaces() {
      try {
        this.loading = true
        this.error = null
        
        // Primero intentar obtener lugares destacados
        let response = await getFeaturedLocations()
        console.log('FeaturedSection: Featured places from API:', response.data?.length || 0)
        
        // Si no hay suficientes items destacados, usar todos los lugares con subcategorías
        if (!response.success || !Array.isArray(response.data) || response.data.length < (this.offset + this.limit)) {
          console.log('⚠️ FeaturedSection: No hay suficientes lugares destacados, usando todos los lugares')
          response = await getLocationsWithSubcategories()
          console.log('FeaturedSection: Total places from API (with subcategories):', response.data?.length || 0)
        }
        
        if (response.success && Array.isArray(response.data)) {
          // Aplicar offset y limit para mostrar diferentes rangos
          const startIndex = this.offset
          const endIndex = Math.min(this.offset + this.limit, response.data.length)
          const slicedData = response.data.slice(startIndex, endIndex)
          
          console.log('FeaturedSection: Total items from API:', response.data.length)
          console.log('FeaturedSection: Using offset:', this.offset, 'limit:', this.limit)
          console.log('FeaturedSection: Slice range:', startIndex, 'to', endIndex)
          console.log('FeaturedSection: Sliced data length:', slicedData.length)
          
          this.featuredPlaces = slicedData.map(location => this.mapLocationToCard(location))
        } else {
          throw new Error('Error al cargar los lugares destacados')
        }
      } catch (error) {
        console.error('Error loading featured places:', error)
        this.error = 'No se pudieron cargar los lugares destacados'
        // Fallback a datos estáticos en caso de error
        const fallbackPlaces = this.getFallbackPlaces()
        const startIndex = this.offset
        const endIndex = this.offset + this.limit
        this.featuredPlaces = fallbackPlaces.slice(startIndex, endIndex)
      } finally {
        this.loading = false
      }
    },
    mapLocationToCard(location) {
      const mainImage = location.images?.find(img => img.is_main) || location.images?.[0]
      const rawImageUrl = mainImage?.image_url || '/images/placeholder.jpg'
      
      // ✅ Procesar la URL con $buildImg
      const processedImageUrl = this.$buildImg ? this.$buildImg(rawImageUrl) : rawImageUrl
      
      return {
        id: location.id,
        name: location.name,
        description: location.description,
        image: processedImageUrl,
        category: this.getCategoryFromType(location.type),
        categoryColor: this.getCategoryColor(location.type),
        location: location.department?.name || 'Bolivia',
        rating: parseFloat(location.average_rating) || 0,
        badge: location.badge || this.getCategoryFromType(location.type),
        to: this.getLocationRoute(location)
      }
    },
    // mapLocationToCard(location) {
    //   const mainImage = location.images?.find(img => img.is_main) || location.images?.[0]
    //   const imageUrl = mainImage?.image_url || '/images/placeholder.jpg'
      
    //   return {
    //     id: location.id,
    //     name: location.name,
    //     description: location.description,
    //     image: imageUrl,
    //     category: this.getCategoryFromType(location.type),
    //     categoryColor: this.getCategoryColor(location.type),
    //     location: location.department?.name || 'Bolivia',
    //     rating: parseFloat(location.average_rating) || 0,
    //     badge: location.badge || this.getCategoryFromType(location.type),
    //     to: this.getLocationRoute(location)
    //   }
    // },
    
    getCategoryFromType(type) {
      const typeMap = {
        'attraction': 'Atracción',
        'accommodation': 'Alojamiento',
        'restaurant': 'Gastronomía',
        'museum': 'Museo',
        'event': 'Evento',
        'city': 'Ciudad'
      }
      return typeMap[type] || 'Lugar'
    },
    
    getCategoryColor(type) {
      const colorMap = {
        'attraction': '#16A34A',
        'accommodation': '#1e40af',
        'restaurant': '#D97706',
        'museum': '#7c3aed',
        'event': '#dc2626',
        'city': '#059669'
      }
      return colorMap[type] || '#6B7280'
    },
    
    getLocationRoute(location) {
      const typeMap = {
        'attraction': 'AttractionDetail',
        'accommodation': 'AccommodationDetail',
        'restaurant': 'GastronomyDetail',
        'museum': 'MuseumDetail',
        'city': 'CityDetail'
      }
      
      const routeName = typeMap[location.type]
      if (routeName) {
        return { name: routeName, params: { id: location.id } }
      }
      return null
    },
    
    getFallbackPlaces() {
      return [
        {
          id: 1,
          name: 'Salar de Uyuni',
          description: 'El desierto de sal más grande del mundo, un espejo natural que refleja el cielo',
          image: 'https://denomades-blog.imgix.net/blog/wp-content/uploads/2020/11/02132247/salar-de-uyuni-3-dias-san-pedro-de-atacama-a-uyuni-id209-2-es.jpg?auto=compress%2Cformat&ixlib=php-3.3.1',
          category: 'Naturaleza',
          categoryColor: '#16A34A',
          location: 'Potosí',
          rating: 4.9,
          badge: 'Naturaleza',
          to: null
        },
        {
          id: 2,
          name: 'Tiwanaku',
          description: 'Sitio arqueológico precolombino, centro espiritual de la cultura Tiwanaku',
          image: 'https://cdn.kanootours.com/media/catalog/product/w/7/w7_3_1.jpg',
          category: 'Historia',
          categoryColor: '#DC2626',
          location: 'La Paz',
          rating: 4.7,
          badge: 'Historia',
          to: null
        },
        {
          id: 3,
          name: 'Lago Titicaca',
          description: 'El lago navegable más alto del mundo, cuna de civilizaciones andinas',
          image: 'https://www.civitatis.com/f/bolivia/la-paz/tour-2-dias-lago-titicaca-589x392.jpg',
          category: 'Naturaleza',
          categoryColor: '#16A34A',
          location: 'La Paz',
          rating: 4.8,
          badge: 'Naturaleza',
          to: null
        }
      ]
    }
  }
}
</script>

<style scoped>
/* Sección de Lugares Destacados */
.featured-section {
  padding: 5rem 0;
  background: var(--bg-primary);
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



.section-subtitle {
  font-family: var(--font-family-base);
  font-size: 1.125rem;
  font-weight: var(--font-weight-regular);
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 1rem 0 0 0;
  max-width: 800px;
}

/* Grid de Lugares */
.places-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  margin-bottom: 0;
}

@media (max-width: 991.98px) {
  .places-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

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

/* Responsive */
@media (max-width: 768px) {
  .featured-section {
    padding: 3rem 0;
  }
  
  .section-header {
    margin-bottom: 2rem;
  }
  
  .section-title {
    font-size: 2.25rem;
  }
  
  .section-subtitle {
    font-size: 1rem;
  }
  
  .places-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

@media (max-width: 480px) {
  .section-title {
    font-size: 1.875rem;
  }
  
  .section-subtitle {
    font-size: 0.95rem;
  }
}
</style>
