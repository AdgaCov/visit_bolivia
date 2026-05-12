<template>
  <div class="planning-page">
    <!-- Loading state para artículos -->
    <div v-if="articlesLoading" class="text-center py-5">
      <div class="loading-spinner mb-2"></div>
      <p>Cargando contenido...</p>
    </div>
    
    <!-- Error state para artículos -->
    <div v-else-if="articlesError" class="text-center py-5">
      <p class="text-danger mb-3">{{ articlesError }}</p>
      <button class="btn btn-primary" @click="fetchPlanningArticles">Reintentar</button>
    </div>
    
    <!-- Contenido principal -->
    <div v-else>
      <PlanningHero :article="planningHeroArticle" />
    
    <!-- Sección: Lugares Destacados - Solo si hay artículo con template_id = 24 -->
    <FeaturedSection 
      v-if="whereToGoArticle"
      :title="whereToGoArticle.title || 'Más Lugares para Planificar'"
      :subtitle="whereToGoArticle.subtitle || 'Descubre otros destinos increíbles para incluir en tu itinerario de viaje por Bolivia'"
      :offset="4"
      :limit="4"
    />
    
    <!-- Sección: Introducción con título y subtítulo -->
    
    <!-- Sección 2: Tarjetas de viaje -->
    <!-- <PlanningCards /> -->

    <!-- Sección: Cómo moverse por Bolivia -->
    <PlanningTransport
      :options="transportOptions"
      :selected-transport="selectedTransport"
      @update:selected="selectTransport"
    >
      <InteractiveMap 
        :lugares="lugaresTuristicos" 
        :center="[-16.5, -68.15]" 
        :zoom="6" 
        height="500px"
        :showRoutes="true"
        :selectedTransport="selectedTransport"
      />
    </PlanningTransport>


    <template v-for="(art, idx) in (planningSectionArticles || [])" :key="'planning-article-'+(art?.id || idx)">
      <ArticleImageRight v-if="art && matchesTemplate(art, 22)" :article="art" />
      <ArticleImageLeft v-else-if="art && matchesTemplate(art, 23)" :article="art" />
    </template>






    <!-- Sección: Dónde alojarse -->
    <!-- <PlanningAccommodation /> -->

    <!-- Sección: Clima y tiempo -->
    <!-- <PlanningWeather /> -->

    <!-- Sección: Feriados públicos -->
    <!-- <PlanningHolidays /> -->

    <!-- Sección: Seguridad -->
    <!-- <PlanningSafety /> -->

    <!-- Sección: Información médica -->
    <!-- <PlanningMedical /> -->

    <!-- Sección: Opciones de viaje para personas con discapacidades -->
    <!-- <PlanningDisabilityTravel /> -->

    <!-- Sección: Bueno saber -->
    <PlanningGoodToKnow />

    <!-- Sección: ¿Cuáles son tus intereses? -->
    <!-- <PlanningInterests /> -->

    <!-- Sección: Mejor época para visitar -->
    <PlanningSeasons />
    

    <!-- Sección: Inspírate -->
    <!-- <PlanningInspire /> -->
    
    <!-- Sección: Más inspiración con los siguientes 8 elementos en carousel horizontal -->
    <InterestsSection 
      :items="inspirationLocations" 
      :map-item-to-card="mapLocationToCard"
      :show-header="true"
      section-title="¿Buscas más inspiración?"
      section-subtitle="Descubre otros destinos increíbles para incluir en tu itinerario de viaje por Bolivia"
      :display-mode="'carousel'"
    />


    <NewsletterWeatherSection 
      :key="weatherLoaded ? 'weather-loaded' : 'weather-loading'"
      :weather-temp="weatherTemp"
      :weather-condition="weatherCondition"
      :weather-time="weatherTime"
      :weather-tz="weatherTz"
      :weather-source="weatherSource"
      :weather-icon="weatherIcon"
    />
    
     
    <!-- Botón flotante para volver arriba -->
    <ScrollToTop />
    </div>
  </div>
</template>

<script>
import InteractiveMap from '@/components/InteractiveMap.vue'
import ScrollToTop from '@/components/ScrollToTop.vue'
import PlanningHero from '@/components/planning/PlanningHero.vue'
import PlanningCards from '@/components/planning/PlanningCards.vue'
import PlanningTransport from '@/components/planning/PlanningTransport.vue'

import ArticleImageLeft from '@/components/cultura/ArticleImageLeft.vue'
import ArticleImageRight from '@/components/cultura/ArticleImageRight.vue'


import PlanningAccommodation from '@/components/planning/PlanningAccommodation.vue'
import PlanningWeather from '@/components/planning/PlanningWeather.vue'
import PlanningHolidays from '@/components/planning/PlanningHolidays.vue'
import PlanningSafety from '@/components/planning/PlanningSafety.vue'
import PlanningMedical from '@/components/planning/PlanningMedical.vue'
import PlanningDisabilityTravel from '@/components/planning/PlanningDisabilityTravel.vue'
import PlanningGoodToKnow from '@/components/planning/PlanningGoodToKnow.vue'
import PlanningInterests from '@/components/planning/PlanningInterests.vue'
import PlanningSeasons from '@/components/planning/PlanningSeasons.vue'
import PlanningInspire from '@/components/planning/PlanningInspire.vue'
import InterestsSection from '@/components/home/InterestsSection.vue'

import NewsletterWeatherSection from '@/components/home/NewsletterWeatherSection.vue'
import { weatherService } from '@/services/weather.service'

import FeaturedSection from '@/components/home/FeaturedSection.vue'
import articlesService from '@/services/articles.service'
import { getLocationsWithSubcategories } from '@/services/locations.service'

export default {
  name: 'PlanningPage',
  components: {
    InteractiveMap,
    ScrollToTop,
    PlanningHero,
    PlanningCards,
    PlanningTransport,


    ArticleImageLeft,
    ArticleImageRight,


    PlanningAccommodation,
    PlanningWeather,
    PlanningHolidays,
    PlanningSafety,
    PlanningMedical,
    PlanningDisabilityTravel,
    PlanningGoodToKnow,
    PlanningInterests,
    PlanningSeasons,
    PlanningInspire,
    InterestsSection,
    FeaturedSection,
    NewsletterWeatherSection
  },
  async mounted() {
    await Promise.all([
      this.fetchPlanningArticles(),
      this.loadInspirationLocations(),
      this.fetchWeatherData()
    ])
  },
  methods: {
    // Helper para normalizar template_id (puede venir como string o número)
    normalizeTemplateId(templateId) {
      if (templateId == null) return null
      if (typeof templateId === 'string') {
        return parseInt(templateId, 10)
      }
      return templateId
    },
    
    // Helper para comparar template_id
    matchesTemplate(article, templateId) {
      if (!article || article.template_id == null) return false
      const articleTemplateId = this.normalizeTemplateId(article.template_id)
      return articleTemplateId === templateId
    },
    
    async fetchPlanningArticles() {
      try {
        this.articlesLoading = true
        this.articlesError = ''
        
        const resp = await articlesService.getArticlesByPageSection('planning')
        const items = Array.isArray(resp?.data) ? resp.data : []
        
        console.log('📄 Planning: Total de artículos cargados:', items.length)
        console.log('📋 Planning: Artículos por template_id:', {
          template20: items.filter(a => this.matchesTemplate(a, 20)).length,
          template22: items.filter(a => this.matchesTemplate(a, 22)).length,
          template23: items.filter(a => this.matchesTemplate(a, 23)).length,
          template24: items.filter(a => this.matchesTemplate(a, 24)).length
        })
        
        // template_id = 20 para PlanningHero
        this.planningHeroArticle = items.find(a => this.matchesTemplate(a, 20)) || null
        console.log('✅ Planning: Hero (template_id 20):', this.planningHeroArticle ? this.planningHeroArticle.title : 'No encontrado')
        
        // Cargar artículos para secciones (template_id 22 y 23)
        this.planningSectionArticles = items.filter(a => a && (this.matchesTemplate(a, 22) || this.matchesTemplate(a, 23)))
        console.log('✅ Planning: Section Articles (template_id 22 y 23):', this.planningSectionArticles.length)
        
        // Buscar artículo con template_id = 24 para FeaturedSection
        this.whereToGoArticle = items.find(a => this.matchesTemplate(a, 24)) || null
        console.log('✅ Planning: WhereToGo (template_id 24):', this.whereToGoArticle ? this.whereToGoArticle.title : 'No encontrado')
        
      } catch (err) {
        console.error('Error cargando artículos de planning:', err)
        this.articlesError = 'Error al cargar los artículos'
        this.planningHeroArticle = null
        this.planningSectionArticles = []
        this.whereToGoArticle = null
      } finally {
        this.articlesLoading = false
      }
    },
    selectTransport(transportId) {
      this.selectedTransport = transportId
    },
    async loadInspirationLocations() {
      try {
        this.loadingInspiration = true
        const resp = await getLocationsWithSubcategories()
        if (resp && resp.success && Array.isArray(resp.data)) {
          const sorted = [...resp.data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          // Tomar los elementos del 8 al 16 (los siguientes 8 después de los primeros 8 del home)
          this.inspirationLocations = sorted.slice(8, 16)
        } else {
          this.inspirationLocations = []
        }
      } catch (e) {
        console.error('Error cargando ubicaciones de inspiración:', e)
        this.inspirationLocations = []
      } finally {
        this.loadingInspiration = false
      }
    },
    // mapLocationToCard(location) {
    //   return {
    //     title: location.name || '',
    //     description: location.description || '',
    //     cover: (Array.isArray(location.images) && (location.images.find?.(img => img.is_main)?.image_url || location.images[0]?.image_url)) || '/images/placeholder.jpg',
    //     to: this.mapLocationToRoute(location)
    //   }
    // },
    mapLocationToCard(location) {
  // Extraer la URL cruda de la imagen
      const rawImageUrl = (Array.isArray(location.images) && 
        (location.images.find?.(img => img.is_main)?.image_url || location.images[0]?.image_url)) || 
        '/images/placeholder.jpg'
      
      // ✅ Procesar la URL con $buildImg
      const processedImageUrl = this.$buildImg ? this.$buildImg(rawImageUrl) : rawImageUrl
      
      return {
        title: location.name || '',
        description: location.description || '',
        cover: processedImageUrl,
        to: this.mapLocationToRoute(location)
      }
    },
    mapLocationToRoute(loc) {
      const id = Number(loc.id)
      const type = String(loc.type || '')
      switch (type) {
        case 'museum':
          return { name: 'GastronomyDetail', params: { id } }
        case 'city':
          return { name: 'GastronomyDetail', params: { id } }
        case 'attraction':
        case 'place':
        default:
          return { name: 'GastronomyDetail', params: { id } }
      }
    },
    
    async fetchWeatherData() {
      try {
        console.log('🌤️ Planning: Obteniendo datos del clima...')
        const weatherData = await weatherService.getLaPazWeather()
        
        console.log('✅ Planning: Datos del clima obtenidos:', weatherData)
        
        this.weatherTemp = weatherData.temp
        this.weatherCondition = weatherData.condition
        this.weatherTime = weatherData.time
        this.weatherTz = weatherData.timezone
        this.weatherSource = 'Datos simulados'
        this.weatherIcon = weatherData.icon
        this.weatherLoaded = true
        
        console.log('📊 Planning: Valores actualizados:', {
          temp: this.weatherTemp,
          condition: this.weatherCondition,
          time: this.weatherTime,
          tz: this.weatherTz,
          loaded: this.weatherLoaded
        })
      } catch (err) {
        console.error('❌ Planning: Error obteniendo datos del clima:', err)
        console.log('🔄 Planning: Usando valores por defecto')
      }
    }
  },
  data() {
    return {
      // Loading states
      articlesLoading: false,
      articlesError: '',
      
      planningHeroArticle: null,
      loadingPlanningHero: false,
      planningHeroError: null,
      planningSectionArticles: [],
      whereToGoArticle: null,
      inspirationLocations: [],
      loadingInspiration: false,
      // Weather data
      weatherTemp: '',
      weatherCondition: '',
      weatherTime: '',
      weatherTz: '',
      weatherSource: '',
      weatherIcon: '',
      weatherLoaded: false,
      selectedTransport: 'car',
      transportOptions: [
        {
          id: 'car',
          name: 'en auto',
          icon: 'fas fa-car'
        },
        {
          id: 'bus',
          name: 'en bus',
          icon: 'fas fa-bus'
        },
        {
          id: 'plane',
          name: 'en avión',
          icon: 'fas fa-plane'
        }
      ],
      lugaresTuristicos: [
        {
          id: 1,
          nombre: 'Salar de Uyuni',
          descripcion: 'El desierto de sal más grande del mundo, un espejo natural que refleja el cielo',
          imagenes: ['/images/salar-uyuni.jpg'],
          region: 'Potosí',
          coordenadas: [-20.1339, -67.4891],
          destacado: true
        },
        {
          id: 2,
          nombre: 'Tiwanaku',
          descripcion: 'Sitio arqueológico precolombino, centro espiritual de la cultura Tiwanaku',
          imagenes: ['/images/tiwanaku.jpg'],
          region: 'La Paz',
          coordenadas: [-16.5547, -68.6734],
          destacado: true
        },
        {
          id: 3,
          nombre: 'Lago Titicaca',
          descripcion: 'El lago navegable más alto del mundo, cuna de civilizaciones andinas',
          imagenes: ['/images/titicaca.jpg'],
          region: 'La Paz',
          coordenadas: [-16.1667, -69.0833],
          destacado: true
        },
        {
          id: 4,
          nombre: 'Cristo de la Concordia',
          descripcion: 'Estatua de Cristo más alta del mundo, ubicada en el cerro San Pedro',
          imagenes: ['/images/cristo-cochabamba.jpg'],
          region: 'Cochabamba',
          coordenadas: [-17.3895, -66.1568],
          destacado: false
        },
        {
          id: 5,
          nombre: 'Parque Nacional Amboró',
          descripcion: 'Reserva natural con selva amazónica, cascadas y biodiversidad excepcional',
          imagenes: ['/images/amboro.jpg'],
          region: 'Santa Cruz',
          coordenadas: [-18.1667, -63.8333],
          destacado: false
        },
        {
          id: 6,
          nombre: 'Carnaval de Oruro',
          descripcion: 'Fiesta folklórica declarada Patrimonio de la Humanidad por la UNESCO',
          imagenes: ['/images/carnaval-oruro.jpg'],
          region: 'Oruro',
          coordenadas: [-17.9754, -67.1105],
          destacado: true
        },
        {
          id: 7,
          nombre: 'Cerro Rico',
          descripcion: 'Montaña histórica de la plata, símbolo de la riqueza colonial',
          imagenes: ['/images/cerro-rico.jpg'],
          region: 'Potosí',
          coordenadas: [-19.5833, -65.75],
          destacado: true
        },
        {
          id: 8,
          nombre: 'Valle de Tarija',
          descripcion: 'Tierra del vino y el singani, con clima mediterráneo y paisajes de viñedos',
          imagenes: ['/images/valle-tarija.jpg'],
          region: 'Tarija',
          coordenadas: [-21.5355, -64.7296],
          destacado: false
        }
      ]
    }
  }
}
</script>

<style scoped>

.accommodation-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 500px;
}

.accommodation-text {
  padding-right: 2rem;
}

.accommodation-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.accommodation-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.accommodation-button {
  background: transparent;
  border: 1px solid var(--primary-blue);
  color: var(--primary-blue);
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: var(--font-weight-medium);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: var(--font-family-base);
}

.accommodation-button:hover {
  background: var(--primary-blue);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.accommodation-image {
  position: relative;
  height: 500px;
  overflow: hidden;
}

.image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 0 16px 16px 0;
  overflow: hidden;
}

.accommodation-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.accommodation-photo:hover {
  transform: scale(1.02);
}

.photo-credit {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: var(--font-weight-medium);
}

/* Responsive para alojamiento */
@media (max-width: 991.98px) {
  .accommodation-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .accommodation-text {
    padding-right: 0;
    text-align: center;
  }
  
  .accommodation-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .accommodation-description {
    max-width: 100%;
  }
  
  .accommodation-image {
    height: 350px;
  }
  
  .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .accommodation-section {
    padding: 3rem 0;
  }
  
  .accommodation-title {
    font-size: 2rem;
  }
  
  .accommodation-description {
    font-size: 1rem;
  }
  
  .accommodation-image {
    height: 250px;
  }
}

/* ===============================
   Sección de Clima
   =============================== */
.weather-section {
  padding: 4.5rem 0;
  background: var(--off-white);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.weather-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 500px;
}

.weather-image {
  position: relative;
  height: 500px;
  overflow: hidden;
}

.weather-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 16px 0 0 16px;
  overflow: hidden;
}

.weather-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.weather-photo:hover {
  transform: scale(1.02);
}

.weather-text {
  padding-left: 2rem;
}

.weather-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.weather-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.weather-button {
  background: transparent;
  border: 1px solid var(--primary-blue);
  color: var(--primary-blue);
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: var(--font-weight-medium);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: var(--font-family-base);
}

.weather-button:hover {
  background: var(--primary-blue);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

/* Responsive para clima */
@media (max-width: 991.98px) {
  .weather-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .weather-text {
    padding-left: 0;
    text-align: center;
  }
  
  .weather-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .weather-description {
    max-width: 100%;
  }
  
  .weather-image {
    height: 350px;
  }
  
  .weather-image .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .weather-section {
    padding: 3rem 0;
  }
  
  .weather-title {
    font-size: 2rem;
  }
  
  .weather-description {
    font-size: 1rem;
  }
  
  .weather-image {
    height: 250px;
  }
}

/* ===============================
   Sección de Feriados
   =============================== */
.holidays-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.holidays-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: start;
  min-height: 600px;
}

.holidays-text {
  padding-right: 2rem;
}

.holidays-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.holidays-intro {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.holidays-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.holiday-item {
  border-bottom: 1px solid var(--border-light);
  padding-bottom: 1.5rem;
}

.holiday-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.holiday-date {
  font-size: 1rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.holiday-name {
  font-size: 1.125rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.holiday-description {
  font-size: 0.95rem;
  color: var(--text-secondary);
  line-height: 1.5;
}

.holidays-image {
  position: relative;
  height: 600px;
  overflow: hidden;
}

.holidays-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 0 16px 16px 0;
  overflow: hidden;
}

.holidays-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.holidays-photo:hover {
  transform: scale(1.02);
}

/* Responsive para feriados */
@media (max-width: 991.98px) {
  .holidays-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .holidays-text {
    padding-right: 0;
    text-align: center;
  }
  
  .holidays-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .holidays-intro {
    max-width: 100%;
  }
  
  .holidays-image {
    height: 400px;
  }
  
  .holidays-image .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .holidays-section {
    padding: 3rem 0;
  }
  
  .holidays-title {
    font-size: 2rem;
  }
  
  .holidays-intro {
    font-size: 1rem;
  }
  
  .holidays-image {
    height: 300px;
  }
  
  .holidays-list {
    gap: 1rem;
  }
  
  .holiday-item {
    padding-bottom: 1rem;
  }
  
  .holiday-date {
    font-size: 0.9rem;
  }
  
  .holiday-name {
    font-size: 1rem;
  }
  
  .holiday-description {
    font-size: 0.85rem;
  }
}

/* ===============================
   Sección de Seguridad
   =============================== */
.safety-section {
  padding: 4.5rem 0;
  background: var(--off-white);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.safety-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 500px;
}

.safety-image {
  position: relative;
  height: 500px;
  overflow: hidden;
}

.safety-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 16px 0 0 16px;
  overflow: hidden;
}

.safety-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.safety-photo:hover {
  transform: scale(1.02);
}

.safety-text {
  padding-left: 2rem;
}

.safety-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.safety-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
  max-width: 500px;
}

.safety-description:last-child {
  margin-bottom: 0;
}

/* Responsive para seguridad */
@media (max-width: 991.98px) {
  .safety-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .safety-text {
    padding-left: 0;
    text-align: center;
  }
  
  .safety-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .safety-description {
    max-width: 100%;
  }
  
  .safety-image {
    height: 350px;
  }
  
  .safety-image .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .safety-section {
    padding: 3rem 0;
  }
  
  .safety-title {
    font-size: 2rem;
  }
  
  .safety-description {
    font-size: 1rem;
  }
  
  .safety-image {
    height: 250px;
  }
}

/* ===============================
   Sección de Información Médica
   =============================== */
.medical-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.medical-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: left;
}

.medical-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.medical-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 0;
}

.medical-description strong {
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  text-decoration: underline;
  cursor: pointer;
  transition: color 0.3s ease;
}

.medical-description strong:hover {
  color: var(--primary-blue);
}

/* Responsive para información médica */
@media (max-width: 991.98px) {
  .medical-content {
    max-width: 100%;
    padding: 0 1rem;
  }
  
  .medical-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .medical-description {
    text-align: center;
  }
}

@media (max-width: 575.98px) {
  .medical-section {
    padding: 3rem 0;
  }
  
  .medical-title {
    font-size: 2rem;
  }
  
  .medical-description {
    font-size: 1rem;
  }
}

/* ===============================
   Sección de Visas
   =============================== */
.visas-section {
  padding: 4.5rem 0;
  background: var(--section-accent);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.visas-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: left;
}

.visas-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.visas-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 0;
}

/* Responsive para visas */
@media (max-width: 991.98px) {
  .visas-content {
    max-width: 100%;
    padding: 0 1rem;
  }
  
  .visas-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .visas-description {
    text-align: center;
  }
}

@media (max-width: 575.98px) {
  .visas-section {
    padding: 3rem 0;
  }
  
  .visas-title {
    font-size: 2rem;
  }
  
  .visas-description {
    font-size: 1rem;
  }
}

/* ===============================
   Sección de Accesibilidad
   =============================== */
.accessibility-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.accessibility-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: left;
}

.accessibility-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.accessibility-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 0;
}

/* Responsive para accesibilidad */
@media (max-width: 991.98px) {
  .accessibility-content {
    max-width: 100%;
    padding: 0 1rem;
  }
  
  .accessibility-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .accessibility-description {
    text-align: center;
  }
}

@media (max-width: 575.98px) {
  .accessibility-section {
    padding: 3rem 0;
  }
  
  .accessibility-title {
    font-size: 2rem;
  }
  
  .accessibility-description {
    font-size: 1rem;
  }
}

/* ===============================
   Sección de Opciones de Viaje para Personas con Discapacidades
   =============================== */
.disability-travel-section {
  padding: 4.5rem 0;
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.disability-travel-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 500px;
}

.disability-travel-text {
  padding-right: 2rem;
}

.disability-travel-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.disability-travel-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.disability-travel-button {
  background: transparent;
  border: 1px solid var(--primary-blue);
  color: var(--primary-blue);
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: var(--font-weight-medium);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: var(--font-family-base);
}

.disability-travel-button:hover {
  background: var(--primary-blue);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.disability-travel-image {
  position: relative;
  height: 500px;
  overflow: hidden;
}

.disability-travel-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 0 16px 16px 0;
  overflow: hidden;
}

.disability-travel-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.disability-travel-photo:hover {
  transform: scale(1.02);
}

/* Responsive para opciones de viaje para personas con discapacidades */
@media (max-width: 991.98px) {
  .disability-travel-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .disability-travel-text {
    padding-right: 0;
    text-align: center;
  }
  
  .disability-travel-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .disability-travel-description {
    max-width: 100%;
  }
  
  .disability-travel-image {
    height: 350px;
  }
  
  .disability-travel-image .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .disability-travel-section {
    padding: 3rem 0;
  }
  
  .disability-travel-title {
    font-size: 2rem;
  }
  
  .disability-travel-description {
    font-size: 1rem;
  }
  
  .disability-travel-image {
    height: 250px;
  }
}

/* ===============================
   Sección de Bueno Saber
   =============================== */
.good-to-know-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
  position: relative;
}

.good-to-know-section::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: var(--bolivia-green);
}

.good-to-know-content {
  max-width: 1200px;
  margin: 0 auto;
  padding-left: 2rem;
}

.good-to-know-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1rem;
  text-align: left;
}

.good-to-know-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 3rem;
  text-align: left;
}

.info-columns {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 3rem;
}

.info-column {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.info-heading {
  font-size: 1rem;
  font-weight: var(--font-weight-semibold);
  color: var(--bolivia-purple);
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-text {
  font-size: 1rem;
  color: var(--text-primary);
  line-height: 1.5;
  margin: 0;
}

/* Responsive para bueno saber */
@media (max-width: 991.98px) {
  .good-to-know-content {
    padding-left: 1rem;
  }
  
  .good-to-know-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .good-to-know-subtitle {
    text-align: center;
  }
  
  .info-columns {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .info-column {
    gap: 1.5rem;
  }
}

@media (max-width: 575.98px) {
  .good-to-know-section {
    padding: 3rem 0;
  }
  
  .good-to-know-title {
    font-size: 2rem;
  }
  
  .good-to-know-subtitle {
    font-size: 1rem;
  }
  
  .info-columns {
    gap: 1.5rem;
  }
  
  .info-column {
    gap: 1rem;
  }
  
  .info-heading {
    font-size: 0.9rem;
  }
  
  .info-text {
    font-size: 0.9rem;
  }
}

/* ===============================
   Sección de Estaciones
   =============================== */
.seasons-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.seasons-content {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.seasons-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.25rem;
  text-align: center;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
}

.seasons-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 3rem;
  text-align: center;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.seasons-cards {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 1.5rem;
  margin-top: 2rem;
}

.season-card {
  background: var(--white);
  border-radius: 20px;
  padding: 2rem 1.5rem;
  text-align: center;
  transition: all 0.3s ease;
  cursor: pointer;
  border: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
  position: relative;
  overflow: hidden;
}

.season-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  transition: all 0.3s ease;
}

.season-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.season-card.spring {
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.season-card.spring::before {
  background: var(--bolivia-green);
}

.season-card.summer {
  background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
}

.season-card.summer::before {
  background: var(--bolivia-yellow);
}

.season-card.autumn {
  background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
}

.season-card.autumn::before {
  background: var(--bolivia-purple);
}

.season-card.winter {
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
}

.season-card.winter::before {
  background: var(--primary-blue);
}

.season-card.dry {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.season-card.dry::before {
  background: var(--text-muted);
}

.season-icon {
  font-size: 2.5rem;
  color: var(--text-primary);
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.season-card:hover .season-icon {
  transform: scale(1.1);
  color: var(--primary-blue);
}

.season-name {
  font-size: 1.25rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  letter-spacing: -0.01em;
}

.season-description {
  font-size: 0.95rem;
  color: var(--text-secondary);
  font-weight: var(--font-weight-medium);
}

/* Responsive para estaciones */
@media (max-width: 991.98px) {
  .seasons-cards {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
  }
  
  .seasons-title {
    font-size: 2.5rem;
    line-height: 1.15;
  }
  
  .seasons-subtitle {
    font-size: 1rem;
  }
  
  .season-card {
    padding: 1.5rem 1rem;
  }
  
  .season-icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
  }
  
  .season-name {
    font-size: 1.125rem;
  }
  
  .season-description {
    font-size: 0.9rem;
  }
}

@media (max-width: 768px) {
  .seasons-cards {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .seasons-title {
    font-size: 2rem;
  }
  
  .seasons-subtitle {
    font-size: 0.95rem;
    margin-bottom: 2rem;
  }
  
  .season-card {
    padding: 1.25rem 0.75rem;
  }
  
  .season-icon {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
  }
  
  .season-name {
    font-size: 1rem;
  }
  
  .season-description {
    font-size: 0.85rem;
  }
}

@media (max-width: 575.98px) {
  .seasons-section {
    padding: 3rem 0;
  }
  
  .seasons-cards {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
  
  .seasons-title {
    font-size: 1.75rem;
    line-height: 1.2;
  }
  
  .seasons-subtitle {
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }
  
  .season-card {
    padding: 1rem;
    border-radius: 16px;
  }
  
  .season-icon {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }
  
  .season-name {
    font-size: 0.95rem;
  }
  
  .season-description {
    font-size: 0.8rem;
  }
}

/* ===============================
   Sección de Intereses
   =============================== */
.interests-section {
  padding: 4.5rem 0;
  background: var(--off-white);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.interests-content {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.interests-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1rem;
  text-align: center;
}

.interests-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 3rem;
  text-align: center;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.interests-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.interest-card {
  background: var(--bg-primary);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.interest-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.card-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.card-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.interest-card:hover .card-photo {
  transform: scale(1.05);
}

.card-content {
  padding: 1.5rem;
}

.card-title {
  font-size: 1rem;
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
  line-height: 1.4;
  margin: 0;
  text-align: left;
}

/* Responsive para intereses */
@media (max-width: 991.98px) {
  .interests-cards {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
  }
  
  .interests-title {
    font-size: 2.5rem;
  }
  
  .interests-subtitle {
    font-size: 1rem;
  }
  
  .card-image {
    height: 180px;
  }
  
  .card-content {
    padding: 1.25rem;
  }
  
  .card-title {
    font-size: 0.95rem;
  }
}

@media (max-width: 575.98px) {
  .interests-section {
    padding: 3rem 0;
  }
  
  .interests-title {
    font-size: 2rem;
  }
  
  .interests-subtitle {
    font-size: 0.95rem;
    margin-bottom: 2rem;
  }
  
  .interests-cards {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .card-image {
    height: 160px;
  }
  
  .card-content {
    padding: 1rem;
  }
  
  .card-title {
    font-size: 0.9rem;
    text-align: center;
  }
}

/* ===============================
   Sección de Inspiración
   =============================== */
.inspire-section {
  padding: 4.5rem 0;
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.inspire-content {
  max-width: 1200px;
  margin: 0 auto;
}

.inspire-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 3rem;
  text-align: left;
}

.carousel-container {
  position: relative;
}

.carousel-wrapper {
  overflow: hidden;
  border-radius: 16px;
}

.carousel-track {
  display: flex;
  transition: transform 0.5s ease;
  gap: 1.5rem;
}

.inspire-card {
  flex: 0 0 300px;
  background: var(--bg-primary);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.inspire-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.inspire-card .card-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.inspire-card .card-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.inspire-card:hover .card-photo {
  transform: scale(1.05);
}

.inspire-card .card-content {
  padding: 1.5rem;
}

.inspire-card .card-title {
  font-size: 1rem;
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
  line-height: 1.4;
  margin: 0;
  text-align: left;
}

.carousel-controls {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 2rem;
  gap: 1rem;
}

.progress-bar {
  flex: 1;
  height: 4px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: var(--text-primary);
  border-radius: 2px;
  transition: width 0.3s ease;
}

.navigation-buttons {
  display: flex;
  gap: 0.5rem;
}

.nav-button {
  width: 40px;
  height: 40px;
  border: none;
  background: var(--bg-primary);
  color: var(--text-primary);
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.nav-button:hover:not(:disabled) {
  background: var(--primary-blue);
  color: white;
  transform: scale(1.1);
}

.nav-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.nav-button i {
  font-size: 0.875rem;
}

/* Responsive para inspiración */
@media (max-width: 991.98px) {
  .inspire-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .carousel-track {
    gap: 1rem;
  }
  
  .inspire-card {
    flex: 0 0 280px;
  }
  
  .inspire-card .card-image {
    height: 180px;
  }
  
  .inspire-card .card-content {
    padding: 1.25rem;
  }
  
  .inspire-card .card-title {
    font-size: 0.95rem;
  }
}

@media (max-width: 575.98px) {
  .inspire-section {
    padding: 3rem 0;
  }
  
  .inspire-title {
    font-size: 2rem;
    margin-bottom: 2rem;
  }
  
  .carousel-track {
    gap: 0.75rem;
  }
  
  .inspire-card {
    flex: 0 0 250px;
  }
  
  .inspire-card .card-image {
    height: 160px;
  }
  
  .inspire-card .card-content {
    padding: 1rem;
  }
  
  .inspire-card .card-title {
    font-size: 0.9rem;
  }
  
  .carousel-controls {
    margin-top: 1.5rem;
  }
  
  .nav-button {
    width: 36px;
    height: 36px;
  }
  
  .nav-button i {
    font-size: 0.75rem;
  }
}

/* Loading spinner */
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

/* ===============================
   Sección de Transporte
   =============================== */
.transport-section {
  padding: 4.5rem 0;
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.transport-header {
  text-align: center;
  margin-bottom: 3rem;
}

.transport-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.25rem;
}

.transport-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}

.transport-content {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 4rem;
  align-items: start;
}

.transport-options {
  display: flex;
  flex-direction: column;
}

.transport-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.transport-option {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  font-size: 1rem;
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
  width: 100%;
}

.transport-option:hover {
  background: var(--light-gray);
  border-color: var(--primary-blue);
  transform: translateX(4px);
}

.transport-option.active {
  background: var(--primary-blue);
  color: var(--white);
  border-color: var(--primary-blue);
  box-shadow: var(--shadow-md);
}

.transport-option i {
  font-size: 1.25rem;
  width: 24px;
  text-align: center;
}

.transport-map {
  background: var(--white);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-light);
}

.bolivia-map {
  width: 100%;
  height: 500px;
  border-radius: 16px;
  overflow: hidden;
}

/* Responsive para transporte */
@media (max-width: 991.98px) {
  .transport-section {
    padding: 3.5rem 0;
  }
  
  .transport-title {
    font-size: 2.25rem;
    line-height: 1.15;
  }
  
  .transport-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .transport-list {
    flex-direction: row;
    overflow-x: auto;
    padding-bottom: 1rem;
    gap: 0.5rem;
  }
  
  .transport-option {
    min-width: 140px;
    flex-shrink: 0;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
  }
  
  .transport-option i {
    font-size: 1rem;
  }
  
  .bolivia-map {
    height: 400px;
  }
}

@media (max-width: 768px) {
  .transport-option {
    flex-direction: column;
    text-align: center;
    gap: 0.5rem;
    min-width: 100px;
    padding: 0.75rem 0.5rem;
  }
  
  .transport-option span {
    font-size: 0.8rem;
  }
}
</style>