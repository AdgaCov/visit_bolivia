<template>
  <section v-if="hasArticles" class="location-articles-section my-4">
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Cargando artículos...</p>
    </div>
    
    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-triangle"></i>
      <p>{{ error }}</p>
      <button @click="fetchArticles" class="retry-btn">Reintentar</button>
    </div>
    
    <div v-else-if="articles.length > 0" class="articles-container">
      <h2 class="section-title mb-4">Artículos relacionados</h2>
      <div class="articles-list">
        <component
          v-for="article in articles"
          :key="article.id"
          :is="getComponentByTemplate(article.template_id)"
          :article="article"
        />
      </div>
    </div>
  </section>
</template>

<script lang="ts">
import { ref, onMounted, computed } from 'vue'
import apiClient from '@/services/api'
import API_ENDPOINTS from '@/services/endpoints'
import ArticleBasic from '@/components/cultura/ArticleBasic.vue'
import ArticleHistory from '@/components/cultura/ArticleHistory.vue'
import ArticleImageLeft from '@/components/cultura/ArticleImageLeft.vue'
import ArticleImageRight from '@/components/cultura/ArticleImageRight.vue'

export default {
  name: 'LocationArticlesSection',
  components: {
    ArticleBasic,
    ArticleHistory,
    ArticleImageLeft,
    ArticleImageRight
  },
  props: {
    locationId: {
      type: [String, Number],
      required: true
    }
  },
  setup(props: { locationId: string | number }) {
    const articles = ref<any[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)
    
    // Computed para determinar si hay artículos
    const hasArticles = computed(() => {
      return !loading.value && articles.value.length > 0
    })

    // Mapeo de template_id a componentes
    const getComponentByTemplate = (templateId: number): string => {
      const componentMap: Record<number, string> = {
        1: 'ArticleBasic',
        2: 'ArticleHistory', 
        3: 'ArticleImageLeft',
        4: 'ArticleImageRight'
      }
      return componentMap[templateId] || 'ArticleBasic'
    }

    // Función para cargar artículos de la ubicación
    const fetchArticles = async () => {
      try {
        loading.value = true
        error.value = null
        console.log('LocationArticlesSection: Fetching articles for location:', props.locationId)
        
        const response = await apiClient.get(API_ENDPOINTS.LOCATIONS.ARTICLES(props.locationId))
        console.log('LocationArticlesSection: API Response:', response)
        
        const articlesData = (response as any).data || []
        console.log('LocationArticlesSection: Articles data:', articlesData)
        
        articles.value = articlesData
      } catch (err) {
        console.error('LocationArticlesSection: Error fetching articles:', err)
        error.value = 'Error al cargar los artículos'
        articles.value = []
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      fetchArticles()
    })

    return {
      articles,
      loading,
      error,
      hasArticles,
      fetchArticles,
      getComponentByTemplate
    }
  }
}
</script>

<style scoped>
.location-articles-section {
  background: var(--bg-primary);
}

.section-title {
  font-family: var(--font-family-base);
  font-size: 2.5rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 2rem;
  letter-spacing: -0.02em;
  line-height: 1.2;
  text-align: center;
}

.articles-list {
  display: flex;
  flex-direction: column;
  gap: 0;
}

/* Estados de carga, error y vacío */
.loading-state,
.error-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
  color: var(--text-secondary);
  min-height: 200px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--border-light);
  border-top: 3px solid var(--primary-blue);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state i,
.empty-state i {
  font-size: 3rem;
  color: var(--text-muted);
  margin-bottom: 1rem;
}

.error-state i {
  color: var(--error-red);
}

.retry-btn {
  background: var(--primary-blue);
  color: var(--white);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.retry-btn:hover {
  background: var(--primary-blue-dark);
  transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
  .section-title {
    font-size: 2rem;
    margin-bottom: 1.5rem;
  }
  
  .loading-state,
  .error-state,
  .empty-state {
    padding: 2rem 1rem;
    min-height: 150px;
  }
  
  .spinner {
    width: 30px;
    height: 30px;
  }
  
  .error-state i,
  .empty-state i {
    font-size: 2rem;
  }
}
</style>
