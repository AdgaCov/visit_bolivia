<template>
  <section class="cultura-hero-section">
    <div class="hero-background">
      <img 
        :src="finalImageUrl" 
        :alt="`${categoryData.name || 'Cultura'} Boliviana - Tradiciones y Folklore`" 
        class="hero-background-image"
      >
      <div class="photo-credit">
        <span>{{ categoryData.name || 'Cultura' }} - Bolivia Turismos</span>
      </div>
    </div>
    
    <!-- Overlay de texto con estilo Estonia -->
    <div class="hero-text-overlay">
      <div class="text-container">
        <h1 class="hero-main-title">{{ overrideTitle || categoryData.name || 'Una guía rápida a la cultura boliviana' }}</h1>
        <p class="hero-description">
          {{ overrideSubtitle || overrideDescription || categoryData.description || 'Bolivia es un país geográficamente compacto, pero el orgullo que los bolivianos tienen por su cultura desborda como los ríos durante las inundaciones de la quinta estación.' }}
        </p>
      </div>
    </div>
    
    <!-- Icono inferior izquierdo -->
    <div class="hero-icon" :style="{ backgroundColor: categoryData.color || '#8B5CF6' }">
      <i :class="categoryData.icon || 'fas fa-theater-masks'"></i>
    </div>
  </section>
</template>

<script>
import { ref, onMounted, watch, getCurrentInstance, computed } from 'vue'
import { useRoute } from 'vue-router'
import categoriesService from '@/services/categories.service'

export default {
  name: 'CulturaHeroSection',
  props: {
    categorySlug: {
      type: String,
      required: true
    },
    subSlug: {
      type: String,
      required: false,
      default: null
    },
    subcategories: {
      type: Array,
      required: false,
      default: () => []
    },
    articleData: {
      type: Object,
      required: false,
      default: null
    }
  },
  setup(props) {
    // const route = useRoute()
    const route = useRoute()
    const instance = getCurrentInstance()
    const $buildImg = instance?.appContext.config.globalProperties.$buildImg
    const categoryData = ref({
      name: '',
      description: '',
      image: '',
      icon: '',
      color: ''
    })

    // Overrides desde query (t=title, s=subtitle, d=description, img=image)
    const overrideTitle = ref('')
    const overrideSubtitle = ref('')
    const overrideDescription = ref('')
    const overrideImage = ref('')

    // ✅ Procesar la URL final de la imagen
    const finalImageUrl = computed(() => {
      const rawUrl = overrideImage.value || categoryData.value.image
      if ($buildImg && rawUrl) {
        return $buildImg(rawUrl)
      }
      // Fallback si no hay buildImg o URL
      return 'https://unifranz.edu.bo/wp-content/uploads/2023/02/IMG_20220507_141504-ph.-Sole-Patty-1.jpg'
    })

    const loadCategoryData = async (slug) => {
      try {
        // Leer datos desde query params para usar los datos del artículo
        const queryTitle = String(route.query.title || '')
        const querySubtitle = String(route.query.subtitle || '')
        const queryDescription = String(route.query.description || '')
        const queryMediaUrl = String(route.query.media_url || '')
        
        console.log('📋 CulturaHero: Query params:', {
          title: queryTitle,
          subtitle: querySubtitle,
          description: queryDescription,
          media_url: queryMediaUrl
        })
        
        // Si hay datos en la query, usarlos para el hero
        if (queryTitle) {
          console.log('🎯 CulturaHero: Usando datos desde query params')
          categoryData.value = {
            name: queryTitle,
            description: querySubtitle || queryDescription || 'Descripción del artículo',
            image: queryMediaUrl || 'https://unifranz.edu.bo/wp-content/uploads/2023/02/IMG_20220507_141504-ph.-Sole-Patty-1.jpg',
            icon: 'fas fa-file-alt',
            color: '#8B5CF6'
          }
          return
        }
        
        // Si hay datos de artículo específico, usarlos directamente
        if (props.articleData) {
          console.log('🎯 CulturaHero: Usando datos del artículo específico:', props.articleData)
          categoryData.value = {
            name: props.articleData.title || 'Artículo',
            description: props.articleData.subtitle || props.articleData.description || 'Descripción del artículo',
            image: props.articleData.media_url || (props.articleData.images && props.articleData.images[0]?.image_url) || 'https://unifranz.edu.bo/wp-content/uploads/2023/02/IMG_20220507_141504-ph.-Sole-Patty-1.jpg',
            icon: 'fas fa-file-alt',
            color: '#8B5CF6'
          }
          return
        }

        // Leer overrides si están presentes en la URL
        overrideTitle.value = String(route.query.t || '')
        overrideSubtitle.value = String(route.query.s || '')
        overrideDescription.value = String(route.query.d || '')
        overrideImage.value = String(route.query.img || '')

        const response = await categoriesService.getAllCategories()
        
        if (response.success && response.data) {
          // Buscar la categoría que coincida con el slug
          const category = response.data.find(cat => {
            const categorySlug = categoriesService.getCategorySlug(cat.name)
            return categorySlug === slug
          })
          
          if (category) {
            const transformedCategory = categoriesService.transformToInterestCard(category)
            categoryData.value = {
              ...transformedCategory,
              icon: categoriesService.getCategoryIcon(category.name),
              color: categoriesService.getCategoryColor(category.name)
            }
            // Si hay subSlug y subcategorías, preferir datos de subcultura
            if (props.subSlug && Array.isArray(props.subcategories)) {
              const match = props.subcategories.find(s => categoriesService.getCategorySlug(String(s?.name || '')) === props.subSlug)
              if (match) {
                categoryData.value = {
                  ...categoryData.value,
                  name: match.name || categoryData.value.name,
                  description: match.description || categoryData.value.description,
                  image: match.image_url || categoryData.value.image
                }
              }
            }
          } else {
            // Fallback si no se encuentra la categoría
            console.warn(`Categoría con slug "${slug}" no encontrada`)
            categoryData.value = {
              name: 'Categoría',
              description: 'Descripción no disponible',
              image: 'https://unifranz.edu.bo/wp-content/uploads/2023/02/IMG_20220507_141504-ph.-Sole-Patty-1.jpg',
              icon: 'fas fa-star',
              color: '#6B7280'
            }
          }
        }
      } catch (error) {
        console.error('Error loading category:', error)
        // Mantener valores por defecto si hay error
        categoryData.value = {
          name: 'Cultura',
          description: 'Bolivia es un país geográficamente compacto, pero el orgullo que los bolivianos tienen por su cultura desborda como los ríos durante las inundaciones de la quinta estación.',
          image: 'https://unifranz.edu.bo/wp-content/uploads/2023/02/IMG_20220507_141504-ph.-Sole-Patty-1.jpg',
          icon: 'fas fa-theater-masks',
          color: '#8B5CF6'
        }
      }
    }

    onMounted(() => {
      loadCategoryData(props.categorySlug)
    })

    // Observar cambios en el slug
    watch(() => props.categorySlug, (newSlug) => {
      loadCategoryData(newSlug)
    })
    // Reaccionar a cambios en query
    watch(() => route.query, () => {
      loadCategoryData(props.categorySlug)
    }, { deep: true })
    // Reaccionar a cambios de subSlug/subcategorías para actualizar el hero
    watch(() => [props.subSlug, props.subcategories], () => {
      loadCategoryData(props.categorySlug)
    }, { deep: true })
    
    // Reaccionar a cambios en articleData
    watch(() => props.articleData, () => {
      loadCategoryData(props.categorySlug)
    }, { deep: true })

    return {
      categoryData,
      overrideTitle,
      overrideSubtitle,
      overrideDescription,
      overrideImage,
      finalImageUrl
    }
  }
}
</script>

<style scoped>
.cultura-hero-section {
  position: relative;
  min-height: 80vh;
  display: flex;
  align-items: center;
  overflow: hidden;
  border-bottom: 1px solid var(--border-light);
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 70vh;
  z-index: 1;
}

.hero-background-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.photo-credit {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-size: 0.75rem;
  z-index: 3;
}

.hero-text-overlay {
  position: relative;
  z-index: 2;
  width: 100%;
  padding: 0 2rem;
  display: flex;
  top: 120px;
  justify-content: center;
}

.text-container {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 3rem 4rem;
  border-radius: 90px 90px 30px 30px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  max-width: 900px;
  width: 100%;
  text-align: center;
}

.hero-main-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  /* text-align: center; */
  margin-bottom: 3rem;
  letter-spacing: -0.01em;
  /* font-family: 'Courier New', Courier, monospace; */
}

.hero-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
  max-width: 800px;
  margin: 0 auto;
}

.hero-icon {
  position: absolute;
  bottom: 2rem;
  left: 2rem;
  width: 60px;
  height: 60px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-weight: bold;
  font-size: 1.25rem;
  z-index: 3;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.hero-icon i {
  font-size: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .cultura-hero-section {
    min-height: 70vh;
  }
  
  .hero-text-overlay {
    padding: 0;
  }
  
  .text-container {
    padding: 2rem 2.5rem 2rem 2.5rem;
    width: 100%;
    min-height: 25vh;
  }
  
  .hero-main-title {
    font-size: 1.75rem;
    margin-bottom: 0.75rem;
  }
  
  .hero-description {
    font-size: 0.95rem;
  }
  
  .hero-icon {
    width: 50px;
    height: 50px;
    font-size: 1rem;
    bottom: 1.5rem;
    left: 1.5rem;
  }
  
  .hero-icon i {
    font-size: 1.25rem;
  }
}

@media (max-width: 480px) {
  .cultura-hero-section {
    min-height: 60vh;
  }
  
  .hero-text-overlay {
    padding: 0;
  }
  
  .text-container {
    padding: 1.5rem 2rem 1.5rem 2rem;
    width: 100%;
    min-height: 20vh;
  }
  
  .hero-main-title {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }
  
  .hero-description {
    font-size: 0.9rem;
  }
  
  .hero-icon {
    width: 45px;
    height: 45px;
    font-size: 0.9rem;
    bottom: 1rem;
    left: 1rem;
  }
  
  .hero-icon i {
    font-size: 1rem;
  }
}
</style>




