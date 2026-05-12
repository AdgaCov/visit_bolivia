<template>
  <div class="cultura-page">
    <CulturaHeroSection
      :categorySlug="categorySlug"
      :subSlug="subSlug"
      :subcategories="heroSubcategories"
      :articleData="specificArticleData"
    />

    <component
      v-for="(article, index) in allArticles"
      :key="article.id"
      :is="getComponentForTemplate(article.template_id, index)"
      :article="article"
      :subcategories="heroSubcategories"
      :articles="allArticles"
    />

    <!-- Mostrar ArticleSubCultures siempre en la página de cultura -->
    <ArticleSubCultures
      :key="allArticles.length + '-' + heroSubcategories.length"
      :subcategories="heroSubcategories"
      :articles="allArticles"
    />

    <!-- <FeaturedPlacesSection :categoryId="currentCategoryId" /> -->
    <!-- <CulturalEventsSection /> -->
    <InterestsSection />
    <ScrollToTop />
  </div>
</template>

<script>
import { categoriesService } from '@/services/categories.service'
import CulturaHeroSection from '@/components/cultura/CulturaHeroSection.vue'
import ArticleBasic from '@/components/cultura/ArticleBasic.vue'
import CulturalRegionsSection from '@/components/cultura/CulturalRegionsSection.vue'
import ArticleHistory from '@/components/cultura/ArticleHistory.vue'


import ArticleImageLeft from '@/components/cultura/ArticleImageLeft.vue'
import ArticleImageRight from '@/components/cultura/ArticleImageRight.vue'
import ArticleSubCultures from '@/components/cultura/ArticleSubCultures.vue'


import RegionalCulturesSection from '@/components/cultura/RegionalCulturesSection.vue'
import FeaturedPlacesSection from '@/components/cultura/FeaturedPlacesSection.vue'
import CulturalEventsSection from '@/components/cultura/CulturalEventsSection.vue'
import ScrollToTop from '@/components/ScrollToTop.vue'
import InterestsSection from '@/components/home/InterestsSection.vue'
import ArticleGrid from '@/components/cultura/ArticleGrid.vue'
import ArticleSlider from '@/components/cultura/ArticleSlider.vue'

export default {
  name: 'CulturaPage',
  props: {
    slug: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      allArticles: [],
      loading: false,
      error: null,
      categorySubcategories: [],
      currentCategoryId: null,
      specificArticleData: null
    }
  },
  computed: {
    categorySlug() {
      return this.$route.params.slug || this.slug
    },
    subSlug() {
      // subcultura opcional desde la ruta
      return this.$route.params.subslug || null
    },
    heroSubcategories() {
      // Combina subcategorías desde artículos y desde la categoría
      const collected = []
      if (Array.isArray(this.allArticles)) {
        this.allArticles.forEach(a => {
          const subs = Array.isArray(a?.subcategories) ? a.subcategories : []
          subs.forEach(s => collected.push(s))
        })
      }
      if (Array.isArray(this.categorySubcategories) && this.categorySubcategories.length) {
        this.categorySubcategories.forEach(s => collected.push(s))
      }

      // Eliminar duplicados por id únicamente
      const seen = new Set()
      return collected.filter(s => {
        const key = s?.id
        if (!key) return false
        if (seen.has(key)) return false
        seen.add(key)
        return true
      })
    }
  },
  created() {
    this.fetchArticles()
  },
  watch: {
    // Reaccionar a cambios en el slug de la ruta
    '$route.params.slug': {
      handler(newSlug, oldSlug) {
        console.log('Route changed:', { newSlug, oldSlug, currentSlug: this.slug })
        if (newSlug) {
          this.fetchArticles()
        }
      },
      immediate: false
    },
    // Reaccionar a cambios en el query subId para cargar artículos de subcategoría
    '$route.query.subId': {
      handler() {
        this.fetchArticles()
      },
      immediate: false
    },
    // Reaccionar a cambios en articleId
    '$route.query.articleId': {
      handler() {
        console.log('🔄 CulturaPage: articleId cambió:', this.$route.query.articleId)
        this.fetchArticles()
      },
      immediate: false
    },
    // Reaccionar a cambios en parentId para cargar artículos hijos
    '$route.query.parentId': {
      handler() {
        console.log('🔄 CulturaPage: parentId cambió:', this.$route.query.parentId)
        this.fetchArticles()
      },
      immediate: false
    }
  },
  methods: {
    getComponentForTemplate(templateId, index) {
      // Mapeo de template_id a componentes
      const templateMap = {
        1: 'ArticleBasic',
        2: 'ArticleHistory',
        3: 'ArticleGrid',
        4: 'ArticleImageLeft', // Música
        5: 'ArticleImageRight', // Parques
        6: 'ArticleSlider',
        7: 'ArticleSubCultures'
      }
      
      // Para template_id 4 y 5, alternar entre ArticleImageLeft y ArticleImageRight
      if (templateId === 4 || templateId === 5) {
        return index % 2 === 0 ? 'ArticleImageLeft' : 'ArticleImageRight'
      }
      
      console.log('Template ID:', templateId, 'Component:', templateMap[templateId] || 'ArticleBasic')
      return templateMap[templateId] || 'ArticleBasic'
    },
    
    async fetchArticles() {
      try {
        this.loading = true
        this.error = null
        // Usar el slug de la ruta actual (más confiable que el prop)
        const currentSlug = this.$route.params.slug || this.slug
        console.log('fetchArticles called with slug:', currentSlug)
        
        // PRIORIDAD 1: Si hay parentId en query, cargar artículos hijos del artículo padre
        // Esto tiene prioridad sobre articleId porque queremos mostrar los hijos, no solo el padre
        const parentIdParam = this.$route.query.parentId
        const parentId = typeof parentIdParam === 'string' ? Number(parentIdParam) : Array.isArray(parentIdParam) ? Number(parentIdParam[0]) : null
        if (parentId && !Number.isNaN(parentId)) {
          console.log('Loading child articles for parentId:', parentId)
          try {
            const response = await categoriesService.getArticlesByParent(parentId)
            console.log('Child articles response:', response)
            if (response.success && response.data) {
              this.applyArticles(response.data)
              // Si hay un artículo padre en la respuesta, guardarlo como specificArticleData
              if (response.data.length > 0 && response.data[0].parent) {
                this.specificArticleData = response.data[0].parent
              } else {
                // Si no viene en la respuesta, cargar el artículo padre usando articleId si está disponible
                const articleIdParam = this.$route.query.articleId
                const articleId = typeof articleIdParam === 'string' ? Number(articleIdParam) : Array.isArray(articleIdParam) ? Number(articleIdParam[0]) : null
                if (articleId && !Number.isNaN(articleId)) {
                  try {
                    const parentResponse = await categoriesService.getSpecificArticle(articleId)
                    if (parentResponse.success && parentResponse.data) {
                      this.specificArticleData = parentResponse.data
                    }
                  } catch (err) {
                    console.warn('No se pudo cargar el artículo padre:', err)
                  }
                }
              }
            }
            return
          } catch (error) {
            console.error('Error loading child articles:', error)
            this.error = 'No se pudieron cargar los artículos hijos'
            return
          }
        }
        
        // PRIORIDAD 2: Si hay subId en query, priorizar artículos de subcategoría
        const subIdParam = this.$route.query.subId
        const subId = typeof subIdParam === 'string' ? Number(subIdParam) : Array.isArray(subIdParam) ? Number(subIdParam[0]) : null
        if (subId && !Number.isNaN(subId)) {
          console.log('Loading subcategory articles for subId:', subId)
          try {
            const response = await categoriesService.getSubcategoryArticles(subId)
            console.log('Subcategory articles response:', response)
            this.applyArticles(response.data)
            return
          } catch (error) {
            console.error('Error loading subcategory articles:', error)
            this.error = 'No se pudieron cargar los artículos de la subcategoría'
            return
          }
        }
        
        // PRIORIDAD 3: Si hay articleId en query (y NO hay parentId), cargar ese artículo específico
        const articleIdParam = this.$route.query.articleId
        const articleId = typeof articleIdParam === 'string' ? Number(articleIdParam) : Array.isArray(articleIdParam) ? Number(articleIdParam[0]) : null
        if (articleId && !Number.isNaN(articleId)) {
          console.log('Loading specific article for articleId:', articleId)
          try {
            const response = await categoriesService.getSpecificArticle(articleId)
            console.log('Specific article response:', response)
            if (response.success && response.data) {
              this.specificArticleData = response.data
              this.applyArticles([response.data])
            }
            return
          } catch (error) {
            console.error('Error loading specific article:', error)
            this.error = 'No se pudo cargar el artículo específico'
            return
          }
        }
        // Resolver dinámicamente el ID de categoría por slug o usar ID numérico si viene en la URL
        let categoryId = null
        const numericId = Number(currentSlug)
        if (!Number.isNaN(numericId)) {
          categoryId = numericId
        } else {
          try {
            const { data: categories } = await categoriesService.getAllCategories()
            const match = Array.isArray(categories)
              ? categories.find(c => categoriesService.getCategorySlug(c.name) === currentSlug)
              : null
            categoryId = match?.id ?? null
          } catch (e) {
            console.error('Error loading categories to resolve slug:', e)
            categoryId = null
          }
        }

        console.log('Category ID resolved:', categoryId)
        this.currentCategoryId = categoryId
        if (!categoryId) {
          this.error = 'No se pudo resolver la categoría para este slug'
          this.allArticles = []
          return
        }
        const [{ data }, categoryResp] = await Promise.all([
          categoriesService.getCategoryArticles(categoryId),
          categoriesService.getCategoryById(categoryId)
        ])
        this.applyArticles(data)
        // Guardar subcategorías de la categoría para el carrusel
        if (categoryResp?.success && Array.isArray(categoryResp.data?.subcategories)) {
          this.categorySubcategories = categoryResp.data.subcategories
        } else {
          this.categorySubcategories = []
        }
        // Si hay subslug, podríamos usarlo para filtrar o destacar en el Hero u otra sección
      } catch (e) {
        console.error('Error fetching articles:', e)
        this.error = 'No se pudieron cargar los artículos'
      } finally {
        this.loading = false
      }
    },
    
    applyArticles(data) {
      const articles = Array.isArray(data) ? data : []
      this.allArticles = articles
      console.log('Articles loaded:', articles.length, articles)
      console.log('Template IDs:', articles.map(a => ({ id: a.id, title: a.title, template_id: a.template_id })))
    }
  },
  components: {
    CulturaHeroSection,
    ArticleBasic,
    CulturalRegionsSection,
    ArticleHistory,
    ArticleGrid,
    ArticleSlider,
    ArticleImageLeft,
    ArticleImageRight,
    ArticleSubCultures,
    RegionalCulturesSection,
    FeaturedPlacesSection,
    CulturalEventsSection,
    ScrollToTop,
    InterestsSection,
  }
}
</script>

<style scoped>
.cultura-page {
  background: var(--bg-primary);
}
</style>


<!-- @CulturaPage.vue en CulturaPage.vue agregue de componente de InterestsSection, como esta en homePage.vue@HomePage.vue , pero desde  HomePage.vue me carga los componentes de cada categoria , pero como lo puse ahora tambien en @CulturaPage.vue , no me carga los componentes que pertenecen a esa categoria, entiendes solo de la primera que es cultura??? -->