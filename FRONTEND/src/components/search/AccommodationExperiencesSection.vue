<template>
  <section class="accommodation-experiences">
    <div class="container">
      <!-- <div class="section-header">
        <h2 class="experience-title">Estos lugares para hospedarte en Bolivia ofrecen mucho más que solo un lugar para dormir</h2>
        <p class="experience-subtitle">¿Sueñas con escapar a una cabaña en los Andes o disfrutar de un retiro lujoso? Intenta planificar tu viaje alrededor de una de estas experiencias únicas de alojamiento.</p>
      </div> -->
      
      <!-- Estado de carga -->
      <div v-if="loading" class="loading-state">
        <div class="loading-spinner"></div>
        <p>Cargando experiencias de alojamiento...</p>
      </div>

      <!-- Estado de error -->
      <div v-else-if="error" class="error-state">
        <i class="fas fa-exclamation-triangle"></i>
        <p>{{ error }}</p>
        <button @click="loadAccommodations" class="retry-button">Reintentar</button>
      </div>

      <!-- Grid de experiencias -->
      <div v-else class="experiences-grid">
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
    </div>
  </section>
</template>

<script lang="ts">
import { ref, onMounted, watch } from 'vue'
import { fetchAccommodations } from '@/services/accommodations.service'
import type { AccommodationApi } from '@/types'
import AccommodationCard from '@/components/home/AccommodationCard.vue'

type AccommodationExperiencesProps = { currentTab: string; query: string }
type ResultsEmit = (event: 'results-count', count: number) => void

export default {
  name: 'AccommodationExperiencesSection',
  components: {
    AccommodationCard
  },
  props: {
    currentTab: { type: String, default: 'alojamiento' },
    query: { type: String, default: '' }
  },
  emits: ['results-count'],
  setup(props: Readonly<AccommodationExperiencesProps>, { emit }: { emit: ResultsEmit }) {
    const accommodationExperiences = ref<AccommodationApi[]>([])
    const allItems = ref<AccommodationApi[]>([])
    const loading = ref(true)
    const error = ref('')

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

    // Aplicar filtros por query/tab
    const applyFilters = () => {
      let items = [...allItems.value]
      const q = props.query?.trim().toLowerCase()
      if (q) {
        items = items.filter(i =>
          i.title?.toLowerCase().includes(q) ||
          i.description?.toLowerCase().includes(q) ||
          i.location?.toLowerCase().includes(q) ||
          i.badge?.toLowerCase().includes(q)
        )
      }
      // Si más adelante hay otras pestañas, aquí puedes filtrar por tipo
      accommodationExperiences.value = items
      emit('results-count', items.length)
    }

    // Cargar acomodaciones de la API
    const loadAccommodations = async () => {
      try {
        loading.value = true
        error.value = ''
        const accommodations = await fetchAccommodations()
        allItems.value = accommodations
        applyFilters()
      } catch (err) {
        console.error('Error loading accommodations:', err)
        error.value = 'No se pudieron cargar las experiencias de alojamiento'
      } finally {
        loading.value = false
      }
    }

    // Efectos
    onMounted(() => {
      loadAccommodations()
    })

    watch(() => [props.query, props.currentTab], () => {
      applyFilters()
    })

    return {
      accommodationExperiences,
      loading,
      error,
      getMainImage,
      formatPrice,
      loadAccommodations
    }
  }
}
</script>

<style scoped>
/* Experiences section - Minimalista (alineado con WhereToGoPage.vue) */
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
}

.experience-subtitle {
  color: var(--text-secondary);
  font-size: 1.125rem;
  font-family: var(--font-family-base);
  margin-bottom: 0;
  line-height: 1.6;
}

.experiences-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.experience-card {
  background: transparent;
  border: none;
  border-radius: 0;
  overflow: visible;
  transition: all 0.3s ease;
  box-shadow: none;
  cursor: pointer;
}

.experience-card:hover {
  transform: translateY(-2px);
}

.experience-image {
  position: relative;
  height: 260px;
  overflow: hidden;
  border-radius: 24px;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
  border-radius: 24px;
}

.experience-card:hover .card-image {
  transform: scale(1.05);
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
}

.experience-content {
  padding: 0.75rem 0 0;
}

.experience-card-title {
  font-size: 1.5rem;
  font-weight: 400;
  color: var(--text-primary);
  margin: 0.75rem 0 0.25rem;
  font-family: var(--font-family-base);
  letter-spacing: -0.02em;
  line-height: 1.25;
}

.experience-description {
  font-size: 0.875rem;
  color: var(--text-muted);
  line-height: 1.4;
  margin: 0 0 0.75rem 0;
  font-family: var(--font-family-base);
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
  margin-bottom: 1.25rem;
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

.experience-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--primary-blue);
  text-decoration: none;
  font-weight: var(--font-weight-medium);
  font-size: 0.95rem;
  transition: all 0.2s ease;
  font-family: var(--font-family-base);
}

.experience-link:hover {
  color: var(--primary-blue-dark);
  transform: translateX(5px);
}

/* Estados de carga y error */
.loading-state {
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

.loading-state p {
  color: var(--text-secondary);
  font-size: 1rem;
  margin: 0;
}

.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.error-state i {
  font-size: 3rem;
  color: var(--warning-orange);
  margin-bottom: 1rem;
}

.error-state p {
  color: var(--text-secondary);
  font-size: 1rem;
  margin-bottom: 1.5rem;
}

.retry-button {
  background: var(--primary-blue);
  color: var(--white);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.retry-button:hover {
  background: var(--primary-blue-dark);
  transform: translateY(-1px);
}
</style>
