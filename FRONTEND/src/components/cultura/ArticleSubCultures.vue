<template>
  <InterestsSection
    :bare="false"
    :items="renderedCultures"
    :map-item-to-card="mapCultureToInterestCard"
    :on-item-click="handleItemClick"
    :section-title="sectionTitle"
    :section-subtitle="sectionSubtitle"
    display-mode="carousel"
  />
</template>

<script>
import InterestsSection from '@/components/home/InterestsSection.vue'

export default {
  name: 'ArticleSubCultures',
  components: {
    InterestsSection
  },
  props: {
    // artículo con template_id = 7 para título/contenido
    article: {
      type: Object,
      required: false,
      default: null
    },
    // Opción 1: pasar subcategorías directamente
    subcategories: {
      type: Array,
      required: false,
      default: null
    },
    // Opción 2: pasar artículos que contienen subcategorías
    articles: {
      type: Array,
      required: false,
      default: null
    }
  },
  data() {
    return {
      
    }
  },
  computed: {
    // Título y subtítulo de la sección
    sectionTitle() {
      return this.article?.title || 'Descubre culturas regionales únicas'
    },
    sectionSubtitle() {
      return this.article?.content || 'A pesar del tamaño compacto de Bolivia, existen varias regiones donde diferentes idiomas, costumbres y tradiciones se han transmitido a través de generaciones.'
    },
    // Normaliza subcategorías desde props
    normalizedFromSubcategories() {
      console.log('Subcategories prop:', this.subcategories)
      if (!Array.isArray(this.subcategories) || this.subcategories.length === 0) return []
      return this.subcategories.map((s) => ({
        id: s?.id,
        name: s?.name || 'Subcategoría',
        description: s?.description || '',
        image: s?.image_url || 'https://via.placeholder.com/640x360?text=Subcategor%C3%ADa',
        label: s?.name || 'Subcategoría'
      }))
    },
    // Extrae y aplana subcategorías desde artículos
    normalizedFromArticles() {
      console.log('Articles prop:', this.articles)
      if (!Array.isArray(this.articles) || this.articles.length === 0) return []
      const collected = []
      this.articles.forEach((a) => {
        const subs = Array.isArray(a?.subcategories) ? a.subcategories : []
        subs.forEach((s) => {
          collected.push({
            id: s?.id,
            name: s?.name || 'Subcategoría',
            description: s?.description || '',
            image: s?.image_url || 'https://via.placeholder.com/640x360?text=Subcategor%C3%ADa',
            label: s?.name || 'Subcategoría'
          })
        })
      })
      // Opcional: eliminar duplicados por nombre
      const seen = new Set()
      const result = collected.filter((c) => {
        const key = c.name
        if (seen.has(key)) return false
        seen.add(key)
        return true
      })
      console.log('Normalized from articles:', result)
      return result
    },
    // Lista final a renderizar
    renderedCultures() {
      const result = this.normalizedFromSubcategories.length > 0 ? this.normalizedFromSubcategories : 
                   this.normalizedFromArticles.length > 0 ? this.normalizedFromArticles : 
                   this.regionalCultures
      console.log('Rendered cultures:', result)
      return result
    }
  },
  methods: {
    // Mapea una cultura/subcategoría al formato de InterestCard
    mapCultureToInterestCard(culture) {
      return {
        title: culture.name,
        description: culture.description,
        cover: culture.image,
        badge: 'Subcategoría',
        to: null // Usaremos onItemClick para navegación personalizada
      }
    },
    
    handleItemClick(culture) {
      // Navegar a CulturaPage con subcultura, incluyendo subId como query para cargar artículos
      const baseSlug = this.$route.params.slug
      const subslug = this.slugify(culture?.name)
      const subId = culture?.id
      const path = `/que-hacer/${baseSlug}/subcultura/${subslug}`
      if (subId) {
        this.$router.push({ path, query: { subId: String(subId) } })
      } else {
        this.$router.push(path)
      }
      console.log('subcultura ',culture);
    },
    
    slugify(name) {
      if (!name) return ''
      return String(name)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim()
    }
  }
}
</script>

<style scoped>
/* Componente simplificado - usa los estilos de InterestsSection */
</style>
