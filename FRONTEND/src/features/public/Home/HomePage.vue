<template>
  <div class="home-page">
    <!-- Debug info -->
    <!-- <div style="background: #f0f0f0; padding: 10px; margin: 10px; border-radius: 5px; font-size: 12px;">
      <strong>Debug:</strong><br>
      articlesLoading: {{ articlesLoading }}<br>
      articlesError: {{ articlesError }}<br>
      heroArticle: {{ heroArticle ? heroArticle.title : 'null' }}<br>
      homeArticles.length: {{ homeArticles.length }}
    </div> -->
    
    <!-- Hero Section dinámico - solo si hay artículo con template_id = 20 -->
    <HeroSection v-if="heroArticle" :article="heroArticle" />
    
    <!-- Loading state para artículos -->
    <div v-if="articlesLoading" class="text-center py-5">
      <div class="loading-spinner mb-2"></div>
      <p>Cargando contenido...</p>
    </div>
    
    <!-- Error state para artículos -->
    <div v-else-if="articlesError" class="text-center py-5">
      <p class="text-danger mb-3">{{ articlesError }}</p>
      <button class="btn btn-primary" @click="fetchHomeArticles">Reintentar</button>
    </div>
    
    <!-- Contenido principal -->
    <div v-else>
    <!-- <section class="map-embed-section">
      <div class="container">
        <h2 class="map-embed-title">Mapa de lugares destacados</h2>
        <p class="map-embed-subtitle">Explora puntos de interés y planifica tu recorrido</p>
        <div class="map-embed-wrapper">
          <iframe
            class="map-embed"
            src="https://www.google.com/maps/d/embed?mid=1yi1cTOKRITfcwbC_4R9uX80cRAWovqc&ehbc=2E312F&noprof=1"
            title="Mapa de lugares destacados de Bolivia"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </section> -->
    
    <!-- InterestsSection dinámico - solo si hay artículo con template_id = 21 -->
    <InterestsSection v-if="interestsArticle" :article="interestsArticle" />
    
    <!-- InterestsSection estático si no hay artículo dinámico -->
    <InterestsSection v-else />
    
    <!-- ArticleHistory dinámico - solo si hay artículo con template_id = 22 -->
    <ArticleHistory v-if="historyArticle" :article="historyArticle" />
    
    <!-- EventsSection dinámico - solo si hay artículo con template_id = 23 -->
    <InterestsSection 
      v-if="eventsArticle"
      :article="eventsArticle"
      :items="upcomingEvents" 
      :map-item-to-card="mapEventToCard"
      :show-header="true"
      :cta-label="'Ver todos'"
      :cta-to="eventsArticle?.link || { name: 'Search', query: { tab: 'eventos' } }"
    />
    
    <!-- EventsSection estático si no hay artículo dinámico -->
    <InterestsSection 
      v-else
      :items="upcomingEvents" 
      :map-item-to-card="mapEventToCard"
      :show-header="true"
      section-title="Vive las festividades en Bolivia"
      section-subtitle="Desde música folklórica hasta música alternativa, desde disc golf hasta deportes callejeros, este año se ofrecen eventos para todos los gustos."
      :cta-label="'Ver todos'"
      :cta-to="{ name: 'Search', query: { tab: 'eventos' } }"
    />
    
    <!-- <PromoSection /> -->


    <MapSection />
  
    <FeaturedSection />
    <FactsSection />
    <!-- <CategoriesSection />
    <DepartmentsSection /> -->
    <InspirationSection />
    <!-- <InspirationSection /> -->
    
    <!-- ArticleImageRight dinámico - solo si hay artículo con template_id = 24 -->
    <ArticleImageRight v-if="imageRightArticle" :article="imageRightArticle" />
    
    <!-- ArticleImageLeft dinámico - solo si hay artículo con template_id = 25 -->
    <ArticleImageLeft v-if="imageLeftArticle" :article="imageLeftArticle" />
    


<!-- Seasons Section -->
    <!-- <SeasonsSection /> -->

    <PlanningSeasons/>

    <!-- 360° Viewer Section -->
    <section class="viewer-360-section">
      <div class="container">
        <div class="viewer-360-header">
          <h2 class="viewer-360-title">Explora Bolivia en 360°</h2>
          <p class="viewer-360-subtitle">Descubre los lugares más hermosos de Bolivia con nuestra experiencia inmersiva</p>
        </div>
        
        <div class="viewer-360-wrapper">
          <iframe
            class="pannellum-container"
            :src="momentoSrc"
            title="Bolivia 360° - Momento360"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
        
        <div class="viewer-360-museums">
          <div
            v-for="(tour, index) in momento360Tours"
            :key="tour.id || index"
            class="museum-card"
            :class="{ active: activeTourIndex === index }"
            @click="loadTour(index)"
          >
            <div class="museum-card-image" v-if="tour.imageUrl">
              <img :src="tour.imageUrl" :alt="tour.name" />
              <div class="museum-card-overlay"></div>
            </div>
            <div class="museum-card-icon" v-else>
              <i :class="tour.icon"></i>
            </div>
            <div class="museum-card-content">
              <h3 class="museum-card-title">{{ tour.name }}</h3>
             
            </div>
            <div class="museum-card-badge" v-if="activeTourIndex === index">
              <i class="fas fa-check"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter + Weather Section -->
    <NewsletterWeatherSection 
      v-if="newsletterArticle"
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
    
    <!-- Newsletter + Weather Section con datos del clima -->
    <NewsletterWeatherSection 
      v-else
      :key="weatherLoaded ? 'weather-loaded' : 'weather-loading'"
      :weather-temp="weatherTemp"
      :weather-condition="weatherCondition"
      :weather-time="weatherTime"
      :weather-tz="weatherTz"
      :weather-source="weatherSource"
      :weather-icon="weatherIcon"
    />
    
    

    <ScrollToTop />
    </div>
  </div>
</template>

<script>

import { ref, computed, onMounted, getCurrentInstance } from 'vue'
import { RouterLink } from 'vue-router'
import HeroSection from '@/components/cultura/HeroSection.vue'
import InterestsSection from '@/components/home/InterestsSection.vue'
import PromoSection from '@/components/home/PromoSection.vue'
import MapSection from '@/components/home/MapSection.vue'
import FeaturedSection from '@/components/home/FeaturedSection.vue'
import FactsSection from '@/components/home/FactsSection.vue'
import CategoriesSection from '@/components/home/CategoriesSection.vue'
import DepartmentsSection from '@/components/home/DepartmentsSection.vue'
import InspirationSection from '@/components/home/InspirationSection.vue'
import ArticleImageLeft from '@/components/cultura/ArticleImageLeft.vue'
import ArticleImageRight from '@/components/cultura/ArticleImageRight.vue'
import ArticleHistory from '@/components/cultura/ArticleHistory.vue'
import ScrollToTop from '@/components/ScrollToTop.vue'
import  eventsService  from '@/services/eventsService'
import { articlesService } from '@/services/articles.service'
import { weatherService } from '@/services/weather.service'
import NewsletterWeatherSection from '@/components/home/NewsletterWeatherSection.vue'
import SeasonsSection from '@/components/home/SeasonsSection.vue'

import PlanningSeasons from '@/components/planning/PlanningSeasons.vue'



export default {
  name: 'HomePage',
  components: {
    RouterLink,
    HeroSection,
    InterestsSection,
    PromoSection,
    MapSection,
    FeaturedSection,
    FactsSection,
    CategoriesSection,
    DepartmentsSection,
    InspirationSection,
    ArticleImageLeft,
    ArticleImageRight,
    ArticleHistory,
    ScrollToTop,
    NewsletterWeatherSection,
    PlanningSeasons,
    SeasonsSection
  },
  setup() {
    // Estado para eventos
    const upcomingEvents = ref([])
    const eventsLoading = ref(false)
    const eventsError = ref('')

    // Estado para artículos de home
    const homeArticles = ref([])
    const articlesLoading = ref(false)
    const articlesError = ref('')
    const heroArticle = ref(null)
    const interestsArticle = ref(null)
    const historyArticle = ref(null)
    const eventsArticle = ref(null)
    const imageLeftArticle = ref(null)
    const imageRightArticle = ref(null)
    const newsletterArticle = ref(null)
    
    // Newsletter data
    const newsletterTitle = ref('')
    const newsletterVisitorDesc = ref('')
    const newsletterBusinessDesc = ref('')
    const newsletterNomadDesc = ref('')
    
    // Weather data (puedes integrar con API de clima)
    const weatherTemp = ref('')
    const weatherCondition = ref('')
    const weatherTime = ref('')
    const weatherTz = ref('')
    const weatherSource = ref('')
    const weatherIcon = ref('')
    const weatherLoaded = ref(false)
    
    // Datos estáticos de museos con tours 360°
    const staticMuseums = [
      {
        id: 1,
        name: 'CONCEPCION',
       
     
        momentoUrl: 'https://momento360.com/e/u/6e77663416dd42e1ae521446119a0ae0?utm_campaign=embed&utm_source=other&heading=6.8&pitch=12.3&field-of-view=75&size=medium&display-plan=true',
        imageUrl: 'images/museos/concepcion.jpeg',
        
      },
      {
        id: 2,
        name: 'SAN XAVIER',
       
       
        momentoUrl: 'https://momento360.com/e/u/159ed578e0014c78846cc2b6b99776e3?utm_campaign=embed&utm_source=other&heading=-1.5&pitch=19.4&field-of-view=75&size=medium&display-plan=true',
        imageUrl: 'images/museos/sanxavier.jpeg',
        
      },
      {
        id: 3,
        name: 'SAN IGNACIO',
       
       
        momentoUrl: 'https://momento360.com/e/u/d356fca5efd84814be70e7ddfff808e2?utm_campaign=embed&utm_source=other&heading=0&pitch=0&field-of-view=75&size=medium&display-plan=true',
        imageUrl: 'images/museos/sanignacio.jpeg',
        
      },
      {
        id: 4,
        name: 'SAN MIGUEL',
       
        momentoUrl: 'https://momento360.com/e/u/770d82352ff248f2aa10e7d2ddc61aeb?utm_campaign=embed&utm_source=other&heading=0&pitch=0&field-of-view=75&size=medium&display-plan=true',
        imageUrl: 'images/museos/sanmiguel.jpeg',
        
      },
      {
        id: 5,
        name: 'SAN JOSE',
       
        
        momentoUrl: 'https://momento360.com/e/u/66e30a44cc0446eba36147b8652e70fe?utm_campaign=embed&utm_source=other&heading=0&pitch=0&field-of-view=75&size=medium&display-plan=true',
        imageUrl: '/images/museos/sanjose.jpeg',
        
      },
      {
        id: 6,
        name: 'SAN RAFAEL',
       
      
        momentoUrl: 'https://momento360.com/e/u/a7ba0930afb4466f8d48f291d93f2ef0?utm_campaign=embed&utm_source=other&heading=0&pitch=0&field-of-view=75&size=medium&display-plan=true',
        imageUrl: 'images/museos/sanrafael.jpeg',
        
      }
    ]
    
    // Estado de museos (datos estáticos)
    const museums = ref(staticMuseums)
    const museumsLoading = ref(false)
    const museumsError = ref('')
    
    // Inicializar museos con datos estáticos
    const fetchMuseums = () => {
      museums.value = staticMuseums
      if (museums.value.length > 0) {
        momentoSrc.value = museums.value[0].momentoUrl
        activeTourIndex.value = 0
      }
    }
    
    // Estado del viewer 360
    const momentoSrc = ref(staticMuseums[0]?.momentoUrl || '')
    const activeTourIndex = ref(0)
    
    const loadTour = (index) => {
      if (museums.value[index]?.momentoUrl) {
        activeTourIndex.value = index
        momentoSrc.value = museums.value[index].momentoUrl
      }
    }
    
    // Computed para obtener los tours con datos de museos
    const momento360Tours = computed(() => {
      return museums.value.map((museum) => ({
        id: museum.id,
        name: museum.name,
        location: museum.location,
        description: museum.description,
        url: museum.momentoUrl,
        imageUrl: museum.imageUrl,
        icon: museum.icon || 'fas fa-university'
      }))
    })

    const instance = getCurrentInstance()
    const $buildImg = instance?.appContext.config.globalProperties.$buildImg

    const fetchEvents = async () => {
      try {
        eventsLoading.value = true
        eventsError.value = ''
        const response = await eventsService.getAllLocationEvents()
        if (response.success && response.data) {
          upcomingEvents.value = response.data.map(event => eventsService.transformLocationToEventCard(event))
        } else {
          throw new Error('Error en la respuesta de la API')
        }
      } catch (err) {
        console.error('Error cargando eventos:', err)
        eventsError.value = 'Error al cargar los eventos'
        upcomingEvents.value = []
      } finally {
        eventsLoading.value = false
      }
    }

    const fetchWeatherData = async () => {
      try {
        console.log('🌤️ Obteniendo datos del clima...')
        console.log('🔑 API Key configurada:', weatherService.API_KEY ? 'Sí' : 'No')
        
        const weatherData = await weatherService.getLaPazWeather()
        
        console.log('✅ Datos del clima obtenidos:', weatherData)
        
        weatherTemp.value = weatherData.temp
        weatherCondition.value = weatherData.condition
        weatherTime.value = weatherData.time
        weatherTz.value = weatherData.timezone
        weatherSource.value = 'Datos simulados'
        weatherIcon.value = weatherData.icon
        weatherLoaded.value = true
        
        console.log('📊 Valores actualizados:', {
          temp: weatherTemp.value,
          condition: weatherCondition.value,
          time: weatherTime.value,
          tz: weatherTz.value,
          loaded: weatherLoaded.value
        })
      } catch (err) {
        console.error('❌ Error obteniendo datos del clima:', err)
        console.log('🔄 Usando valores por defecto')
      }
    }

    const fetchHomeArticles = async () => {
      try {
        console.log('Iniciando carga de artículos de home...')
        articlesLoading.value = true
        articlesError.value = ''
        
        const response = await articlesService.getHomeArticles()
        console.log('Respuesta completa de API:', response)
        
        if (response.success && response.data) {
          homeArticles.value = response.data
          console.log('Artículos cargados:', response.data)
          
          // Buscar artículo con template_id = 20 para el hero
          heroArticle.value = articlesService.getFirstByTemplate(response.data, 20)
          console.log('🔍 Buscando artículo hero con template_id = 20')
          console.log('📋 Todos los template_id en los artículos:', response.data.map(a => ({ id: a.id, template_id: a.template_id, title: a.title })))
          console.log('✅ Artículo hero encontrado:', heroArticle.value)
          if (!heroArticle.value) {
            console.warn('⚠️ No se encontró ningún artículo con template_id = 20')
          }
          
          // Buscar artículo con template_id = 21 para InterestsSection
          interestsArticle.value = articlesService.getFirstByTemplate(response.data, 21)
          console.log('Artículo interests encontrado:', interestsArticle.value)
          
          // Buscar artículo con template_id = 22 para ArticleHistory
          historyArticle.value = articlesService.getFirstByTemplate(response.data, 22)
          console.log('Artículo history encontrado:', historyArticle.value)
          
          // Buscar artículo con template_id = 23 para EventsSection
          eventsArticle.value = articlesService.getFirstByTemplate(response.data, 23)
          console.log('Artículo events encontrado:', eventsArticle.value)
          
          // Buscar artículo con template_id = 24 para ArticleImageRight
          imageRightArticle.value = articlesService.getFirstByTemplate(response.data, 24)
          console.log('Artículo imageRight encontrado:', imageRightArticle.value)
          
          // Buscar artículo con template_id = 25 para ArticleImageLeft
          imageLeftArticle.value = articlesService.getFirstByTemplate(response.data, 25)
          console.log('Artículo imageLeft encontrado:', imageLeftArticle.value)
          
          // Buscar artículo con template_id = 27 para NewsletterWeatherSection
          newsletterArticle.value = articlesService.getFirstByTemplate(response.data, 27)
          console.log('Artículo newsletter encontrado:', newsletterArticle.value)
          
          // Extraer datos del artículo newsletter
          if (newsletterArticle.value) {
            newsletterTitle.value = newsletterArticle.value.title || ''
            // Puedes extraer más datos del contenido del artículo
            newsletterVisitorDesc.value = newsletterArticle.value.subtitle || ''
            newsletterBusinessDesc.value = newsletterArticle.value.description || ''
            newsletterNomadDesc.value = newsletterArticle.value.content || ''
          }
          
          console.log('Home articles loaded:', {
            total: response.data.length,
            hero: heroArticle.value?.title || 'No encontrado',
            heroTemplateId: heroArticle.value?.template_id,
            interests: interestsArticle.value?.title || 'No encontrado',
            interestsTemplateId: interestsArticle.value?.template_id,
            history: historyArticle.value?.title || 'No encontrado',
            historyTemplateId: historyArticle.value?.template_id,
            events: eventsArticle.value?.title || 'No encontrado',
            eventsTemplateId: eventsArticle.value?.template_id,
            imageRight: imageRightArticle.value?.title || 'No encontrado',
            imageRightTemplateId: imageRightArticle.value?.template_id,
            imageLeft: imageLeftArticle.value?.title || 'No encontrado',
            imageLeftTemplateId: imageLeftArticle.value?.template_id
          })
        } else {
          console.error('Respuesta de API sin éxito:', response)
          throw new Error('Error en la respuesta de la API')
        }
      } catch (err) {
        console.error('Error cargando artículos de home:', err)
        articlesError.value = 'Error al cargar los artículos'
        homeArticles.value = []
        heroArticle.value = null
        interestsArticle.value = null
        historyArticle.value = null
        eventsArticle.value = null
        imageLeftArticle.value = null
        imageRightArticle.value = null
        newsletterArticle.value = null
      } finally {
        articlesLoading.value = false
        console.log('Estado final:', {
          articlesLoading: articlesLoading.value,
          articlesError: articlesError.value,
          heroArticle: heroArticle.value,
          interestsArticle: interestsArticle.value,
          historyArticle: historyArticle.value,
          eventsArticle: eventsArticle.value,
          imageLeftArticle: imageLeftArticle.value,
          imageRightArticle: imageRightArticle.value
        })
      }
    }

    const mapEventToCard = (event) => {
      // Procesar la imagen con $buildImg
      const processedImage = $buildImg ? $buildImg(event.image) : event.image || ''
      
      return{
        title: event.title,
        subtitle: event.date,
        description: event.location,
        cover: processedImage,
        badge: 'Evento',
        to: { name: 'GastronomyDetail', params: { id: event.id } },
        icon: 'far fa-calendar-alt'
      }
      
    }

//     const mapEventToCard = (event) => {
//   // Procesar la imagen con $buildImg
//   const processedImage = $buildImg ? $buildImg(event.image) : event.image || ''
  
//   return {
//     title: event.title,
//     subtitle: event.date,
//     description: event.location,
//     cover: processedImage, // ✅ Ahora es una URL completa
//     badge: 'Evento',
//     to: { name: 'GastronomyDetail', params: { id: event.id } },
//     icon: 'far fa-calendar-alt'
//   }
// }

    // 360° Viewer functions
    // Removed legacy Pannellum viewer logic

    onMounted(async () => {
      // Inicializar museos estáticos
      fetchMuseums()
      
      // Cargar datos de API
      await Promise.all([
        fetchEvents(),
        fetchHomeArticles(),
        fetchWeatherData()
      ])
    })

    return {
      // Eventos
      upcomingEvents,
      eventsLoading,
      eventsError,
      fetchEvents,
      mapEventToCard,
      
      // Artículos
      homeArticles,
      articlesLoading,
      articlesError,
      fetchHomeArticles,
      heroArticle,
      interestsArticle,
      historyArticle,
      eventsArticle,
      imageLeftArticle,
      imageRightArticle,
      newsletterArticle,
      newsletterTitle,
      newsletterVisitorDesc,
      newsletterBusinessDesc,
      newsletterNomadDesc,
      weatherTemp,
      weatherCondition,
      weatherTime,
      weatherTz,
      weatherSource,
      weatherIcon,
      weatherLoaded,
      
      // 360° Viewer (Momento360)
      momentoSrc,
      momento360Tours,
      activeTourIndex,
      loadTour,
      museums,
      museumsLoading,
      museumsError
    }
  }
}
</script>

<style scoped>
.home-page {
  background: var(--bg-primary);
}

/* Map embed styles */
.map-embed-section {
  padding: 3rem 0 2rem;
  background: var(--bg-primary);
}

.map-embed-title {
  font-size: 2rem;
  font-weight: 400;
  color: var(--text-primary);
  letter-spacing: -0.01em;
  margin: 0 0 0.25rem 0;
}

.map-embed-subtitle {
  color: var(--text-secondary);
  margin: 0 0 1.25rem 0;
}

.map-embed-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  background: var(--light-gray);
  border: 1px solid var(--border-light);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.map-embed-wrapper:hover {
  transform: translateY(-2px);
  box-shadow: 0 16px 40px rgba(0,0,0,0.10);
}

.map-embed {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

@media (max-width: 768px) {
  .map-embed-title { font-size: 1.5rem; }
  .map-embed-section { padding: 2rem 0 1rem; }
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

/* 360° Viewer Styles */
.viewer-360-section {
  padding: 4rem 0;
  background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
}

.viewer-360-header {
  text-align: center;
  margin-bottom: 3rem;
}

.viewer-360-title {
  font-size: 2.5rem;
  font-weight: 300;
  color: var(--text-primary);
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}

.viewer-360-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}

.viewer-360-wrapper {
  position: relative;
  width: 100%;
  max-width: 900px;
  margin: 0 auto 2rem;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.viewer-360-wrapper:hover {
  transform: translateY(-4px);
  box-shadow: 0 25px 80px rgba(0,0,0,0.2);
}

.pannellum-container {
  width: 100%;
  height: 500px;
  border-radius: 16px;
  overflow: hidden;
}

.viewer-360-museums {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-top: 3rem;
}

.museum-card {
  background: white;
  border: 2px solid var(--border-light, #e0e0e0);
  border-radius: 16px;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  min-height: 180px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.museum-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--border-light) 0%, var(--border-color) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.museum-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.museum-card:hover::before {
  opacity: 1;
}

.museum-card.active {
  border-color: var(--primary-blue, #007bff);
  background: linear-gradient(135deg, rgba(0, 123, 255, 0.05) 0%, rgba(0, 123, 255, 0.02) 100%);
  box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2);
}

.museum-card.active::before {
  transform: scaleX(1);
}

.museum-card-image {
  width: 100%;
  height: 260px;
  border-radius: 24px;
  overflow: hidden;
  margin-bottom: 1rem;
  position: relative;
  background: var(--light-gray);
  transition: border-radius 300ms ease;
}

.museum-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scale(1);
  transition: transform 400ms ease;
  will-change: transform;
}

.museum-card:hover .museum-card-image img { transform: scale(1.05); }

.museum-card:hover .museum-card-image {
  border-radius: 24px 50px 24px 24px;
}

.museum-card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(0deg, rgba(0,0,0,0.1), rgba(0,0,0,0.0));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.museum-card:hover .museum-card-overlay {
  opacity: 1;
}

.museum-card-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-blue, #007bff), var(--primary-blue, #0056b3));
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.museum-card-icon i {
  font-size: 1.5rem;
  color: white;
  opacity: 0.8;
  transition: color 0.3s ease, opacity 0.3s ease;
}

.museum-card:hover .museum-card-icon {
  transform: scale(1.1) rotate(5deg);
}

.museum-card:hover .museum-card-icon i {
  opacity: 1;
}

.museum-card.active .museum-card-icon {
  box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
}

.museum-card-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.museum-card-title {
  font-size: 1.5rem;
  font-weight: 400;
  color: var(--text-primary, #333);
  margin: 1rem 0 0.5rem;
  letter-spacing: -0.02em;
  line-height: 1.25;
  transition: color 0.3s ease;
}

.museum-card.active .museum-card-title {
  color: var(--primary-blue, #007bff);
}

.museum-card:hover .museum-card-title {
  color: var(--primary-blue, #007bff);
}

.museum-card-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary, #666);
  margin: 0 0 0.5rem 0;
}

.museum-card-description {
  font-size: 0.8125rem;
  color: var(--text-secondary, #999);
  margin: 0.5rem 0 0 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.museum-card-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--primary-blue, #007bff);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.75rem;
  box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.museums-loading,
.museums-error {
  text-align: center;
  padding: 3rem 0;
  color: var(--text-secondary, #666);
}

.museums-error .text-danger {
  color: var(--danger, #dc3545);
}

/* Responsive */
@media (max-width: 768px) {
  .viewer-360-section {
    padding: 2rem 0;
  }
  
  .viewer-360-title {
    font-size: 2rem;
  }
  
  .viewer-360-subtitle {
    font-size: 1rem;
  }
  
  .pannellum-container {
    height: 300px;
  }
  
  .viewer-360-museums {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .museum-card {
    min-height: 200px;
    padding: 1.25rem;
  }
  
  .museum-card-image {
    height: 120px;
    margin-bottom: 0.75rem;
  }
  
  .museum-card-icon {
    width: 50px;
    height: 50px;
    margin-bottom: 0.75rem;
  }
  
  .museum-card-icon i {
    font-size: 1.25rem;
  }
  
  .museum-card-title {
    font-size: 1rem;
  }
  
  .museum-card-description {
    font-size: 0.75rem;
    -webkit-line-clamp: 2;
  }
  
  @media (max-width: 480px) {
    .viewer-360-museums {
      grid-template-columns: 1fr;
    }
  }
}
</style> 