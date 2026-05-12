<template>
  <div class="where-to-go">
    <!-- Loading state para artículos -->
    <div v-if="articlesLoading" class="text-center py-5">
      <div class="loading-spinner mb-2"></div>
      <p>Cargando contenido...</p>
    </div>
    
    <!-- Error state para artículos -->
    <div v-else-if="articlesError" class="text-center py-5">
      <p class="text-danger mb-3">{{ articlesError }}</p>
      <button class="btn btn-primary" @click="fetchWhereToGoArticles">Reintentar</button>
    </div>
    
    <!-- Contenido principal -->
    <div v-else>
      <WhereToGoHeroSection v-if="showHeroSection" :title="heroTitle" />
    <!-- <PopularDestinationsSection /> -->
    <AccommodationExperiencesSection 
      v-if="showAccommodationsSection"
      :title="accommodationsTitle"
      :subtitle="accommodationsSubtitle"
    />
    <CitiesCarouselSection 
      v-if="showCitiesSection"
      :title="citiesTitle"
      :subtitle="citiesSubtitle"
    />
    <!-- <InspirationSection /> -->
    <!-- <InspirationSections/> -->
    <ArticleSlider :article="sliderArticle" />

    <InspirationSections
      v-if="showInspirationSection"
      :start="8"
      :limit="8"
      :title="inspirationTitle"
      :subtitle="inspirationSubtitle"
    />
    <!-- <AutumnSliderSection /> -->

       <!-- AQUI AGREGAMOS  LOS ITEMS SLIDER DE IMAGENES -->
   


    <!-- <PlanningHeroSection /> -->
    <MapSection 
      v-if="showMapSection"
      :title="mapTitle"
      :subtitle="mapSubtitle"
    />
    
    <!-- CTA -->
    <!-- <section class="cta">
      <div class="container text-center">
        <h2 class="section-title">¿Listo para explorar?</h2>
        <router-link to="/lugares" class="btn-primary">Ver todos los destinos</router-link>
      </div>
    </section> -->

     <NewsletterWeatherSection 
       :key="weatherLoaded ? 'weather-loaded' : 'weather-loading'"
       :title="newsletterTitle"
       :visitor-description="newsletterVisitorDesc"
       :business-description="newsletterBusinessDesc"
       :nomad-description="newsletterNomadDesc"
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
import WhereToGoHeroSection from '@/components/wheretogo/WhereToGoHeroSection.vue'
import PopularDestinationsSection from '@/components/wheretogo/PopularDestinationsSection.vue'
import AccommodationExperiencesSection from '@/components/wheretogo/AccommodationExperiencesSection.vue'
import CitiesCarouselSection from '@/components/wheretogo/CitiesCarouselSection.vue'
import InspirationSection from '@/components/wheretogo/InspirationSection.vue'

import InspirationSections from '@/components/home/InspirationSection.vue'




import AutumnSliderSection from '@/components/wheretogo/AutumnSliderSection.vue'
import ArticleSlider from '@/components/cultura/ArticleSlider.vue'


import PlanningHeroSection from '@/components/wheretogo/PlanningHeroSection.vue'
import MapSection from '@/components/home/MapSection.vue'

import ScrollToTop from '@/components/ScrollToTop.vue'
import articlesService from '@/services/articles.service'
import { weatherService } from '@/services/weather.service'
import NewsletterWeatherSection from '@/components/home/NewsletterWeatherSection.vue'

export default {
  name: 'WhereToGoPage',
  components: {
    WhereToGoHeroSection,
    PopularDestinationsSection,
    AccommodationExperiencesSection,
    CitiesCarouselSection,
    // InspirationSection,
    InspirationSections,
    AutumnSliderSection,
    ArticleSlider,
    PlanningHeroSection,
    MapSection,
    NewsletterWeatherSection,
    ScrollToTop
  },
  data() {
    return {
      // Loading states
      articlesLoading: false,
      articlesError: '',
      
      sliderArticle: null,
      loadingSlider: false,
      sliderError: null,
      showHeroSection: false,
      heroTitle: '',
      showAccommodationsSection: false,
      accommodationsTitle: '',
      accommodationsSubtitle: '',
      showCitiesSection: false,
      citiesTitle: '',
      citiesSubtitle: '',
      showInspirationSection: false,
      inspirationTitle: '',
      inspirationSubtitle: '',
      showMapSection: false,
      mapTitle: '',
      mapSubtitle: '',
      // Newsletter data
      newsletterTitle: '',
      newsletterVisitorDesc: '',
      newsletterBusinessDesc: '',
      newsletterNomadDesc: '',
      // Weather data
      weatherTemp: '',
      weatherCondition: '',
      weatherTime: '',
      weatherTz: '',
      weatherSource: '',
      weatherIcon: '',
      weatherLoaded: false
    }
  },
  async mounted() {
    await Promise.all([
      this.fetchWhereToGoArticles(),
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
    
    async fetchWhereToGoArticles() {
      try {
        this.articlesLoading = true
        this.articlesError = ''
        
        const resp = await articlesService.getArticlesByPageSection('where_to_go')
        const list = Array.isArray(resp?.data) ? resp.data : []
        
        console.log('📄 WhereToGo: Total de artículos cargados:', list.length)
        console.log('📋 WhereToGo: Artículos por template_id:', {
          template20: list.filter(a => this.matchesTemplate(a, 20)).length,
          template21: list.filter(a => this.matchesTemplate(a, 21)).length,
          template22: list.filter(a => this.matchesTemplate(a, 22)).length,
          template23: list.filter(a => this.matchesTemplate(a, 23)).length,
          template24: list.filter(a => this.matchesTemplate(a, 24)).length,
          template26: list.filter(a => this.matchesTemplate(a, 26)).length,
          template27: list.filter(a => this.matchesTemplate(a, 27)).length
        })
        
        const heroArticle = list.find(a => this.matchesTemplate(a, 20)) || null
        this.showHeroSection = !!heroArticle
        this.heroTitle = heroArticle?.title || ''
        console.log('✅ WhereToGo: Hero (template_id 20):', heroArticle ? heroArticle.title : 'No encontrado')
        
        const accArticle = list.find(a => this.matchesTemplate(a, 21)) || null
        this.showAccommodationsSection = !!accArticle
        this.accommodationsTitle = accArticle?.title || ''
        this.accommodationsSubtitle = accArticle?.subtitle || accArticle?.description || ''
        console.log('✅ WhereToGo: Accommodations (template_id 21):', accArticle ? accArticle.title : 'No encontrado')
        
        const citiesArticle = list.find(a => this.matchesTemplate(a, 22)) || null
        this.showCitiesSection = !!citiesArticle
        this.citiesTitle = citiesArticle?.title || ''
        this.citiesSubtitle = citiesArticle?.subtitle || citiesArticle?.description || ''
        console.log('✅ WhereToGo: Cities (template_id 22):', citiesArticle ? citiesArticle.title : 'No encontrado')
        
        const inspirationArticle = list.find(a => this.matchesTemplate(a, 23)) || null
        this.showInspirationSection = !!inspirationArticle
        this.inspirationTitle = inspirationArticle?.title || ''
        this.inspirationSubtitle = inspirationArticle?.subtitle || inspirationArticle?.description || ''
        console.log('✅ WhereToGo: Inspiration (template_id 23):', inspirationArticle ? inspirationArticle.title : 'No encontrado')
        
        const mapArticle = list.find(a => this.matchesTemplate(a, 26)) || null
        this.showMapSection = !!mapArticle
        this.mapTitle = mapArticle?.title || ''
        this.mapSubtitle = mapArticle?.subtitle || mapArticle?.description || ''
        console.log('✅ WhereToGo: Map (template_id 26):', mapArticle ? mapArticle.title : 'No encontrado')
        
        const article24 = list.find(a => this.matchesTemplate(a, 24)) || null
        this.sliderArticle = article24
        console.log('✅ WhereToGo: Slider (template_id 24):', article24 ? article24.title : 'No encontrado')
        
        // Buscar artículo con template_id = 27 para NewsletterWeatherSection
        const newsletterArticle = list.find(a => this.matchesTemplate(a, 27)) || null
        if (newsletterArticle) {
          this.newsletterTitle = newsletterArticle.title || ''
          this.newsletterVisitorDesc = newsletterArticle.subtitle || ''
          this.newsletterBusinessDesc = newsletterArticle.description || ''
          this.newsletterNomadDesc = newsletterArticle.content || ''
          console.log('✅ WhereToGo: Newsletter (template_id 27):', newsletterArticle.title)
        } else {
          console.log('⚠️ WhereToGo: Newsletter (template_id 27): No encontrado')
        }
      } catch (e) {
        console.error('Error loading where_to_go articles:', e)
        this.articlesError = 'Error al cargar los artículos'
        this.sliderArticle = null
      } finally {
        this.articlesLoading = false
      }
    },
    
    async fetchWeatherData() {
      try {
        console.log('🌤️ WhereToGo: Obteniendo datos del clima...')
        const weatherData = await weatherService.getLaPazWeather()
        
        console.log('✅ WhereToGo: Datos del clima obtenidos:', weatherData)
        
        this.weatherTemp = weatherData.temp
        this.weatherCondition = weatherData.condition
        this.weatherTime = weatherData.time
        this.weatherTz = weatherData.timezone
        this.weatherSource = 'Datos simulados'
        this.weatherIcon = weatherData.icon
        this.weatherLoaded = true
        
        console.log('📊 WhereToGo: Valores actualizados:', {
          temp: this.weatherTemp,
          condition: this.weatherCondition,
          time: this.weatherTime,
          tz: this.weatherTz,
          loaded: this.weatherLoaded
        })
      } catch (err) {
        console.error('❌ WhereToGo: Error obteniendo datos del clima:', err)
        console.log('🔄 WhereToGo: Usando valores por defecto')
      }
    }
  }
}
</script>

<style scoped>
.where-to-go { 
  background: var(--bg-secondary);
  color: var(--text-primary);
  font-family: var(--font-family-base) !important;
  min-height: 100vh;
}

/* CTA section */
.cta {
  padding: 4rem 0;
  text-align: center;
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-light);
}

.section-title {
  font-size: 2rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  margin-bottom: 2rem;
  font-family: var(--font-family-base) !important;
  text-align: center;
  letter-spacing: -0.02em;
}

.btn-primary {
  display: inline-block;
  background: var(--primary-blue);
  color: var(--white);
  padding: 1rem 2rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: var(--font-weight-medium) !important;
  font-family: var(--font-family-base) !important;
  font-size: 1rem !important;
  transition: all 0.3s ease;
  box-shadow: var(--shadow-sm);
}

.btn-primary:hover {
  background: var(--primary-blue-dark);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

/* Responsive */
@media (max-width: 767.98px) {
  .cta {
    padding: 3rem 0;
  }
  
  .section-title {
    font-size: 1.75rem !important;
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
</style>
