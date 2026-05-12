<template>
  <section class="featured-places-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">{{ titleText }}</h2>
        <p v-if="subtitleText" class="section-subtitle">{{ subtitleText }}</p>
      </div>
      
      <div v-if="loading" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Cargando lugares destacados...</p>
      </div>
      <div v-else-if="error" class="error-container">
        <p class="error-message">{{ error }}</p>
      </div>
      <div v-else class="places-grid">
        <div class="place-card" v-for="place in renderedPlaces" :key="place.id" @click="handlePlaceClick(place)">
          <div class="place-image">
            <img :src="place.image" :alt="place.name" loading="lazy">
          </div>
          <div class="place-content">
            <h3 class="place-name">{{ place.name }}</h3>
            <p class="place-description">{{ place.description }}</p>
            <div class="place-location">
              <i class="fas fa-map-marker-alt"></i>
              <span>{{ place.location }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'FeaturedPlacesSection',
  props: {
    categoryId: {
      type: Number,
      required: false,
      default: null
    },
    type: {
      type: String,
      required: false,
      default: ''
    },
    places: {
      type: Array,
      required: false,
      default: null
    },
    title: {
      type: String,
      required: false,
      default: 'Lugares Culturales Destacados'
    },
    subtitle: {
      type: String,
      required: false,
      default: 'Explora los sitios más emblemáticos de la cultura boliviana'
    }
  },
  data() {
    return {
      fallbackPlaces: [
        {
          id: 1,
          name: 'Tiwanaku',
          description: 'Centro ceremonial preincaico declarado Patrimonio de la Humanidad',
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/35.jpg',
          location: 'La Paz'
        },
        {
          id: 2,
          name: 'Ciudad de Potosí',
          description: 'Una de las festividades más importantes de América Latina',
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/33.png',
          location: 'Potosí'
        },
        {
          id: 3,
          name: 'Misiones Jesuíticas',
          description: 'Arquitectura colonial única en el oriente boliviano',
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/34.png',
          location: 'Santa Cruz'
        }
      ],
      loading: false,
      error: null,
      fetchedPlaces: []
    }
  },
  computed: {
    renderedPlaces() {
      // Prioridad: places prop > fetchedPlaces > fallbackPlaces
      if (Array.isArray(this.places) && this.places.length > 0) {
        return this.places
      }
      if (Array.isArray(this.fetchedPlaces) && this.fetchedPlaces.length > 0) {
        return this.fetchedPlaces
      }
      return this.fallbackPlaces
    },
    titleText() {
      return this.title
    },
    subtitleText() {
      return this.subtitle || null
    }
  },
  async mounted() {
    if (this.categoryId) {
      await this.loadPlacesByCategory()
    } else if (this.type) {
      await this.loadPlacesByType()
    }
  },
  watch: {
    categoryId: {
      immediate: false,
      async handler() {
        await this.loadPlacesByCategory()
      }
    },
    type: {
      immediate: false,
      async handler() {
        await this.loadPlacesByType()
      }
    }
  },
  methods: {
    async loadPlacesByCategory() {
      if (!this.categoryId) return
      try {
        this.loading = true
        this.error = null
        const { getLocationsByCategory } = await import('@/services/locations.service')
        const response = await getLocationsByCategory(this.categoryId)
        console.log('Category locations response:', response)
        if (response?.success && Array.isArray(response.data)) {
          this.fetchedPlaces = response.data.slice(0, 3).map(loc => this.mapLocationToCard(loc))
          console.log('Mapped places:', this.fetchedPlaces)
        } else {
          this.fetchedPlaces = []
        }
      } catch (e) {
        console.error('Error loading places by category:', e)
        this.error = 'No se pudieron cargar los lugares destacados por categoría'
        this.fetchedPlaces = []
      } finally {
        this.loading = false
      }
    },
    async loadPlacesByType() {
      if (!this.type || (this.places && this.places.length)) return
      try {
        this.loading = true
        this.error = null
        const { getLocationsByTypeWithReviews } = await import('@/services/locations.service')
        const response = await getLocationsByTypeWithReviews(this.type)
        if (response?.success && Array.isArray(response.data)) {
          this.fetchedPlaces = response.data.map(loc => this.mapLocationToCard(loc))
        } else {
          this.fetchedPlaces = []
        }
      } catch (e) {
        console.error('Error loading places by type:', e)
        this.error = 'No se pudieron cargar los lugares destacados por tipo'
        this.fetchedPlaces = []
      } finally {
        this.loading = false
      }
    },
    mapLocationToCard(location) {
      const images = Array.isArray(location.images) ? location.images : []
      const mainImage = images.find(img => img && (img.is_main || img.isMain)) || images[0] || null
      const imageUrl = (mainImage && (mainImage.image_url || mainImage.url)) || '/images/placeholder.jpg'
      return {
        id: Number(location.id),
        name: String(location.name || ''),
        description: String(location.description || ''),
        image: imageUrl,
        location: location.department?.name || 'Bolivia',
        type: String(location.type || '')
      }
    },
    handlePlaceClick(place) {
      if (!place || !place.id) return
      
      const routeMap = {
        'attraction': 'AttractionDetail',
        'accommodation': 'AccommodationDetail', 
        'restaurant': 'GastronomyDetail',
        'museum': 'MuseumDetail',
        'city': 'CityDetail',
        'event': 'EventDetail'
      }
      
      const routeName = routeMap[place.type] || 'AttractionDetail'
      this.$router.push({ name: routeName, params: { id: place.id } })
    }
  }
}
</script>

<style scoped>
.featured-places-section { padding: 4rem 0; }

.section-header {
  text-align: center;
  margin-bottom: 3rem;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.section-title { 
  font-size: 2.5rem; 
  font-weight: 400; 
  color: var(--text-primary); 
  text-align: center; 
  margin-bottom: 3rem; 
  letter-spacing: -0.01em; 
}

.section-subtitle { font-size: 1.125rem; color: var(--text-secondary); line-height: 1.6; margin: 0; }

.places-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.place-card { background: transparent; border: none; border-radius: 0; overflow: visible; box-shadow: none; display: block; text-decoration: none; color: inherit; cursor: pointer; }

.place-image { height: 260px; position: relative; border-radius: 24px; overflow: hidden; background: var(--light-gray); transition: border-radius 300ms ease; }
.place-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transform: scale(1); transition: transform 400ms ease; will-change: transform; }
.place-card:hover .place-image img { transform: scale(1.05); }
.place-card:hover .place-image { border-radius: 24px 12px 24px 24px; }

.place-content { padding: 0.75rem 0 0; }

.place-name { margin: 0.75rem 0 0.25rem; font-size: 1.5rem; font-weight: 400; letter-spacing: -0.02em; line-height: 1.25; color: var(--text-primary); }

.place-description { color: var(--text-muted); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; max-height: calc(1.4em * 2); text-overflow: ellipsis; word-wrap: break-word; hyphens: auto; }

.place-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--primary-blue);
  font-size: 0.875rem;
  font-weight: 500;
}

.place-location i {
  font-size: 0.75rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .featured-places-section {
    padding: 3rem 0;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .section-description { font-size: 1rem; }
  
  .places-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

@media (max-width: 480px) {
  .section-title {
    font-size: 1.75rem;
  }
  
  .place-content { padding: 0.75rem 0 0; }
  
  .place-name { font-size: 1.25rem; }
}
</style>
