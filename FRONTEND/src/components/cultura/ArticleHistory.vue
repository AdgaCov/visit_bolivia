<template>
  <section class="promo-section">
    <div class="container-fluid">
      <div class="promo-wrapper">
        <div class="promo-media" aria-hidden="true">
          <img :src="backgroundImage" :alt="backgroundAlt" class="promo-image">
        </div>
        <div class="promo-card">
          <h3 class="promo-title">{{ article?.title || 'Historia' }}</h3>
          <p v-if="article?.content" class="promo-text">{{ article.content }}</p>
          <p v-else-if="article?.subtitle" class="promo-text">{{ article.subtitle }}</p>
          <div class="promo-actions">
            <router-link 
              v-if="article?.link" 
              :to="buildLinkWithData(article.link)" 
              class="promo-btn promo-btn-secondary"
            >
              Qué hacer
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { RouterLink } from 'vue-router'
import { categoriesService } from '@/services/categories.service'

export default {
  name: 'HistorySection',
  components: {
    RouterLink
  },
  props: {
    article: {
      type: Object,
      required: false,
      default: null
    }
  },
  methods: {
    // buildLinkWithData(baseLink) {
    //   if (!baseLink) return '/que-hacer/cultura'

    //   // Extraer imagen del artículo
    //   const images = Array.isArray(this.article?.images) ? this.article.images : []
    //   const mainImage = images.find(img => Number(img?.is_main) === 1)
    //   const firstImage = images[0]
    //   const imageUrl = this.article?.media_url ||
    //                    mainImage?.image_url ||
    //                    firstImage?.image_url ||
    //                    ''

    //   // Si baseLink ya es objeto, agrega query directamente
    //   if (typeof baseLink === 'object') {
    //     return {
    //       ...baseLink,
    //       query: {
    //         ...(baseLink.query || {}),
    //         title: this.article?.title || '',
    //         subtitle: this.article?.subtitle || '',
    //         description: this.article?.content || this.article?.description || '',
    //         media_url: imageUrl
    //       }
    //     }
    //   }

    //   // Si es string, arma el objeto de ruta
    //   return {
    //     path: baseLink,
    //     query: {
    //       title: this.article?.title || '',
    //       subtitle: this.article?.subtitle || '',
    //       description: this.article?.content || this.article?.description || '',
    //       media_url: imageUrl
    //     }
    //   }
    // }
//   buildLinkWithData(baseLink) {
//   const articleTitle = this.article?.title || ''
//   const slug = categoriesService.getCategorySlug(articleTitle)
//   const imageUrl = this.getImageUrl()
  
//   // Pasar datos mínimos para el hero
//   const query = {}
//   if (this.article?.title) query.t = this.article.title
//   if (this.article?.subtitle) query.s = this.article.subtitle
//   if (this.article?.content || this.article?.description) {
//     query.d = this.article.content || this.article.description
//   }
//   if (imageUrl) query.img = imageUrl
  
//   return {
//     name: 'Cultura',
//     params: { slug: slug },
//     query: query
//   }
// },
buildLinkWithData(baseLink) {
  // Rutas que ya existen y no deben ir a /que-hacer/
  const existingRoutes = ['/buscar', '/mapa', '/eventos', '/gastronomia', '/planificacion', '/donde-ir', '/que-hacer']
  
  // Si el link es una ruta existente, usarla directamente
  if (baseLink && existingRoutes.some(route => baseLink === route || baseLink.startsWith(route + '/'))) {
    const query = this.getQueryParams()
    // Agregar articleId si existe para cargar artículos hijos
    if (this.article?.id) {
      query.articleId = this.article.id
      query.parentId = this.article.id // Usar el mismo ID como parentId para cargar hijos
    }
    return {
      path: baseLink,
      query: query
    }
  }
  
  // Si no, generar slug desde el título para ir a CulturaPage
  const articleTitle = this.article?.title || ''
  const slug = categoriesService.getCategorySlug(articleTitle)
  const imageUrl = this.getImageUrl()
  
  // Pasar datos mínimos para el hero
  const query = {}
  if (this.article?.title) query.t = this.article.title
  if (this.article?.subtitle) query.s = this.article.subtitle
  if (this.article?.content || this.article?.description) {
    query.d = this.article.content || this.article.description
  }
  if (imageUrl) query.img = imageUrl
  
  // Agregar articleId y parentId para cargar artículos hijos
  if (this.article?.id) {
    query.articleId = this.article.id
    query.parentId = this.article.id // Usar el mismo ID como parentId para cargar hijos
  }
  
  return {
    name: 'Cultura',
    params: { slug: slug },
    query: query
  }
},
  getImageUrl() {
    const images = Array.isArray(this.article?.images) ? this.article.images : []
    const mainImage = images.find(img => Number(img?.is_main) === 1)
    const firstImage = images[0]
    return this.article?.media_url ||
           mainImage?.image_url ||
           firstImage?.image_url ||
           ''
  },
  
  getQueryParams() {
    return {
      title: this.article?.title || '',
      subtitle: this.article?.subtitle || '',
      description: this.article?.content || this.article?.description || '',
      media_url: this.getImageUrl()
    }
  }
  },
  computed: {
    backgroundImage() {
      const images = Array.isArray(this.article?.images) ? this.article.images : []
      const main = images.find(img => Number(img?.is_main) === 1)
      const first = images[0]
      
      // Prioridad: media_url -> imagen principal -> primera imagen -> fallback
      let rawUrl = (
        this.article?.media_url ||
        main?.image_url ||
        first?.image_url ||
        'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/4.jpg'
      )
      
      // ✅ Procesar la URL con $buildImg
      return this.$buildImg ? this.$buildImg(rawUrl) : rawUrl
    },
    backgroundAlt() {
      const images = Array.isArray(this.article?.images) ? this.article.images : []
      const main = images.find(img => Number(img?.is_main) === 1)
      const first = images[0]
      return main?.alt_text || first?.alt_text || this.article?.title || 'Historia de Bolivia'
    },
    photoAuthor() {
      // Si en el futuro llega author de la foto, úsalo. Por ahora intenta usar author general
      return this.article?.author || null
    }
  }
}
</script>

<style scoped>
/* Promo */
.promo-section { 
  border-bottom: 1px solid var(--border-light);
}

/* Quitar gutter izquierdo del contenedor dentro de Promo */
.promo-section :deep(.container-fluid) {
  padding-left: 0;
}

.promo-wrapper { 
  position: relative; 
  border-radius: 0 16px 16px 0; 
  overflow: hidden;
  
  background: var(--white);
  border: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
  transition: border-radius 300ms ease;
}

.promo-wrapper:hover {
  border-radius: 0 50px 24px 16px;
}

.promo-media { 
  height: 520px; 
  position: relative;
  overflow: hidden;
}

.promo-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scale(1);
  transition: transform 400ms ease;
  will-change: transform;
}

.promo-wrapper:hover .promo-image {
  transform: scale(1.05);
}

.promo-card {
  position: absolute;
  right: 1rem;
  bottom: 1rem;
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 16px;
  padding: 1.5rem;
  max-width: 420px;
  box-shadow: var(--shadow-lg);
  backdrop-filter: blur(10px);
}

.promo-title { 
  font-size: 2rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.25;
  margin-bottom: 1rem;
  text-align: left;
}

.promo-text { 
  color: var(--text-secondary); 
  margin-bottom: 1rem;
  line-height: 1.6;
}

/* Botones promo minimalistas */
.promo-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.promo-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  letter-spacing: 0.025em;
  min-width: 140px;
}

.promo-btn-primary {
  background: var(--white);
  color: var(--text-primary);
  border-color: var(--white);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.promo-btn-primary:hover {
   background: var(--white);
  color: var(--text-primary);
  border-color: var(--white);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.promo-btn-secondary {
  background: var(--light-gray);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
}

.promo-btn-secondary:hover {
  background: var(--medium-gray);
  color: var(--text-primary);
  border-color: var(--border-color);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-radius: 50px 16px 16px 0; 
}

/* Responsive */
@media (max-width: 768px) {
  .promo-card { 
    position: static; 
    margin-top: -1.5rem;
    max-width: none;
    padding: 1.25rem;
  }
  
  .promo-title {
    text-align: center;
    font-size: 1.75rem;
  }
  
  .promo-text {
    font-size: 0.95rem;
  }
  
  .promo-actions {
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
  }
  
  .promo-btn {
    padding: 0.625rem 1.25rem;
    font-size: 0.85rem;
    min-width: 120px;
  }
}
</style>