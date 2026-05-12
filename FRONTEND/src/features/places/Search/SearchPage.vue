<template>
  <div class="search-page">
    <SearchHeroSection 
      :current-tab="currentTab"
      :total-results="totalResults"
      :places-results="placesResults"
      :search-query="searchQuery"
      :is-searching="isSearching"
      :search-suggestions="searchSuggestions"
      :show-suggestions="showSuggestions"
      :category-tabs="categoryTabs"
      @tab-changed="handleTabChange"
      @search-performed="handleSearch"
      @generate-suggestions="generateSuggestions"
      @select-suggestion="selectSuggestion"
    />
    
    <section v-if="currentTab === 'todos'" class="all-locations-section container py-4">
      <div v-if="allLoading" class="text-center py-5">
        <div class="loading-spinner mb-2"></div>
        <p>Cargando lugares...</p>
      </div>
      <div v-else-if="allError" class="text-center py-5">
        <p class="text-danger mb-3">{{ allError }}</p>
        <button class="btn btn-primary" @click="loadAll">Reintentar</button>
      </div>
      <div v-else>
        <InterestsSection
          :bare="true"
          :items="allCards"
          :mapItemToCard="mapGenericToInterestCard"
          display-mode="grid"
        />
      </div>
    </section>

    <section v-else-if="currentTab === 'alojamiento'" class="accommodations-section container py-4">
      <div v-if="accomLoading" class="text-center py-5">
        <div class="loading-spinner mb-2"></div>
        <p>Cargando alojamientos...</p>
      </div>
      <div v-else-if="accomError" class="text-center py-5">
        <p class="text-danger mb-3">{{ accomError }}</p>
        <button class="btn btn-primary" @click="loadAccommodations">Reintentar</button>
      </div>
      <div v-else>
        <InterestsSection
          :bare="true"
          :items="accomCards"
          :mapItemToCard="mapAccommodationToInterestCard"
          display-mode="grid"
        />
      </div>
    </section>

    <section v-else-if="currentTab === 'eventos'" class="events-section container py-4">
      <div v-if="eventsLoading" class="text-center py-5">
        <div class="loading-spinner mb-2"></div>
        <p>Cargando eventos...</p>
      </div>
      <div v-else-if="eventsError" class="text-center py-5">
        <p class="text-danger mb-3">{{ eventsError }}</p>
        <button class="btn btn-primary" @click="loadEvents">Reintentar</button>
      </div>
      <div v-else>
        <InterestsSection
          :bare="true"
          :items="eventsCards"
          :mapItemToCard="mapEventToInterestCard"
          display-mode="grid"
        />
      </div>
    </section>

    <section v-else-if="currentTab === 'gastronomia'" class="gastronomy-section container py-4">
      <div v-if="restLoading" class="text-center py-5">
        <div class="loading-spinner mb-2"></div>
        <p>Cargando restaurantes...</p>
      </div>
      <div v-else-if="restError" class="text-center py-5">
        <p class="text-danger mb-3">{{ restError }}</p>
        <button class="btn btn-primary" @click="loadRestaurants">Reintentar</button>
      </div>
      <div v-else>
        <InterestsSection
          :bare="true"
          :items="restaurantsCards"
          :mapItemToCard="mapRestaurantToInterestCard"
          display-mode="grid"
        />
      </div>
    </section>

    <section v-else-if="currentTab === 'historia'" class="museums-section container py-4">
      <div v-if="museumsLoading" class="text-center py-5">
        <div class="loading-spinner mb-2"></div>
        <p>Cargando museos...</p>
      </div>
      <div v-else-if="museumsError" class="text-center py-5">
        <p class="text-danger mb-3">{{ museumsError }}</p>
        <button class="btn btn-primary" @click="loadMuseums">Reintentar</button>
      </div>
      <div v-else>
        <InterestsSection
          :bare="true"
          :items="museumsCards"
          :mapItemToCard="mapMuseumToInterestCard"
          display-mode="grid"
        />
      </div>
    </section>

    <section v-else-if="currentTab === 'naturaleza'" class="nature-section container py-4">
      <div v-if="natureLoading" class="text-center py-5">
        <div class="loading-spinner mb-2"></div>
        <p>Cargando lugares de naturaleza...</p>
      </div>
      <div v-else-if="natureError" class="text-center py-5">
        <p class="text-danger mb-3">{{ natureError }}</p>
        <button class="btn btn-primary" @click="loadNature">Reintentar</button>
      </div>
      <div v-else>
        <InterestsSection
          :bare="true"
          :items="natureCards"
          :mapItemToCard="mapGenericToInterestCard"
          display-mode="grid"
        />
      </div>
    </section>

    <section v-else-if="currentTab === 'bienestar'" class="wellness-section container py-4">
      <div v-if="wellnessLoading" class="text-center py-5">
        <div class="loading-spinner mb-2"></div>
        <p>Cargando lugares de bienestar...</p>
      </div>
      <div v-else-if="wellnessError" class="text-center py-5">
        <p class="text-danger mb-3">{{ wellnessError }}</p>
        <button class="btn btn-primary" @click="loadWellness">Reintentar</button>
      </div>
      <div v-else>
        <InterestsSection
          :bare="true"
          :items="wellnessCards"
          :mapItemToCard="mapGenericToInterestCard"
          display-mode="grid"
        />
      </div>
    </section>

    <!-- Sección genérica para tabs dinámicos de categorías -->
    <section v-else-if="currentTab.startsWith('category-')" class="category-section container py-4">
      <div v-if="categoryTabsLoading[currentTab]" class="text-center py-5">
        <div class="loading-spinner mb-2"></div>
        <p>Cargando lugares...</p>
      </div>
      <div v-else-if="categoryTabsError[currentTab]" class="text-center py-5">
        <p class="text-danger mb-3">{{ categoryTabsError[currentTab] }}</p>
        <button class="btn btn-primary" @click="() => loadCategoryTabData(currentTab)">Reintentar</button>
      </div>
      <div v-else>
        <InterestsSection
          :bare="true"
          :items="categoryTabsCards[currentTab] || []"
          :mapItemToCard="mapGenericToInterestCard"
          display-mode="grid"
        />
      </div>
    </section>

    <ScrollToTop />
  </div>
</template>

<script>

import { ref, onMounted, watch, getCurrentInstance, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import SearchHeroSection from '@/components/search/SearchHeroSection.vue'
import AccommodationExperiencesSection from '@/components/search/AccommodationExperiencesSection.vue'
import GastronomyList from '@/components/gastronomy/GastronomyList.vue'
import ScrollToTop from '@/components/ScrollToTop.vue'
import { eventsService } from '@/services/eventsService'
import { gastronomyService } from '@/services/gastronomyService'
import InterestsSection from '@/components/home/InterestsSection.vue'
import apiClient from '@/services/api'
import API_ENDPOINTS from '@/services/endpoints'
import { getAllLocations } from '@/services/locations.service'
import { categoriesService } from '@/services/categories.service'

export default {
  name: 'SearchPage',
  components: {
    SearchHeroSection,
    AccommodationExperiencesSection,
    GastronomyList,
    ScrollToTop,
    InterestsSection
  },
  setup() {

    const instance = getCurrentInstance()
    const $buildImg = instance?.appContext.config.globalProperties.$buildImg
    const route = useRoute()
    const router = useRouter()
    const currentTab = ref(route.query.tab || 'todos')
    const totalResults = ref(0)
    const placesResults = ref(0)
    const searchQuery = ref(route.query.q || '')
    const searchTimeout = ref(null)
    const isSearching = ref(false)
    const searchSuggestions = ref([])
    const showSuggestions = ref(false)

    // Cache de todas las locaciones para el buscador pro
    const allLocationsCache = ref([])
    const cacheLoaded = ref(false)

    // Estado para todos (locations)
    const allLoading = ref(false)
    const allError = ref('')
    const allCards = ref([])

    // Estado para alojamientos (locations by type accommodation)
    const accomLoading = ref(false)
    const accomError = ref('')
    const accomCards = ref([])

    // Estado para eventos
    const eventsLoading = ref(false)
    const eventsError = ref('')
    const eventsCards = ref([])

    // Estado para gastronomía (locations by type restaurant)
    const restLoading = ref(false)
    const restError = ref('')
    const restaurantsCards = ref([])
    // Estado para museos (locations by type museum)
    const museumsLoading = ref(false)
    const museumsError = ref('')
    const museumsCards = ref([])
    
    // Estado para naturaleza (locations by type attraction/nature)
    const natureLoading = ref(false)
    const natureError = ref('')
    const natureCards = ref([])
    
    // Estado para bienestar (locations by type spa/wellness)
    const wellnessLoading = ref(false)
    const wellnessError = ref('')
    const wellnessCards = ref([])
    
    // Tabs dinámicos desde categorías
    const categoryTabs = ref([])
    const loadingCategoryTabs = ref(false)
    
    // Estado unificado para tabs de categorías dinámicas
    const categoryTabsCards = ref({})
    const categoryTabsLoading = ref({})
    const categoryTabsError = ref({})
    // Función para obtener el tipo de location desde el nombre de categoría
    const getLocationTypeFromCategory = (categoryName) => {
      if (!categoryName) return null
      
      const name = categoryName.toLowerCase().trim()
      console.log('🔍 Mapeando categoría:', categoryName, '→ nombre normalizado:', name)
      
      // Mapeo flexible que busca palabras clave en el nombre
      if (name.includes('gastronomía') || name.includes('gastronomia') || name.includes('restaurante')) {
        console.log('✅ Mapeado a: restaurant')
        return 'restaurant'
      }
      if (name.includes('evento') || name.includes('festividad') || name.includes('carnaval')) {
        console.log('✅ Mapeado a: event')
        return 'event'
      }
      if (name.includes('cultura') || name.includes('patrimonio') || name.includes('historia') || name.includes('museo')) {
        console.log('✅ Mapeado a: museum')
        return 'museum'
      }
      if (name.includes('naturaleza') || name.includes('aventura') || name.includes('religión') || name.includes('religion') || 
          name.includes('artesanía') || name.includes('artesania') || name.includes('nocturna') || name.includes('entretenimiento')) {
        console.log('✅ Mapeado a: attraction')
        return 'attraction'
      }
      
      // Si no coincide, retornar null para usar el endpoint de categorías
      console.log('⚠️ No se encontró mapeo, usando endpoint de categorías')
      return null
    }
    
    // Cargar categorías y crear tabs dinámicos
    const loadCategoryTabs = async () => {
      loadingCategoryTabs.value = true
      try {
        const response = await categoriesService.getAllCategories()
        const categories = response.success && Array.isArray(response.data) ? response.data : []
        
        // Mapear categorías a tabs (excluyendo las que ya están hardcodeadas)
        const excludedNames = ['Todos', 'Hospedaje', 'Alojamiento'] // Nombres a excluir
        const mappedTabs = categories
          .filter(cat => !excludedNames.some(excluded => 
            cat.name.toLowerCase().includes(excluded.toLowerCase())
          ))
          .map(cat => {
            // Generar ID único para el tab basado en el slug de la categoría
            const slug = categoriesService.getCategorySlug(cat.name)
            // Obtener el tipo de location correspondiente usando la función flexible
            const locationType = getLocationTypeFromCategory(cat.name)
            return {
              id: `category-${slug}`,
              label: cat.name,
              icon: categoriesService.getCategoryIcon(cat.name),
              categoryId: cat.id,
              locationType: locationType // Tipo de location para filtrar
            }
          })
        
        categoryTabs.value = mappedTabs
        
        // Inicializar estados para cada tab de categoría
        mappedTabs.forEach(tab => {
          categoryTabsCards.value[tab.id] = []
          categoryTabsLoading.value[tab.id] = false
          categoryTabsError.value[tab.id] = ''
        })
      } catch (error) {
        console.error('Error cargando categorías para tabs:', error)
        categoryTabs.value = []
      } finally {
        loadingCategoryTabs.value = false
      }
    }
    
    // Cargar locations por categoría (filtrando por tipo de location)
    const loadCategoryTabData = async (tabId) => {
      const tab = categoryTabs.value.find(t => t.id === tabId)
      if (!tab) return
      
      categoryTabsLoading.value[tabId] = true
      categoryTabsError.value[tabId] = ''
      
      try {
        const searchParams = {}
        if (searchQuery.value && searchQuery.value.trim()) {
          searchParams.q = searchQuery.value.trim()
        }
        
        // Si la categoría tiene un tipo de location mapeado, usar BY_TYPE
        // Si no, usar el endpoint de categorías
        let res
        if (tab.locationType) {
          // Filtrar por tipo de location (como antes: gastronomía → restaurant, eventos → event, etc.)
          res = await apiClient.get(API_ENDPOINTS.LOCATIONS.BY_TYPE(tab.locationType), searchParams)
        } else {
          // Fallback: usar endpoint de categorías
          res = await apiClient.get(API_ENDPOINTS.CATEGORIES.LOCATIONS(tab.categoryId), searchParams)
        }
        
        const items = res && res.success === true && Array.isArray(res.data) ? res.data : []
        
        const mapped = items.filter(Boolean).map((loc) => {
          const mainImage = Array.isArray(loc.images) ? (loc.images.find(img => img.is_main) || loc.images[0]) : null
          const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
          const isEvent = loc.type === 'event'
          const dateText = isEvent && loc.start_date ? new Date(loc.start_date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : ''
          
          let to
          if (loc && loc.id) {
            if (loc.type === 'event') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'restaurant') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'accommodation') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'city') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'museum') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'attraction') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'place') to = { name: 'GastronomyDetail', params: { id: loc.id } }
          }
          
          return {
            id: loc.id,
            title: loc.name,
            type: loc.type,
            date: dateText,
            location: locationText,
            image: $buildImg ? $buildImg((mainImage && mainImage.image_url) || '/images/default-event.jpg') : ((mainImage && mainImage.image_url) || '/images/default-event.jpg'),
            description: loc.description || '',
            to
          }
        }).filter(card => card && card.title)
        
        categoryTabsCards.value[tabId] = mapped
        totalResults.value = mapped.length
        placesResults.value = mapped.length
      } catch (err) {
        console.error(`Error cargando categoría ${tab.label}:`, err)
        categoryTabsError.value[tabId] = `No se pudieron cargar los lugares de ${tab.label}`
        categoryTabsCards.value[tabId] = []
        totalResults.value = 0
        placesResults.value = 0
      } finally {
        categoryTabsLoading.value[tabId] = false
      }
    }

    const loadAccommodations = async () => {
      try {
        accomLoading.value = true
        accomError.value = ''
        
        // Construir parámetros de búsqueda
        const searchParams = {}
        if (searchQuery.value && searchQuery.value.trim()) {
          searchParams.q = searchQuery.value.trim()
        }
        
        const res = await apiClient.get(API_ENDPOINTS.LOCATIONS.BY_TYPE('accommodation'), searchParams)
        const items = res && res.success === true && Array.isArray(res.data) ? res.data : []
        const mapped = items.filter(Boolean).map((loc) => {
          const mainImage = Array.isArray(loc.images) ? (loc.images.find(img => img.is_main) || loc.images[0]) : null
          const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
          return {
            id: loc.id,
            title: loc.name,
            type: loc.type,
            date: '',
            location: locationText,
            image: $buildImg ? $buildImg((mainImage && mainImage.image_url) || '/images/default-event.jpg') : ((mainImage && mainImage.image_url) || '/images/default-event.jpg'),
            // image: (mainImage && mainImage.image_url) || '/images/default-event.jpg',
            description: loc.description || '',
            to: { name: 'GastronomyDetail', params: { id: loc.id } }
          }
        }).filter(card => card && card.title)
        accomCards.value = mapped
        totalResults.value = mapped.length
        placesResults.value = mapped.length
      } catch (err) {
        console.error('Error cargando alojamientos:', err)
        accomError.value = 'No se pudieron cargar los alojamientos'
        accomCards.value = []
        totalResults.value = 0
        placesResults.value = 0
      } finally {
        accomLoading.value = false
      }
    }


    const handleTabChange = (tab) => {
      // Actualizar estado y URL (agregar entrada al historial)
      currentTab.value = tab
      router.push({ query: { ...route.query, tab } })
      
      // Ejecutar búsqueda en el nuevo tab
      if (searchQuery.value.trim()) {
        performSearch()
      } else {
        loadCurrentTabData()
      }
    }

    // Función de búsqueda con debounce
    const handleSearch = (query) => {
      console.log('SearchPage handleSearch called with:', query)
      searchQuery.value = query
      showSuggestions.value = false
      
      // Limpiar timeout anterior
      if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
      }
      
      // Debounce: esperar 500ms antes de ejecutar la búsqueda
      searchTimeout.value = setTimeout(() => {
        console.log('Executing search after debounce for:', query)
        performSearch()
      }, 500)
    }

    // Función principal de búsqueda
    const ensureAllLocationsLoaded = async () => {
      if (!cacheLoaded.value) {
        try {
          console.log('🔄 Cargando todas las locaciones para buscador pro...')
          const resp = await getAllLocations()
          allLocationsCache.value = Array.isArray(resp?.data) ? resp.data : []
          cacheLoaded.value = true
          console.log('✅ Locaciones cargadas en cache:', allLocationsCache.value.length)
        } catch (e) {
          console.error('❌ Error cargando locaciones (cache):', e)
          allLocationsCache.value = []
          cacheLoaded.value = false
        }
      }
    }

    const matchesQuery = (text, query) => {
      if (!text || !query) return false
      return String(text).toLowerCase().includes(String(query).toLowerCase())
    }

    const filterLocationsByQueryLocal = (locations, query) => {
      if (!query) return locations
      return (locations || []).filter((loc) => {
        return (
          matchesQuery(loc.name, query) ||
          matchesQuery(loc.description, query) ||
          matchesQuery(loc.address, query) ||
          matchesQuery(loc?.department?.name, query) ||
          Array.isArray(loc.subcategories) && loc.subcategories.some(sc => matchesQuery(sc.name, query))
        )
      })
    }

    const performSearch = async () => {
      console.log('performSearch called with query:', searchQuery.value, 'tab:', currentTab.value)
      
      if (!searchQuery.value.trim()) {
        // Si no hay query, cargar todos los elementos
        console.log('No search query, loading current tab data')
        loadCurrentTabData()
        return
      }

      isSearching.value = true
      console.log('Starting search for:', searchQuery.value)
      
      try {
        // Buscador pro: usar todas las locaciones del servicio y filtrar localmente
        await ensureAllLocationsLoaded()
        const all = allLocationsCache.value
        const filtered = filterLocationsByQueryLocal(all, searchQuery.value)

        // Mapear por pestaña
        const mapGeneric = (loc) => {
          const mainImage = Array.isArray(loc.images) ? (loc.images.find(img => img.is_main) || loc.images[0]) : null
          const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
          return {
            id: loc.id,
            title: loc.name,
            type: loc.type,
            date: '',
            location: locationText,
            image: $buildImg ? $buildImg((mainImage && mainImage.image_url) || '/images/default-event.jpg') : ((mainImage && mainImage.image_url) || '/images/default-event.jpg'),
            description: loc.description || '',
            to: { name: 'GastronomyDetail', params: { id: loc.id } }
          }
        }

        const byType = (type) => filtered.filter(loc => String(loc.type) === type)

        if (currentTab.value === 'todos') {
          const mapped = filtered.map(mapGeneric)
          allCards.value = mapped
          totalResults.value = mapped.length
          placesResults.value = mapped.length
        } else if (currentTab.value === 'alojamiento') {
          const list = byType('accommodation').map(mapGeneric)
          accomCards.value = list
          totalResults.value = list.length
          placesResults.value = list.length
        } else if (currentTab.value === 'eventos') {
          const list = byType('event').map((loc) => {
            const card = mapGeneric(loc)
            card.date = loc.start_date ? new Date(loc.start_date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : ''
            return card
          })
          eventsCards.value = list
          totalResults.value = list.length
          placesResults.value = list.length
        } else if (currentTab.value === 'gastronomia') {
          const list = byType('restaurant').map(mapGeneric)
          restaurantsCards.value = list
          totalResults.value = list.length
          placesResults.value = list.length
        } else if (currentTab.value === 'historia') {
          const list = byType('museum').map(mapGeneric)
          museumsCards.value = list
          totalResults.value = list.length
          placesResults.value = list.length
        } else if (currentTab.value === 'naturaleza') {
          const list = byType('attraction').map(mapGeneric)
          natureCards.value = list
          totalResults.value = list.length
          placesResults.value = list.length
        } else if (currentTab.value === 'bienestar') {
          const wellnessTypes = ['spa', 'wellness', 'hotel', 'resort']
          const list = filtered.filter(loc => wellnessTypes.some(t => matchesQuery(loc.name || '', t) || matchesQuery(loc.description || '', t)))
            .map(mapGeneric)
          wellnessCards.value = list
          totalResults.value = list.length
          placesResults.value = list.length
        } else if (currentTab.value.startsWith('category-')) {
          // Tab dinámico de categoría - filtrar por tipo de location
          const tab = categoryTabs.value.find(t => t.id === currentTab.value)
          if (tab && tab.locationType) {
            // Filtrar por tipo de location (como antes)
            const list = byType(tab.locationType).map(mapGeneric)
            categoryTabsCards.value[currentTab.value] = list
            totalResults.value = list.length
            placesResults.value = list.length
          } else if (tab) {
            // Fallback: filtrar por categoría usando subcategorías
            const categoryLocations = filtered.filter(loc => 
              Array.isArray(loc.subcategories) && 
              loc.subcategories.some(sub => sub.category_id === tab.categoryId)
            )
            const list = categoryLocations.map(mapGeneric)
            categoryTabsCards.value[currentTab.value] = list
            totalResults.value = list.length
            placesResults.value = list.length
          }
        }
        
        // Actualizar URL después de la búsqueda exitosa
        router.push({ 
          query: { 
            ...route.query, 
            q: searchQuery.value,
            tab: currentTab.value 
          } 
        })
        
        console.log('Search completed successfully')
      } catch (error) {
        console.error('Search error:', error)
      } finally {
        isSearching.value = false
      }
    }

    // Cargar datos del tab actual sin filtro de búsqueda
    const loadCurrentTabData = async () => {
      const tempQuery = searchQuery.value
      searchQuery.value = ''
      
      try {
        if (currentTab.value === 'todos') {
          await loadAll()
        } else if (currentTab.value === 'alojamiento') {
          await loadAccommodations()
        } else if (currentTab.value.startsWith('category-')) {
          // Tab dinámico de categoría
          await loadCategoryTabData(currentTab.value)
        } else {
          // Mantener compatibilidad con tabs antiguos (eventos, gastronomia, etc.)
          if (currentTab.value === 'eventos') {
            await loadEvents()
          } else if (currentTab.value === 'gastronomia') {
            await loadRestaurants()
          } else if (currentTab.value === 'historia') {
            await loadMuseums()
          } else if (currentTab.value === 'naturaleza') {
            await loadNature()
          } else if (currentTab.value === 'bienestar') {
            await loadWellness()
          }
        }
      } finally {
        searchQuery.value = tempQuery
      }
    }

    // Generar sugerencias de búsqueda
    const generateSuggestions = async (query) => {
      if (query.length < 2) {
        searchSuggestions.value = []
        showSuggestions.value = false
        return
      }

      try {
        // Buscador pro: generar sugerencias desde todas las locaciones
        await ensureAllLocationsLoaded()
        const all = allLocationsCache.value
        const filtered = filterLocationsByQueryLocal(all, query)
        const names = Array.from(new Set(filtered.map(l => l.name).filter(Boolean)))
        searchSuggestions.value = names.slice(0, 5)
        showSuggestions.value = searchSuggestions.value.length > 0
      } catch (error) {
        console.error('Error getting search suggestions:', error)
        // Sugerencias estáticas como fallback
        const staticSuggestions = [
          'Salar de Uyuni',
          'Tiwanaku',
          'Lago Titicaca',
          'Sucre',
          'La Paz'
        ].filter(item => 
          item.toLowerCase().includes(query.toLowerCase())
        )
        searchSuggestions.value = staticSuggestions.slice(0, 5)
        showSuggestions.value = staticSuggestions.length > 0
      }
    }

    // Manejar selección de sugerencia
    const selectSuggestion = (suggestion) => {
      searchQuery.value = suggestion
      showSuggestions.value = false
      handleSearch(suggestion)
    }

    const updateCounts = (n) => {
      // Para alojamientos
      if (currentTab.value === 'alojamiento') {
        placesResults.value = n
        totalResults.value = n
      }
      // Para eventos
      else if (currentTab.value === 'eventos') {
        placesResults.value = n
        totalResults.value = n
      }
      // Para gastronomía
      else if (currentTab.value === 'gastronomia') {
        placesResults.value = n
        totalResults.value = n
      }
    }

    const loadAll = async () => {
      try {
        allLoading.value = true
        allError.value = ''
        
        // Construir parámetros de búsqueda
        const searchParams = {}
        if (searchQuery.value && searchQuery.value.trim()) {
          searchParams.q = searchQuery.value.trim()
        }
        
        console.log('loadAll called with params:', searchParams)
        const res = await apiClient.get(API_ENDPOINTS.LOCATIONS.BASE, searchParams)
        console.log('loadAll API response:', res)
        const items = res && res.success === true && Array.isArray(res.data) ? res.data : []
        // Mapear a un formato compatible con InterestCard
        const mapped = items.filter(Boolean).map((loc) => {
          const mainImage = Array.isArray(loc.images) ? (loc.images.find(img => img.is_main) || loc.images[0]) : null
          const isEvent = loc.type === 'event'
          const dateText = isEvent && loc.start_date ? new Date(loc.start_date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : ''
          const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
          let to
          if (loc && loc.id) {
            if (loc.type === 'event') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'restaurant') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'accommodation') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'city') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'museum') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'attraction') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'place') to = { name: 'GastronomyDetail', params: { id: loc.id } }
          }
          return {
            id: loc.id,
            title: loc.name,
            type: loc.type,
            date: dateText,
            location: locationText,
            image: $buildImg ? $buildImg((mainImage && mainImage.image_url) || '/images/default-event.jpg') : ((mainImage && mainImage.image_url) || '/images/default-event.jpg'),
            // image: (mainImage && mainImage.image_url) || '/images/default-event.jpg',
            description: loc.description || '',
            to
          }
        }).filter(card => card && card.title)
        allCards.value = mapped
        totalResults.value = mapped.length
        placesResults.value = mapped.length
      } catch (err) {
        console.error('Error cargando locations:', err)
        allError.value = 'No se pudieron cargar los lugares'
        allCards.value = []
        totalResults.value = 0
        placesResults.value = 0
      } finally {
        allLoading.value = false
      }
    }

    // const loadEvents = async () => {
    //   try {
    //     eventsLoading.value = true
    //     eventsError.value = ''
        
    //     // Construir parámetros de búsqueda
    //     const searchParams = {}
    //     if (searchQuery.value && searchQuery.value.trim()) {
    //       searchParams.q = searchQuery.value.trim()
    //     }
        
    //     const { data, success } = await eventsService.getAllLocationEvents(searchParams)
    //     const items = success ? data : []
    //     // Mapear a EventCardData desde locations events
    //     const mapped = items.map(ev => eventsService.transformLocationToEventCard(ev))
    //     eventsCards.value = mapped
    //     totalResults.value = mapped.length
    //     placesResults.value = mapped.length
    //   } catch (err) {
    //     console.error('Error cargando eventos:', err)
    //     eventsError.value = 'No se pudieron cargar los eventos'
    //     eventsCards.value = []
    //     totalResults.value = 0
    //     placesResults.value = 0
    //   } finally {
    //     eventsLoading.value = false
    //   }
    // }
    const loadEvents = async () => {
  try {
    eventsLoading.value = true
    eventsError.value = ''
    
    // Construir parámetros de búsqueda
    const searchParams = {}
    if (searchQuery.value && searchQuery.value.trim()) {
      searchParams.q = searchQuery.value.trim()
    }
    
    const { data, success } = await eventsService.getAllLocationEvents(searchParams)
    const items = success ? data : []
    
    // ✅ Mapear y procesar imágenes
    const mapped = items.map(ev => {
      const card = eventsService.transformLocationToEventCard(ev)
      // ✅ Procesar la imagen con $buildImg
      card.image = $buildImg ? $buildImg(card.image || '/images/default-event.jpg') : (card.image || '/images/default-event.jpg')
      return card
    })
    
    eventsCards.value = mapped
    totalResults.value = mapped.length
    placesResults.value = mapped.length
  } catch (err) {
    console.error('Error cargando eventos:', err)
    eventsError.value = 'No se pudieron cargar los eventos'
    eventsCards.value = []
    totalResults.value = 0
    placesResults.value = 0
  } finally {
    eventsLoading.value = false
  }
}

    const loadRestaurants = async () => {
      try {
        restLoading.value = true
        restError.value = ''
        
        // Construir parámetros de búsqueda
        const searchParams = {}
        if (searchQuery.value && searchQuery.value.trim()) {
          searchParams.q = searchQuery.value.trim()
        }
        
        const res = await apiClient.get(API_ENDPOINTS.LOCATIONS.BY_TYPE('restaurant'), searchParams)
        const items = res && res.success === true && Array.isArray(res.data) ? res.data : []
        const mapped = items.filter(Boolean).map((loc) => {
          const mainImage = Array.isArray(loc.images) ? (loc.images.find(img => img.is_main) || loc.images[0]) : null
          const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
          return {
            id: loc.id,
            title: loc.name,
            type: loc.type,
            date: '',
            location: locationText,
            image: $buildImg ? $buildImg((mainImage && mainImage.image_url) || '/images/default-event.jpg') : ((mainImage && mainImage.image_url) || '/images/default-event.jpg'),
            // image: (mainImage && mainImage.image_url) || '/images/default-event.jpg',
            description: loc.description || '',
            to: { name: 'GastronomyDetail', params: { id: loc.id } }
          }
        }).filter(card => card && card.title)
        restaurantsCards.value = mapped
        totalResults.value = mapped.length
        placesResults.value = mapped.length
      } catch (err) {
        console.error('Error cargando restaurantes:', err)
        restError.value = 'No se pudieron cargar los restaurantes'
        restaurantsCards.value = []
        totalResults.value = 0
        placesResults.value = 0
      } finally {
        restLoading.value = false
      }
    }

    const loadMuseums = async () => {
      try {
        museumsLoading.value = true
        museumsError.value = ''
        
        // Construir parámetros de búsqueda
        const searchParams = {}
        if (searchQuery.value && searchQuery.value.trim()) {
          searchParams.q = searchQuery.value.trim()
        }
        
        const res = await apiClient.get(API_ENDPOINTS.LOCATIONS.BY_TYPE('museum'), searchParams)
        const items = res && res.success === true && Array.isArray(res.data) ? res.data : []
        const mapped = items.filter(Boolean).map((loc) => {
          const mainImage = Array.isArray(loc.images) ? (loc.images.find(img => img.is_main) || loc.images[0]) : null
          const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
          return {
            id: loc.id,
            title: loc.name,
            type: loc.type,
            date: '',
            location: locationText,
            image: $buildImg ? $buildImg((mainImage && mainImage.image_url) || '/images/default-event.jpg') : ((mainImage && mainImage.image_url) || '/images/default-event.jpg'),
            // image: (mainImage && mainImage.image_url) || '/images/default-event.jpg',
            description: loc.description || '',
            to: { name: 'GastronomyDetail', params: { id: loc.id } }
          }
        }).filter(card => card && card.title)
        museumsCards.value = mapped
        totalResults.value = mapped.length
        placesResults.value = mapped.length
      } catch (err) {
        console.error('Error cargando museos:', err)
        museumsError.value = 'No se pudieron cargar los museos'
        museumsCards.value = []
        totalResults.value = 0
        placesResults.value = 0
      } finally {
        museumsLoading.value = false
      }
    }

    const loadNature = async () => {
      try {
        natureLoading.value = true
        natureError.value = ''
        
        // Construir parámetros de búsqueda
        const searchParams = {}
        if (searchQuery.value && searchQuery.value.trim()) {
          searchParams.q = searchQuery.value.trim()
        }
        
        const res = await apiClient.get(API_ENDPOINTS.LOCATIONS.BY_TYPE('attraction'), searchParams)
        const items = res && res.success === true && Array.isArray(res.data) ? res.data : []
        const mapped = items.filter(Boolean).map((loc) => {
          const mainImage = Array.isArray(loc.images) ? (loc.images.find(img => img.is_main) || loc.images[0]) : null
          const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
          return {
            id: loc.id,
            title: loc.name,
            type: loc.type,
            date: '',
            location: locationText,
            // image: (mainImage && mainImage.image_url) || '/images/default-event.jpg',
            image: $buildImg ? $buildImg((mainImage && mainImage.image_url) || '/images/default-event.jpg') : ((mainImage && mainImage.image_url) || '/images/default-event.jpg'),
            description: loc.description || '',
            to: { name: 'GastronomyDetail', params: { id: loc.id } }
          }
        }).filter(card => card && card.title)
        natureCards.value = mapped
        totalResults.value = mapped.length
        placesResults.value = mapped.length
      } catch (err) {
        console.error('Error cargando naturaleza:', err)
        natureError.value = 'No se pudieron cargar los lugares de naturaleza'
        natureCards.value = []
        totalResults.value = 0
        placesResults.value = 0
      } finally {
        natureLoading.value = false
      }
    }

    const loadWellness = async () => {
      try {
        wellnessLoading.value = true
        wellnessError.value = ''
        
        // Construir parámetros de búsqueda
        const searchParams = {}
        if (searchQuery.value && searchQuery.value.trim()) {
          searchParams.q = searchQuery.value.trim()
        }
        
        // Por ahora usamos el endpoint de locations y filtramos por tipo spa/wellness
        const res = await apiClient.get(API_ENDPOINTS.LOCATIONS.BASE, searchParams)
        const items = res && res.success === true && Array.isArray(res.data) ? res.data : []
        
        // Filtrar por tipos relacionados con bienestar
        const wellnessTypes = ['spa', 'wellness', 'hotel', 'resort']
        const filteredItems = items.filter(loc => 
          wellnessTypes.some(type => 
            loc.name.toLowerCase().includes(type) || 
            loc.description.toLowerCase().includes(type) ||
            loc.badge?.toLowerCase().includes(type)
          )
        )
        
        const mapped = filteredItems.filter(Boolean).map((loc) => {
          const mainImage = Array.isArray(loc.images) ? (loc.images.find(img => img.is_main) || loc.images[0]) : null
          const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
          let to
          if (loc && loc.id) {
            if (loc.type === 'event') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'restaurant') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'accommodation') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'city') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'museum') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'attraction') to = { name: 'GastronomyDetail', params: { id: loc.id } }
            else if (loc.type === 'place') to = { name: 'GastronomyDetail', params: { id: loc.id } }
          }
          return {
            id: loc.id,
            title: loc.name,
            type: loc.type,
            date: '',
            location: locationText,
            image: (mainImage && mainImage.image_url) || '/images/default-event.jpg',
            description: loc.description || '',
            to
          }
        }).filter(card => card && card.title)
        wellnessCards.value = mapped
        totalResults.value = mapped.length
        placesResults.value = mapped.length
      } catch (err) {
        console.error('Error cargando bienestar:', err)
        wellnessError.value = 'No se pudieron cargar los lugares de bienestar'
        wellnessCards.value = []
        totalResults.value = 0
        placesResults.value = 0
      } finally {
        wellnessLoading.value = false
      }
    }

    const getTypeLabel = (type) => {
      const map = { event: 'Evento', restaurant: 'Restaurante', accommodation: 'Hotel', museum: 'Museo', attraction: 'Atracción' }
      return map[type] || 'Lugar'
    }

    // Mapeadores hacia InterestCard
    const mapGenericToInterestCard = (card) => ({
      title: card.title,
      subtitle: card.date,
      description: card.location || card.description,
      cover: card.image,
      badge: getTypeLabel(card.type),
      to: card.to
    })

    const mapAccommodationToInterestCard = (card) => ({
      title: card.title,
      subtitle: card.location,
      description: card.description,
      cover: card.image,
      badge: 'Hotel',
      to: card.to
    })

    const mapRestaurantToInterestCard = (card) => ({
      title: card.title,
      subtitle: card.location,
      description: card.description,
      cover: card.image,
      badge: 'Restaurante',
      to: card.to
    })

    const mapMuseumToInterestCard = (card) => ({
      title: card.title,
      subtitle: card.location,
      description: card.description,
      cover: card.image,
      badge: 'Museo',
      to: card.to
    })

    const mapEventToInterestCard = (card) => ({
      title: card.title,
      subtitle: card.date,
      description: card.location || card.description,
      cover: card.image,
      badge: 'Evento',
      to: card.to
    })

    onMounted(async () => {
      console.log('SearchPage mounted')
      
      // Primero cargar las categorías para crear los tabs dinámicos
      await loadCategoryTabs()
      
      // Sincronizar con query inicial
      const qtab = route.query.tab
      const qquery = route.query.q
      
      console.log('Initial route query:', { tab: qtab, q: qquery })
      
      if (qtab && typeof qtab === 'string') currentTab.value = qtab
      if (qquery && typeof qquery === 'string') searchQuery.value = qquery
      
      console.log('Initial state:', { currentTab: currentTab.value, searchQuery: searchQuery.value })
      
      // Cargar datos iniciales
      loadCurrentTabData()
    })

    // Responder a cambios del query (back/forward del navegador)
    watch(() => route.query.tab, (tab) => {
      if (typeof tab === 'string' && tab && tab !== currentTab.value) {
        currentTab.value = tab
        loadCurrentTabData()
      } else if (!tab) {
        currentTab.value = 'todos'
        loadCurrentTabData()
      }
    })

    // Responder a cambios en la query de búsqueda
    watch(() => route.query.q, (query) => {
      if (typeof query === 'string' && query !== searchQuery.value) {
        searchQuery.value = query
        if (query.trim()) {
          performSearch()
        } else {
          loadCurrentTabData()
        }
      }
    })

      return {
      currentTab,
      totalResults,
      placesResults,
      searchQuery,
      isSearching,
      searchSuggestions,
      showSuggestions,
      handleTabChange,
      handleSearch,
      performSearch,
      generateSuggestions,
      selectSuggestion,
      updateCounts,
      getTypeLabel,
      // todos
      allLoading,
      allError,
      allCards,
      loadAll,
      mapGenericToInterestCard,
      // alojamiento
      accomLoading,
      accomError,
      accomCards,
      loadAccommodations,
      mapAccommodationToInterestCard,
      // eventos
      eventsLoading,
      eventsError,
      eventsCards,
      loadEvents,
      // gastronomía
      restLoading,
      restError,
      restaurantsCards,
      loadRestaurants,
      mapRestaurantToInterestCard,
      // historia & cultura (museos)
      museumsLoading,
      museumsError,
      museumsCards,
      loadMuseums,
      mapMuseumToInterestCard,
      // naturaleza
      natureLoading,
      natureError,
      natureCards,
      loadNature,
      // bienestar
      wellnessLoading,
      wellnessError,
      wellnessCards,
      loadWellness,
      // eventos
      mapEventToInterestCard,
      // Tabs dinámicos de categorías
      categoryTabs,
      categoryTabsCards,
      categoryTabsLoading,
      categoryTabsError,
      loadCategoryTabData
    }
  }
}
</script>

<style scoped>
.card-with-badge { position: relative; }
.type-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 2;
  font-size: 0.7rem;
  padding: 0.35rem 0.65rem;
  border-radius: 999px;
  color: #fff;
  backdrop-filter: saturate(120%) blur(2px);
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.type-event { background: #f59e0b; }
.type-restaurant { background: #ef4444; }
.type-accommodation { background: #3b82f6; }
.type-attraction { background: #10b981; }
.type-museum { background: #6b7280; }
.type-city { background: #198754; }
.type-place { background: #0d6efd; }
.search-page {
  background: var(--bg-primary);
}
/* Make grid cards look professional when few results (centered, fixed card width) */
.search-page :deep(.interests-bare) .interests-grid-layout,
.search-page :deep(.interests-section) .interests-grid-layout {
  grid-template-columns: repeat(auto-fit, minmax(280px, 360px));
  justify-content: center;
}

.search-page :deep(.interests-bare) .interests-grid-layout > *,
.search-page :deep(.interests-section) .interests-grid-layout > * {
  max-width: 360px;
  width: 100%;
}

/* Optional: constrain overall width when single result */
.search-page .container { max-width: 1200px; }

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--border-light);
  border-top: 4px solid var(--primary-blue);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 