<template>
  <div class="what-to-do">
    <!-- Loading state para artículos -->
    <div v-if="articlesLoading" class="text-center py-5">
      <div class="loading-spinner mb-2"></div>
      <p>Cargando contenido...</p>
    </div>
    
    <!-- Error state para artículos -->
    <div v-else-if="articlesError" class="text-center py-5">
      <p class="text-danger mb-3">{{ articlesError }}</p>
      <button class="btn btn-primary" @click="fetchWhatToDoArticles">Reintentar</button>
    </div>
    
    <!-- Contenido principal -->
    <div v-else>
      <HeroWhatToDoSection :title="heroTitle" :subtitle="heroSubtitle" :mediaUrl="heroMediaUrl" />

    <template v-if="combinedSections && combinedSections.length">
      <template v-for="(sec, idx) in combinedSections" :key="sec.article?.id || 'sec-' + idx">
        <ArticleBasic v-if="sec.type === 'basic'" :article="sec.article" />
        <ArticleHistory v-else-if="sec.type === 'history'" :article="sec.article" />
      </template>
    </template>

    <!-- Secciones de imágenes intercaladas (template_id 24 y 25) -->
    <template v-if="imageSections && imageSections.length">
      <template v-for="(sec, idx) in imageSections" :key="sec.article?.id || 'img-sec-' + idx">
        <ArticleImageLeft v-if="sec.type === 'imageLeft'" :article="sec.article" />
        <ArticleImageRight v-else-if="sec.type === 'imageRight'" :article="sec.article" />
      </template>
    </template>

    <!-- ArticleGrid dinámico con artículos de template_id 22 -->
    <ArticleGrid
      v-if="gridArticles22.length"
      :articles="gridArticles22"
      :title="gridTitle22"
      :subtitle="gridSubtitle22"
    />
    
    <!-- Fallback si no hay artículos dinámicos -->
    <ArticleGrid
      v-else
      :articles="adventureTop3"
      :title="adventureTitle"
      :subtitle="adventureSubtitle"
    />


    



<!-- 
    <IntroWhatToDoSection /> -->
    <ArticleGrid :articles="adventureTop3" :title="adventureTitle" :subtitle="adventureSubtitle" />
    <SustainableWhatToDoSection />
    <!-- <ArticleHistory/> -->
    <!-- <ItinerariesWhatToDoSection /> -->
    <ArticleGrid :articles="gastronomyTop3" :title="gastronomyTitle" :subtitle="gastronomySubtitle" />
    <IntroWhatToDoSection />



    
    

    <!-- <IntroWhatToDoSection />

     <ArticleHistory/> -->

    <PlanningSeasons/>

    <!-- <FoodWhatToDoSection />

    
    <FAQWhatToDoSection /> -->
    <!-- <CTAWhatToDoSection /> -->
    <InterestsSection/>
    
      <ScrollToTop />
    </div>
  </div>
</template>

<script>
import ScrollToTop from '@/components/ScrollToTop.vue'
import HeroWhatToDoSection from '@/components/whattodo/HeroWhatToDoSection.vue'
import ArticleBasic from '@/components/cultura/ArticleBasic.vue'
import ArticleImageLeft from '@/components/cultura/ArticleImageLeft.vue'
import ArticleImageRight from '@/components/cultura/ArticleImageRight.vue'

import IntroWhatToDoSection from '@/components/whattodo/IntroWhatToDoSection.vue'
import ArticleGrid from '@/components/cultura/ArticleGrid.vue'
import SustainableWhatToDoSection from '@/components/whattodo/SustainableWhatToDoSection.vue'
import ArticleHistory from '@/components/cultura/ArticleHistory.vue'

import ItinerariesWhatToDoSection from '@/components/whattodo/ItinerariesWhatToDoSection.vue'
import FoodWhatToDoSection from '@/components/whattodo/FoodWhatToDoSection.vue'
import FAQWhatToDoSection from '@/components/whattodo/FAQWhatToDoSection.vue'
import CTAWhatToDoSection from '@/components/whattodo/CTAWhatToDoSection.vue'
import PlanningSeasons from '@/components/planning/PlanningSeasons.vue'


import InterestsSection from '@/components/home/InterestsSection.vue'
import { categoriesService } from '@/services/categories.service'
import { articlesService } from '@/services/articles.service'

export default {
  name: 'WhatToDoPage',
  components: {
    ScrollToTop,
    HeroWhatToDoSection,
    ArticleBasic,
    ArticleImageLeft,
    ArticleImageRight,
    IntroWhatToDoSection,
    ArticleGrid,
    SustainableWhatToDoSection,
    ArticleHistory,
    ItinerariesWhatToDoSection,
    FoodWhatToDoSection,
    FAQWhatToDoSection,
    PlanningSeasons,
    InterestsSection,
    CTAWhatToDoSection
  },
  data() {
    return {
      // Loading states
      articlesLoading: false,
      articlesError: '',
      
      // Hero dinámico
      heroTitle: '',
      heroSubtitle: '',
      heroMediaUrl: '',
      // Articles template 21
      articleBasics: [],
      // Articles template 23
      historyArticles23: [],
      // Intercalados 21/23
      combinedSections: [],
      // Articles template 22 -> ArticleGrid
      gridArticles22: [],
      gridTitle22: '',
      gridSubtitle22: '',
      // Articles template 24 -> ArticleImageLeft
      imageLeftArticles24: [],
      // Articles template 25 -> ArticleImageRight
      imageRightArticles25: [],
      // Intercalados 24/25
      imageSections: [],
      adventureTop3: [],
      adventureTitle: 'A un paso de la aventura',
      adventureSubtitle: '',
      gastronomyTop3: [],
      gastronomyTitle: 'Gastronomía',
      gastronomySubtitle: ''
    }
  },
  async created() {
    await this.fetchWhatToDoArticles()
  },
  computed: {
    // Intercalar todos los componentes (excepto Hero que siempre va arriba)
    interleavedComponents() {
      const components = []
      
      // 1. ArticleBasic y ArticleHistory intercalados (21 y 23)
      if (this.combinedSections && this.combinedSections.length) {
        this.combinedSections.forEach((sec, idx) => {
          components.push({
            type: sec.type,
            component: sec.type === 'basic' ? 'ArticleBasic' : 'ArticleHistory',
            props: { article: sec.article },
            key: `combined-${idx}`
          })
        })
      }
      
      // 2. ArticleImageLeft y ArticleImageRight intercalados (24 y 25)
      if (this.imageSections && this.imageSections.length) {
        this.imageSections.forEach((sec, idx) => {
          components.push({
            type: sec.type,
            component: sec.type === 'imageLeft' ? 'ArticleImageLeft' : 'ArticleImageRight',
            props: { article: sec.article },
            key: `image-${idx}`
          })
        })
      }
      
      // 3. ArticleGrid dinámico (22)
      if (this.gridArticles22 && this.gridArticles22.length) {
        components.push({
          type: 'grid',
          component: 'ArticleGrid',
          props: {
            articles: this.gridArticles22,
            title: this.gridTitle22,
            subtitle: this.gridSubtitle22
          },
          key: 'grid-dynamic'
        })
      }
      
      // 4. ArticleGrid Aventura
      if (this.adventureTop3 && this.adventureTop3.length) {
        components.push({
          type: 'grid',
          component: 'ArticleGrid',
          props: {
            articles: this.adventureTop3,
            title: this.adventureTitle,
            subtitle: this.adventureSubtitle
          },
          key: 'grid-adventure'
        })
      }
      
      // 5. SustainableWhatToDoSection
      components.push({
        type: 'static',
        component: 'SustainableWhatToDoSection',
        props: {},
        key: 'sustainable'
      })
      
      // 6. ArticleGrid Gastronomía
      if (this.gastronomyTop3 && this.gastronomyTop3.length) {
        components.push({
          type: 'grid',
          component: 'ArticleGrid',
          props: {
            articles: this.gastronomyTop3,
            title: this.gastronomyTitle,
            subtitle: this.gastronomySubtitle
          },
          key: 'grid-gastronomy'
        })
      }
      
      // 7. IntroWhatToDoSection
      components.push({
        type: 'static',
        component: 'IntroWhatToDoSection',
        props: {},
        key: 'intro'
      })
      
      // 8. PlanningSeasons
      components.push({
        type: 'static',
        component: 'PlanningSeasons',
        props: {},
        key: 'planning-seasons'
      })
      
      // 9. InterestsSection
      components.push({
        type: 'static',
        component: 'InterestsSection',
        props: {},
        key: 'interests'
      })
      
      return components
    }
  },
  methods: {
    // Helper para normalizar template_id (puede venir como string o número)
    normalizeTemplateId(templateId) {
      if (typeof templateId === 'string') {
        return parseInt(templateId, 10)
      }
      return templateId
    },
    
    // Helper para comparar template_id
    matchesTemplate(article, templateId) {
      const articleTemplateId = this.normalizeTemplateId(article.template_id)
      return articleTemplateId === templateId
    },
    
    async fetchWhatToDoArticles() {
      try {
        this.articlesLoading = true
        this.articlesError = ''
        
        // Cargar artículos de la sección what_to_do y tomar el template_id 20 para el hero
        const articlesResp = await articlesService.getArticlesByPageSection('what_to_do')
        console.log('📄 WhatToDo: Respuesta completa de artículos:', articlesResp)
      if (articlesResp?.success && Array.isArray(articlesResp.data)) {
        console.log('📊 WhatToDo: Total de artículos cargados:', articlesResp.data.length)
        console.log('📋 WhatToDo: Artículos por template_id:', {
          template20: articlesResp.data.filter(a => this.matchesTemplate(a, 20)).length,
          template21: articlesResp.data.filter(a => this.matchesTemplate(a, 21)).length,
          template22: articlesResp.data.filter(a => this.matchesTemplate(a, 22)).length,
          template23: articlesResp.data.filter(a => this.matchesTemplate(a, 23)).length,
          template24: articlesResp.data.filter(a => this.matchesTemplate(a, 24)).length,
          template25: articlesResp.data.filter(a => this.matchesTemplate(a, 25)).length
        })
        const hero = articlesResp.data.find(a => this.matchesTemplate(a, 20))
        if (hero) {
          console.log('✅ WhatToDo: Hero encontrado (template_id 20):', hero)
          this.heroTitle = hero.title || ''
          // Usamos content como subtítulo si viene nulo subtitle
          this.heroSubtitle = hero.subtitle || hero.content || ''
          
          // Procesar media_url
          let processedMediaUrl = hero.media_url || '/videos/conocebolivia1080X192.mp4'
          
          // Si media_url existe y es una ruta relativa (no empieza con / ni http), procesarla con $buildImg
          if (hero.media_url && !hero.media_url.startsWith('/') && !hero.media_url.startsWith('http')) {
            // Es una URL relativa de la API, necesita $buildImg
            processedMediaUrl = this.$buildImg ? this.$buildImg(hero.media_url) : hero.media_url
          } else if (hero.media_url && hero.media_url.startsWith('/')) {
            // Es una ruta absoluta del sitio (ej: /videos/...), usarla directamente
            processedMediaUrl = hero.media_url
          } else if (hero.media_url && hero.media_url.startsWith('http')) {
            // Es una URL absoluta externa, usarla directamente
            processedMediaUrl = hero.media_url
          }
          
          // Si media_url es una imagen externa (no es video), usar el video local
          if (hero.media_url && hero.media_url.startsWith('http') && 
              !hero.media_url.includes('/videos/') && 
              !hero.media_url.endsWith('.mp4') && 
              !hero.media_url.includes('video')) {
            processedMediaUrl = '/videos/conocebolivia1080X192.mp4'
          }
          
          this.heroMediaUrl = processedMediaUrl
          console.log('🎥 WhatToDo: Media URL procesada:', {
            original: hero.media_url,
            processed: this.heroMediaUrl,
            hasBuildImg: !!this.$buildImg,
            isVideo: this.heroMediaUrl && (this.heroMediaUrl.includes('.mp4') || this.heroMediaUrl.includes('/videos/'))
          })
        } else {
          console.warn('⚠️ WhatToDo: No se encontró ningún artículo con template_id = 20 para el hero')
          // Fallback si no hay hero
          this.heroMediaUrl = '/videos/conocebolivia1080X192.mp4'
        }

        // Cargar artículos para ArticleBasic (template_id = 21)
        const basics = articlesResp.data
          .filter(a => this.matchesTemplate(a, 21))
          .sort(() => Math.random() - 0.5)
        this.articleBasics = basics
        console.log('✅ WhatToDo: Artículos template_id 21 (ArticleBasic):', basics.length)

        // Cargar artículos para ArticleGrid (template_id = 22) desde what_to_do
        const gridArticles22 = articlesResp.data.filter(a => this.matchesTemplate(a, 22))
        console.log('📋 WhatToDo: Artículos template_id 22 (ArticleGrid) desde what_to_do:', gridArticles22.length)
        
        // Intentar también cargar desde what_to_do_grid si no hay suficientes
        if (gridArticles22.length === 0) {
          try {
            const gridResp = await articlesService.getArticlesByPageSection('what_to_do_grid')
            console.log('📋 WhatToDo: Respuesta de artículos what_to_do_grid:', gridResp)
            
            if (gridResp?.success && Array.isArray(gridResp.data)) {
              this.gridArticles22 = gridResp.data
              this.gridTitle22 = gridResp.data[0]?.title || 'Aventuras y experiencias'
              this.gridSubtitle22 = gridResp.data[0]?.subtitle || gridResp.data[0]?.description || 'Descubre las mejores actividades y lugares para explorar en Bolivia'
              console.log('✅ WhatToDo: ArticleGrid configurado desde what_to_do_grid:', {
                title: this.gridTitle22,
                subtitle: this.gridSubtitle22,
                articlesCount: this.gridArticles22.length
              })
              // Verificar que los artículos tengan el campo 'link' desde la API
              const articlesWithLink = this.gridArticles22.filter(a => a.link)
              console.log('🔗 WhatToDo: Artículos con link desde la API (what_to_do_grid):', articlesWithLink.length, 'de', this.gridArticles22.length)
              if (articlesWithLink.length > 0) {
                console.log('📋 WhatToDo: Ejemplos de links encontrados:', articlesWithLink.slice(0, 3).map(a => ({ title: a.title, link: a.link })))
              }
            } else {
              console.log('⚠️ WhatToDo: No se encontraron artículos en what_to_do_grid')
              this.gridArticles22 = []
            }
          } catch (gridError) {
            console.error('❌ WhatToDo: Error cargando artículos what_to_do_grid:', gridError)
            this.gridArticles22 = []
          }
        } else {
          // Usar los artículos de template_id 22 encontrados en what_to_do
          this.gridArticles22 = gridArticles22
          this.gridTitle22 = gridArticles22[0]?.title || 'Aventuras y experiencias'
          this.gridSubtitle22 = gridArticles22[0]?.subtitle || gridArticles22[0]?.description || 'Descubre las mejores actividades y lugares para explorar en Bolivia'
          console.log('✅ WhatToDo: ArticleGrid configurado desde what_to_do (template_id 22):', {
            title: this.gridTitle22,
            subtitle: this.gridSubtitle22,
            articlesCount: this.gridArticles22.length
          })
          // Verificar que los artículos tengan el campo 'link' desde la API
          const articlesWithLink = this.gridArticles22.filter(a => a.link)
          console.log('🔗 WhatToDo: Artículos con link desde la API:', articlesWithLink.length, 'de', this.gridArticles22.length)
          if (articlesWithLink.length > 0) {
            console.log('📋 WhatToDo: Ejemplos de links encontrados:', articlesWithLink.slice(0, 3).map(a => ({ title: a.title, link: a.link })))
          }
        }

        // Cargar artículos para ArticleHistory (template_id = 23)
        this.historyArticles23 = articlesResp.data.filter(a => this.matchesTemplate(a, 23))
        console.log('✅ WhatToDo: Artículos template_id 23 (ArticleHistory):', this.historyArticles23.length)

        // Cargar artículos para ArticleImageLeft (template_id = 24)
        this.imageLeftArticles24 = articlesResp.data.filter(a => this.matchesTemplate(a, 24))
        console.log('🖼️ WhatToDo: Artículos template_id 24 (ArticleImageLeft):', this.imageLeftArticles24.length)

        // Cargar artículos para ArticleImageRight (template_id = 25)
        this.imageRightArticles25 = articlesResp.data.filter(a => this.matchesTemplate(a, 25))
        console.log('🖼️ WhatToDo: Artículos template_id 25 (ArticleImageRight):', this.imageRightArticles25.length)

        // Intercalar 21 y 23
        const interleaved = []
        const maxLen = Math.max(this.articleBasics.length, this.historyArticles23.length)
        for (let i = 0; i < maxLen; i++) {
          if (i < this.articleBasics.length) {
            interleaved.push({ type: 'basic', article: this.articleBasics[i] })
          }
          if (i < this.historyArticles23.length) {
            interleaved.push({ type: 'history', article: this.historyArticles23[i] })
          }
        }
        this.combinedSections = interleaved

        // Intercalar 24 y 25 (ArticleImageLeft y ArticleImageRight)
        const imageInterleaved = []
        const imageMaxLen = Math.max(this.imageLeftArticles24.length, this.imageRightArticles25.length)
        for (let i = 0; i < imageMaxLen; i++) {
          if (i < this.imageLeftArticles24.length) {
            imageInterleaved.push({ type: 'imageLeft', article: this.imageLeftArticles24[i] })
          }
          if (i < this.imageRightArticles25.length) {
            imageInterleaved.push({ type: 'imageRight', article: this.imageRightArticles25[i] })
          }
        }
        this.imageSections = imageInterleaved
        console.log('🔄 WhatToDo: Secciones de imágenes intercaladas:', this.imageSections)
      }

      // Categoria 3 = Aventura (endpoint fijo)
      const { data } = await categoriesService.getCategoryArticles(3)
      const list = Array.isArray(data) ? data.slice(0, 3) : []
      this.adventureTop3 = list
      // Título y descripción desde la categoría 3
      const cat = await categoriesService.getCategoryById(3)
      if (cat?.success && cat.data) {
        this.adventureTitle = cat.data.name || this.adventureTitle
        this.adventureSubtitle = cat.data.description || ''
      }

      // Categoria 4 = Gastronomía
      const { data: dataG } = await categoriesService.getCategoryArticles(4)
      const listG = Array.isArray(dataG) ? dataG.slice(0, 3) : []
      this.gastronomyTop3 = listG
      const catG = await categoriesService.getCategoryById(4)
      if (catG?.success && catG.data) {
        this.gastronomyTitle = catG.data.name || this.gastronomyTitle
        this.gastronomySubtitle = catG.data.description || ''
      }
      } catch (e) {
        console.error('Error cargando artículos de WhatToDo:', e)
        this.articlesError = 'Error al cargar los artículos'
        this.adventureTop3 = []
        this.gastronomyTop3 = []
      } finally {
        this.articlesLoading = false
      }
    }
  }
}
</script>

<style scoped>
.what-to-do { 
  background: var(--white); 
  color: var(--text-primary);
  font-family: var(--font-family-base) !important;
  position: relative;
}

/* Forzar fuente en todos los elementos excepto ScrollToTop */
.what-to-do > *:not(.scroll-to-top) {
  font-family: var(--font-family-base) !important;
}

/* Asegurar que ScrollToTop funcione correctamente */
.what-to-do :deep(.scroll-to-top) {
  position: fixed !important;
  z-index: 9999 !important;
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


