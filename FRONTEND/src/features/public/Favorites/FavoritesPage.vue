<template>
  <div class="favorites-page">
    <!-- Header Section -->
    <section class="favorites-header">
      <div class="container">
        <div class="header-content">
          <h1 class="page-title">
            <i class="fas fa-heart me-2"></i>
            Mis Favoritos
          </h1>
          <p class="page-subtitle">Guarda y organiza tus lugares, artículos y eventos favoritos</p>
        </div>
      </div>
    </section>

    <!-- Tabs Section -->
    <section class="favorites-tabs">
      <div class="container">
        <div class="tabs-wrapper">
          <!-- <button
            v-for="tab in tabs"
            :key="tab.id"
            class="tab-button"
            :class="{ active: activeTab === tab.id }"
            @click="activeTab = tab.id"
          >
            <i :class="tab.icon"></i>
            <span>{{ tab.label }}</span>
            <span v-if="getTabCount(tab.id) > 0" class="tab-count">{{ getTabCount(tab.id) }}</span>
          </button> -->
        </div>
      </div>
    </section>

    <!-- Mis Favoritos Section -->
    <section class="favorites-content">
      <div class="container">
        <!-- <div class="section-header">
          <h2 class="section-title">
            <i class="fas fa-heart me-2"></i>
            Mis Favoritos
          </h2>
          <p class="section-subtitle">Tus lugares, artículos y eventos guardados</p>
          <p v-if="usingTemporaryFavorites" class="temporary-hint">
            Estás usando favoritos temporales guardados en este dispositivo. Inicia sesión para sincronizarlos en todos tus dispositivos.
          </p>
        </div> -->

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Cargando tus favoritos...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h3>Error al cargar favoritos</h3>
          <p>{{ error }}</p>
          <button @click="loadUserFavorites" class="btn btn-primary">
            <i class="fas fa-redo me-2"></i>
            Reintentar
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="getActiveFavorites().length === 0" class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-heart"></i>
          </div>
          <h3>No tienes favoritos aún</h3>
          <p>Comienza a explorar y guarda tus lugares, artículos y eventos favoritos haciendo clic en el ícono de corazón.</p>
          <router-link to="/" class="btn btn-primary">
            <i class="fas fa-compass me-2"></i>
            Explorar Bolivia
          </router-link>
        </div>

        <!-- My Favorites Grid -->
        <div v-else class="favorites-grid">
          <div
            v-for="item in getActiveFavorites()"
            :key="item.id"
        class="favorite-card"
          >
        <button class="action-btn remove-favorite-btn" @click="removeFavorite(item)">
          <i class="fas fa-times"></i>
        </button>
        <InterestCard
          class="favorite-card-content"
          :title="item.title"
          :subtitle="item.location"
          :description="item.description"
          :badge="item.badge"
          :cover="item.image"
          :to="item.link"
        />
        <div class="card-details">
          <div class="card-meta">
            <span v-if="item.category" class="tag-chip">
              <i class="fas fa-tag"></i>
              {{ item.category }}
            </span>
            <!-- <span v-if="item.type" class="tag-chip">
              <i :class="getIconForType(item.type)"></i>
              {{ getTypeLabel(item.type) }}
            </span> -->
          </div>
          <router-link :to="item.link" class="btn btn-outline-primary w-100">
            Ver detalles
            <i class="fas fa-arrow-right ms-2"></i>
          </router-link>
        </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Más Favoritos Section -->
    <section class="popular-favorites-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">
            <i class="fas fa-fire me-2"></i>
            Los Favoritos
          </h2>
          <p class="section-subtitle">Los lugares y experiencias más populares entre nuestros visitantes</p>
        </div>

        <!-- Loading State -->
        <div v-if="loadingPopular" class="loading-state">
          <div class="spinner"></div>
          <p>Cargando favoritos populares...</p>
        </div>

        <!-- Popular Favorites Grid -->
        <div v-else class="favorites-grid">
          <div
            v-for="item in popularFavorites"
            :key="item.id"
            class="favorite-card popular-card"
          >
            <button class="action-btn add-favorite-btn" @click="addFavorite(item)">
              <i class="fas fa-heart"></i>
            </button>
            <div class="popular-badge">
              <i class="fas fa-fire"></i>
              {{ item.favoritesCount }} favoritos
            </div>
            <InterestCard
              class="favorite-card-content"
              :title="item.title"
              :subtitle="item.location"
              :description="item.description"
              :badge="item.badge"
              :cover="item.image"
              :to="item.link"
            />
            <div class="card-details">
              <!-- <div class="card-meta">
                <span v-if="item.category" class="tag-chip">
                  <i class="fas fa-tag"></i>
                  {{ item.category }}
                </span>
              </div> -->
              <router-link :to="item.link" class="btn btn-outline-primary w-100">
                Ver detalles
                <i class="fas fa-arrow-right ms-2"></i>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, computed, watch,getCurrentInstance } from 'vue'
import InterestCard from '@/components/home/InterestCard.vue'
import favoritesService, { type FavoriteCard, type FavoriteType } from '@/services/favorites.service'
import localFavoritesService from '@/services/localFavorites.service'
import { useAuthStore } from '@/store/auth'

const TYPE_ICONS = {
  all: 'fas fa-list',
  places: 'fas fa-map-marker-alt',
  articles: 'fas fa-newspaper',
  events: 'fas fa-calendar-alt',
  gastronomy: 'fas fa-utensils'
}

const TYPE_LABELS = {
  places: 'Lugar',
  articles: 'Artículo',
  events: 'Evento',
  gastronomy: 'Gastronomía'
}

type FavoritesBuckets = {
  all: FavoriteCard[]
  places: FavoriteCard[]
  articles: FavoriteCard[]
  events: FavoriteCard[]
  gastronomy: FavoriteCard[]
}

const createEmptyBuckets = (): FavoritesBuckets => ({
  all: [],
  places: [],
  articles: [],
  events: [],
  gastronomy: []
})

export default defineComponent({
  name: 'FavoritesPage',
  components: {
    InterestCard
  },
  setup() {
    const authStore = useAuthStore()
    const instance = getCurrentInstance()
    const $buildImg = instance?.appContext.config.globalProperties.$buildImg
    const activeTab = ref<'all' | FavoriteType>('all')
    const loading = ref(false)
    const loadingPopular = ref(false)
    const error = ref<string | null>(null)
    const usingTemporaryFavorites = ref(false)

    const tabs: Array<{ id: 'all' | FavoriteType; label: string; icon: string }> = [
      { id: 'all', label: 'Todos', icon: TYPE_ICONS.all },
      { id: 'places', label: 'Lugares', icon: TYPE_ICONS.places },
      { id: 'articles', label: 'Artículos', icon: TYPE_ICONS.articles },
      { id: 'events', label: 'Eventos', icon: TYPE_ICONS.events },
      { id: 'gastronomy', label: 'Gastronomía', icon: TYPE_ICONS.gastronomy }
    ]

    // Datos reactivos
    const favorites = ref<FavoritesBuckets>(createEmptyBuckets())

    const popularFavorites = ref<FavoriteCard[]>([])

    const isAuthenticated = computed(() => authStore.authenticated)

    const populateFavorites = (items: FavoriteCard[]) => {
      favorites.value.all = items
      favorites.value.places = items.filter(f => f.type === 'places')
      favorites.value.articles = items.filter(f => f.type === 'articles')
      favorites.value.events = items.filter(f => f.type === 'events')
      favorites.value.gastronomy = items.filter(f => f.type === 'gastronomy')
    }

    const loadLocalFavorites = () => {
      const tempFavorites = localFavoritesService.getAll()
      // Procesar imágenes de favoritos locales también
      const processedFavorites = tempFavorites.map(fav => {
        if (fav.image) {
          fav.image = $buildImg ? $buildImg(fav.image) : fav.image
        }
        return fav
      })
      populateFavorites(processedFavorites)
      usingTemporaryFavorites.value = true
      error.value = null
      loading.value = false
    }

    // Cargar favoritos del usuario
    type ServiceError = {
      response?: {
        status: number
        data?: { message?: string }
      }
      message?: string
    }

    const isServiceError = (error: unknown): error is ServiceError => {
      return typeof error === 'object' && error !== null
    }

    const loadUserFavorites = async () => {
      loading.value = true
      error.value = null
      try {
        const response = await favoritesService.getUserFavorites()
        if (response.success && response.data) {
          const transformedFavorites = response.data.flatMap(fav => {
            try {
            const card = favoritesService.transformFavoriteToCard(fav)
            // Procesar imagen con $buildImg si está disponible
            if (card.image) {
              card.image = $buildImg ? $buildImg(card.image) : card.image
            }
            return [card]
            } catch (error) {
              console.warn('Favorito omitido por datos incompletos:', fav, error)
              return []
            }
          })
          populateFavorites(transformedFavorites)
          usingTemporaryFavorites.value = false
        }
      } catch (err: unknown) {
        console.error('Error loading user favorites:', err)
        
        if (isServiceError(err) && err.response) {
          const status = err.response.status
          
          if (status === 401) {
            loadLocalFavorites()
            return
          }
          
          if (status === 404) {
            error.value = 'La ruta de favoritos no está disponible. Por favor, verifica que el endpoint esté configurado en el servidor.'
          } else if (status === 403) {
            error.value = 'No tienes permisos para acceder a esta información.'
          } else {
            error.value = err.response.data?.message || `Error al cargar tus favoritos (${status})`
          }
        } else if (isServiceError(err) && err.message) {
          error.value = err.message
        } else {
          error.value = 'Error al cargar tus favoritos. Por favor, intenta nuevamente.'
        }
      } finally {
        loading.value = false
      }
    }

    // Cargar favoritos populares
    const loadPopularFavorites = async () => {
      loadingPopular.value = true
      try {
        console.log('🔄 Cargando favoritos populares...')
        const response = await favoritesService.getAllFavorites()
        console.log('📦 Respuesta de favoritos populares:', response)
        
        if (response.success && response.data && Array.isArray(response.data)) {
          // Transformar y procesar imágenes con $buildImg
          const transformed = response.data.flatMap(fav => {
            try {
            const card = favoritesService.transformFavoriteToCard(fav)
            // Procesar imagen con $buildImg si está disponible
            if (card.image) {
              card.image = $buildImg ? $buildImg(card.image) : card.image
            }
            return [card]
            } catch (error) {
              console.warn('Favorito popular omitido por datos incompletos:', fav, error)
              return []
            }
          })
          console.log('🔄 Favoritos transformados:', transformed)
          
          // Ordenar por favorites_count descendente
          popularFavorites.value = transformed.sort((a, b) => 
            (b.favoritesCount || 0) - (a.favoritesCount || 0)
          )
          console.log('✅ Favoritos populares ordenados:', popularFavorites.value)
        } else {
          console.warn('⚠️ Respuesta de favoritos populares no tiene el formato esperado:', response)
          popularFavorites.value = []
        }
      } catch (err) {
        console.error('❌ Error loading popular favorites:', err)
        // No mostrar error al usuario para favoritos populares, solo dejar vacío
        popularFavorites.value = []
      } finally {
        loadingPopular.value = false
      }
    }

    const initializeFavorites = async () => {
      if (isAuthenticated.value) {
        await loadUserFavorites()
      } else {
        loadLocalFavorites()
      }
    }

    const getActiveFavorites = () => {
      if (activeTab.value === 'all') {
        return favorites.value.all
      }
      return favorites.value[activeTab.value] || []
    }

    const getTabCount = (tabId: 'all' | FavoriteType) => {
      if (tabId === 'all') {
        return favorites.value.all.length
      }
      return favorites.value[tabId]?.length || 0
    }

    const getIconForType = (type: FavoriteType | 'all'): string => TYPE_ICONS[type] || TYPE_ICONS.all
    const getTypeLabel = (type: FavoriteType | 'all'): string => TYPE_LABELS[type as FavoriteType] || 'Favorito'

    const removeFavorite = async (favorite: FavoriteCard) => {
      if (!favorite) return
      try {
        if (!isAuthenticated.value || favorite.isTemporary) {
          const updated = localFavoritesService.remove(favorite.id)
          populateFavorites(updated)
          usingTemporaryFavorites.value = true
          return
        }
        const locationId = favorite.locationId ?? favorite.id
        await favoritesService.removeFavorite(locationId)
        await loadUserFavorites()
      } catch (err) {
        console.error('Error removing favorite:', err)
      }
    }

    const addFavorite = async (favorite: FavoriteCard) => {
      if (!favorite) return
      try {
        if (!isAuthenticated.value) {
          const updated = localFavoritesService.add(favorite)
          populateFavorites(updated)
          usingTemporaryFavorites.value = true
          return
        }
        const locationId = favorite.locationId ?? favorite.id
        await favoritesService.addFavorite(locationId)
        await loadUserFavorites()
      } catch (err) {
        console.error('Error adding favorite:', err)
      } finally {
        await loadPopularFavorites()
      }
    }

    // Cargar datos al montar el componente
    onMounted(async () => {
      await initializeFavorites()
      await loadPopularFavorites()
    })

    watch(isAuthenticated, async () => {
      await initializeFavorites()
    })

    return {
      activeTab,
      tabs,
      favorites,
      popularFavorites,
      loading,
      loadingPopular,
      error,
      usingTemporaryFavorites,
      getActiveFavorites,
      getTabCount,
      getIconForType,
      getTypeLabel,
      removeFavorite,
      addFavorite,
      loadUserFavorites
    }
  }
})
</script>

<style scoped>
.favorites-page {
  background: var(--bg-primary, #f8f9fa);
  min-height: 100vh;
  padding-bottom: 4rem;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Header Section */
.favorites-header {
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  padding: 4rem 0 3rem;
  margin-bottom: 2rem;
  position: relative;
  border-bottom: 1px solid rgba(226, 232, 240, 0.5);
}

.header-content {
  text-align: center;
}

.page-title {
  font-size: 2.75rem;
  font-weight: 800;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.02em;
}

.page-title i {
  color: #ef4444;
  -webkit-text-fill-color: #ef4444;
  animation: heartbeat 2s ease-in-out infinite;
}

@keyframes heartbeat {
  0%, 100% {
    transform: scale(1);
  }
  10%, 30% {
    transform: scale(1.1);
  }
  20%, 40% {
    transform: scale(1);
  }
}

.page-subtitle {
  font-size: 1.125rem;
  color: #64748b;
  margin: 0;
  font-weight: 400;
  letter-spacing: 0.01em;
}

/* Tabs Section */
.favorites-tabs {
  background: white;
  border-bottom: 2px solid var(--border-light, #e5e7eb);
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tabs-wrapper {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
  padding: 0.5rem 0;
  scrollbar-width: thin;
}

.tabs-wrapper::-webkit-scrollbar {
  height: 4px;
}

.tabs-wrapper::-webkit-scrollbar-thumb {
  background: var(--border-color, #d1d5db);
  border-radius: 2px;
}

.tab-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  color: var(--text-secondary, #6b7280);
  font-weight: 500;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
  position: relative;
}

.tab-button:hover {
  color: var(--primary-blue, #1e40af);
  background: var(--light-gray, #f3f4f6);
}

.tab-button.active {
  color: var(--primary-blue, #1e40af);
  border-bottom-color: var(--primary-blue, #1e40af);
  background: var(--light-gray, #f3f4f6);
}

.tab-button i {
  font-size: 1rem;
}

.tab-count {
  background: var(--primary-blue, #1e40af);
  color: white;
  border-radius: 12px;
  padding: 0.15rem 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  min-width: 20px;
  text-align: center;
}

.tab-button.active .tab-count {
  background: var(--primary-dark, #1e3a8a);
}

/* Content Section */
.favorites-content {
  padding: 2rem 0;
}

.section-header {
  margin-bottom: 2rem;
  text-align: center;
}

.section-title {
  font-size: 2.25rem;
  font-weight: 800;
  background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  letter-spacing: -0.02em;
}

.section-title i {
  color: #ef4444;
  -webkit-text-fill-color: #ef4444;
  animation: flame 1.5s ease-in-out infinite;
}

.section-subtitle {
  font-size: 1.0625rem;
  color: #64748b;
  margin: 0;
  font-weight: 400;
  letter-spacing: 0.01em;
}

.temporary-hint {
  margin-top: 0.75rem;
  font-size: 0.9rem;
  color: var(--text-secondary, #6b7280);
  background: var(--light-gray, #f3f4f6);
  border-left: 3px solid var(--primary-blue, #1e40af);
  padding: 0.75rem 1rem;
  border-radius: 8px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 5rem 2rem;
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 24px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(226, 232, 240, 0.8);
}

.empty-icon {
  font-size: 5rem;
  color: #cbd5e1;
  margin-bottom: 2rem;
  opacity: 0.7;
  animation: heartbeat 2s ease-in-out infinite;
}

.empty-state h3 {
  font-size: 1.75rem;
  font-weight: 700;
  background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}

.empty-state p {
  color: #64748b;
  font-size: 1.0625rem;
  margin-bottom: 2.5rem;
  max-width: 520px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
}

/* Favorites Grid */
.favorites-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 2.5rem;
  padding: 1.5rem 0;
}

@media (min-width: 768px) {
  .favorites-grid {
  gap: 2rem;
  }
}

@media (min-width: 1024px) {
  .favorites-grid {
    gap: 2.5rem;
  }
}

.favorite-card {
  position: relative;
  background: transparent;
  border-radius: 24px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: visible;
}

.favorite-card:hover {
  transform: translateY(-8px);
}

.favorite-card:hover .favorite-card-content :deep(.interest-card) {
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
}

.favorite-card-content :deep(.interest-card) {
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.1);
  border-radius: 24px;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.favorite-card-content :deep(.interest-image) {
  height: 280px;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.favorite-card:hover .favorite-card-content :deep(.interest-image) {
  transform: scale(1.05);
}

.card-details {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 20px;
  padding: 1.5rem;
  margin-top: 1rem;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  border: 1px solid rgba(226, 232, 240, 0.8);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.favorite-card:hover .card-details {
  box-shadow: 0 12px 32px rgba(15, 23, 42, 0.15);
  transform: translateY(-2px);
}

.card-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.tag-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 50px;
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  color: var(--text-secondary, #475569);
  font-size: 0.875rem;
  font-weight: 600;
  border: 1px solid rgba(226, 232, 240, 0.8);
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tag-chip:hover {
  background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.tag-chip i {
  font-size: 0.875rem;
  color: var(--primary-blue, #2563eb);
}

.action-btn {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 10;
  font-size: 1rem;
}

.action-btn:hover {
  transform: scale(1.1) rotate(90deg);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
}

.action-btn:active {
  transform: scale(0.95);
}

.remove-favorite-btn {
  color: #64748b;
}

.remove-favorite-btn:hover {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 12px 28px rgba(239, 68, 68, 0.4);
}

.btn {
  display: inline-flex;
  align-items: center;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
}

.btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: white;
  font-weight: 600;
  letter-spacing: 0.025em;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  border: none;
  position: relative;
  overflow: hidden;
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: -1;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
}

.btn-primary:hover::before {
  left: 0;
}

.btn-outline-primary {
  background: transparent;
  color: var(--primary-blue, #2563eb);
  border: 2px solid var(--primary-blue, #2563eb);
  width: 100%;
  justify-content: center;
  font-weight: 600;
  letter-spacing: 0.025em;
  position: relative;
  overflow: hidden;
}

.btn-outline-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: -1;
}

.btn-outline-primary:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: white;
  border-color: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
}

.btn-outline-primary:hover::before {
  left: 0;
}

.btn-outline-primary i {
  transition: transform 0.3s ease;
}

.btn-outline-primary:hover i {
  transform: translateX(4px);
}

/* Popular Favorites Section */
.popular-favorites-section {
  padding: 4rem 0;
  background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
  margin-top: 3rem;
  position: relative;
}

.popular-favorites-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(226, 232, 240, 0.8), transparent);
}

.popular-card {
  position: relative;
}

.popular-card:hover {
  transform: translateY(-10px);
}

.add-favorite-btn {
  color: #2563eb;
}

.add-favorite-btn:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: white;
  box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
}

.popular-card .popular-badge {
  position: absolute;
  top: 1.5rem;
  left: 1.5rem;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  z-index: 10;
  box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  animation: pulse 2s ease-in-out infinite;
  letter-spacing: 0.025em;
}

.popular-card .popular-badge i {
  font-size: 0.875rem;
  animation: flame 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 12px 28px rgba(239, 68, 68, 0.5);
  }
}

@keyframes flame {
  0%, 100% {
    transform: scale(1) rotate(0deg);
  }
  25% {
    transform: scale(1.1) rotate(-5deg);
  }
  75% {
    transform: scale(1.1) rotate(5deg);
  }
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 5rem 2rem;
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 24px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(226, 232, 240, 0.8);
}

.spinner {
  width: 56px;
  height: 56px;
  border: 5px solid #f1f5f9;
  border-top-color: #2563eb;
  border-right-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 2rem;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  color: #64748b;
  font-size: 1.0625rem;
  margin: 0;
  font-weight: 500;
  letter-spacing: 0.01em;
}

/* Error State */
.error-state {
  text-align: center;
  padding: 5rem 2rem;
  background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
  border-radius: 24px;
  box-shadow: 0 8px 24px rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(254, 226, 226, 0.8);
}

.error-icon {
  font-size: 5rem;
  color: #ef4444;
  margin-bottom: 2rem;
  animation: shake 0.5s ease-in-out infinite;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

.error-state h3 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #991b1b;
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}

.error-state p {
  color: #7f1d1d;
  font-size: 1.0625rem;
  margin-bottom: 2.5rem;
  line-height: 1.6;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

/* Responsive */
@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }

  .page-subtitle {
    font-size: 1rem;
  }

  .favorites-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .card-details {
    padding: 1.25rem;
  }

  .action-btn {
    width: 40px;
    height: 40px;
    top: 1.25rem;
    right: 1.25rem;
  }

  .tab-button {
    padding: 0.6rem 1rem;
    font-size: 0.85rem;
  }

  .tab-button span:not(.tab-count) {
    display: none;
  }

  .tab-button i {
    font-size: 1.1rem;
  }
}

@media (max-width: 480px) {
  .favorites-header {
    padding: 2rem 0;
  }

  .page-title {
    font-size: 1.75rem;
    flex-direction: column;
    gap: 0.25rem;
  }

  .empty-state {
    padding: 3rem 1.5rem;
  }

  .empty-icon {
    font-size: 3rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .section-subtitle {
    font-size: 0.9rem;
  }

  .popular-favorites-section {
    padding: 2rem 0;
  }
}
</style>
