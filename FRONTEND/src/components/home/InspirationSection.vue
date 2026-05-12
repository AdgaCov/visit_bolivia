<template>
  <section class="inspiration-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">{{ computedTitle }}</h2>
        <p class="section-subtitle">{{ computedSubtitle }}</p>
      </div>
      <div class="inspiration-grid">
        <InspirationCard
          v-for="item in cards"
          :key="item.id"
          :image="item.image"
          :title="item.title"
          :text="item.text"
          :to="item.to"
          @click="onCardClick(item)"
        />
      </div>
    </div>
  </section>
</template>

<script>
import InspirationCard from '@/components/home/InspirationCard.vue'
import { getLocationsWithSubcategories } from '@/services/locations.service'

export default {
  name: 'InspirationSection',
  components: {
    InspirationCard
  },
  props: {
    start: { type: Number, default: 0 },
    limit: { type: Number, default: 8 },
    title: { type: String, default: '¿Buscas más inspiración?' },
    subtitle: { type: String, default: 'Bolivia es la fusión entre lo nórdico y lo báltico, entre Oriente y Occidente. Es un lugar donde se pueden escuchar los ecos del pasado y anticipar un futuro fascinante.' }
  },
  data() {
    return {
      loading: false,
      error: '',
      articles: []
    }
  },
  computed: {
    computedTitle() {
      return this.title
    },
    computedSubtitle() {
      return this.subtitle
    },
    cards() {
      const start = Math.max(0, Number(this.start) || 0)
      const end = start + (Number(this.limit) || 8)
      
      // ✅ Función para procesar la imagen
      const processImage = (url) => {
        if (!url) return '/images/placeholder.jpg'
        return this.$buildImg ? this.$buildImg(url) : url
      }

      return this.articles.slice(start, end).map(loc => {
        // ✅ Extraer la URL de la imagen
        let rawImageUrl = '/images/placeholder.jpg'
        if (Array.isArray(loc.images)) {
          const mainImage = loc.images.find(img => img.is_main)
          rawImageUrl = mainImage?.image_url || loc.images[0]?.image_url || '/images/placeholder.jpg'
        }
        
        return {
          id: Number(loc.id),
          title: String(loc.name || ''),
          text: String(loc.description || ''),
          image: processImage(rawImageUrl), // ✅ Procesar con $buildImg
          to: this.mapLocationToRoute(loc)
        }
      })
  }
    // cards() {
    //   const start = Math.max(0, Number(this.start) || 0)
    //   const end = start + (Number(this.limit) || 8)
    //   return this.articles.slice(start, end).map(loc => ({
    //     id: Number(loc.id),
    //     title: String(loc.name || ''),
    //     text: String(loc.description || ''),
    //     image: (Array.isArray(loc.images) && (loc.images.find?.(img => img.is_main)?.image_url || loc.images[0]?.image_url)) || '/images/placeholder.jpg',
    //     to: this.mapLocationToRoute(loc)
    //   }))
    // }
  },
  async mounted() {
    try {
      this.loading = true
      this.error = ''
      const resp = await getLocationsWithSubcategories()
      if (resp && resp.success && Array.isArray(resp.data)) {
        const sorted = [...resp.data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        this.articles = sorted
      } else {
        this.error = 'No se encontraron ubicaciones'
        this.articles = []
      }
    } catch (e) {
      console.error('Error cargando ubicaciones:', e)
      this.error = 'Error al cargar ubicaciones'
      this.articles = []
    } finally {
      this.loading = false
    }
  },
  methods: {
    mapLocationToRoute(loc) {
      const id = Number(loc.id)
      const type = String(loc.type || '')
      switch (type) {
        case 'museum':
          return { name: 'MuseumDetail', params: { id } }
        case 'city':
          return { name: 'CityDetail', params: { id } }
        case 'attraction':
        case 'place':
        default:
          return { name: 'AttractionDetail', params: { id } }
      }
    },
    slugify(text) {
      return String(text)
        .toLowerCase()
        .normalize('NFD')
        .replace(/\p{Diacritic}+/gu, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .slice(0, 60)
        .replace(/-+$/g, '')
    },
    onCardClick(item) {
      if (item && item.to) {
        this.$router.push(item.to)
      }
    }
  }
}
</script>

<style scoped>
/* Sección de Inspiración */
.inspiration-section { 
  padding: 5rem 0;
  background: var(--bg-secondary);
  position: relative;
}

.section-header {
  text-align: left;
  margin-bottom: 4rem;
}

.section-title {
  font-family: var(--font-family-base);
  font-size: 3rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  letter-spacing: -0.02em;
  line-height: 1.2;
  position: relative;
}



.section-subtitle {
  font-family: var(--font-family-base);
  font-size: 1.25rem;
  font-weight: var(--font-weight-regular);
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
  max-width: 1000px;
}

.inspiration-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
  gap: 2rem;
  margin-top: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
  .inspiration-section {
    padding: 3rem 0;
  }
  
  .section-header {
    margin-bottom: 2.5rem;
    text-align: left;
  }
  
  .section-title {
    font-size: 2.25rem;
    text-align: left;
  }
  
  .section-subtitle {
    font-size: 1.125rem;
    text-align: left;
  }
  
  .inspiration-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

@media (max-width: 480px) {
  .section-title {
    font-size: 1.875rem;
  }
  
  .section-subtitle {
    font-size: 1rem;
  }
}
</style>
