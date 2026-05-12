<template>
  <section class="highlights section light">
    <div class="container">
      <div class="d-flex justify-content-between align-items-start mb-6">
        <div class="highlights-header">
          <h3 class="highlights-title">{{ title }}</h3>
          <p class="highlights-subtitle" v-if="subtitle">{{ subtitle }}</p>
        </div>
        <router-link 
          v-if="computedReadMoreLink && !computedReadMoreLink.startsWith('http')" 
          :to="computedReadMoreLink" 
          class="btn-read"
        >
          LEER MÁS
        </router-link>
        <a 
          v-else-if="computedReadMoreLink && computedReadMoreLink.startsWith('http')" 
          :href="computedReadMoreLink" 
          target="_blank" 
          rel="noopener noreferrer"
          class="btn-read"
        >
          LEER MÁS
        </a>
        <router-link 
          v-else 
          to="/lugares" 
          class="btn-read"
        >
          LEER MÁS
        </router-link>
      </div>
      <div class="row g-4 mt-4">
        <template v-if="hasDynamic">
          <div class="col-md-6" v-if="firstItem">
            <div class="adventure-card-large" @click="handleArticleClick(firstItem)">
              <div class="adventure-card-image-container">
                <div class="adventure-card-image" :style="{ backgroundImage: 'url(' + getImage(firstItem) + ')' }"></div>
                <div class="adventure-card-overlay">
                  <h4 class="adventure-card-title">{{ firstItem.title }}</h4>
                  <p class="adventure-card-text">{{ getText(firstItem) }}</p>
                  <button 
                    v-if="firstItem?.link" 
                    class="adventure-card-button" 
                    @click.stop="handleLinkClick(firstItem.link)"
                  >
                    VER MÁS INFORMACIÓN
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row g-4 h-100">
              <div class="col-12" v-if="secondItem">
                <div class="adventure-card-pill" @click="handleArticleClick(secondItem)">
                  <div class="adventure-card-pill-image-container">
                    <div class="adventure-card-pill-image" :style="{ backgroundImage: 'url(' + getImage(secondItem) + ')' }"></div>
                    <div class="adventure-card-pill-overlay">
                      <span class="adventure-card-pill-text">{{ secondItem.title }}</span>
                      <button 
                        v-if="secondItem?.link" 
                        class="adventure-card-pill-button" 
                        @click.stop="handleLinkClick(secondItem.link)"
                      >
                        VER MÁS
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12" v-if="thirdItem">
                <div class="adventure-card-pill" @click="handleArticleClick(thirdItem)">
                  <div class="adventure-card-pill-image-container">
                    <div class="adventure-card-pill-image" :style="{ backgroundImage: 'url(' + getImage(thirdItem) + ')' }"></div>
                    <div class="adventure-card-pill-overlay">
                      <span class="adventure-card-pill-text">{{ thirdItem.title }}</span>
                      <button 
                        v-if="thirdItem?.link" 
                        class="adventure-card-pill-button" 
                        @click.stop="handleLinkClick(thirdItem.link)"
                      >
                        VER MÁS
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Resto de artículos como pills en grilla balanceada -->
          <div class="col-12 col-md-6" v-for="(item, idx) in remainingItems" :key="item.id || 'rest-' + idx">
            <div class="adventure-card-pill" @click="handleArticleClick(item)">
              <div class="adventure-card-pill-image-container">
                <div class="adventure-card-pill-image" :style="{ backgroundImage: 'url(' + getImage(item) + ')' }"></div>
                <div class="adventure-card-pill-overlay">
                  <span class="adventure-card-pill-text">{{ item.title }}</span>
                  <button 
                    v-if="item?.link" 
                    class="adventure-card-pill-button" 
                    @click.stop="handleLinkClick(item.link)"
                  >
                    VER MÁS
                  </button>
                </div>
              </div>
            </div>
          </div>
        </template>
       
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HighlightsWhatToDoSection',
  props: {
    articles: {
      type: Array,
      default: () => []
    },
    title: {
      type: String,
      default: 'A un paso de la aventura'
    },
    subtitle: {
      type: String,
      default: 'Muévete a pie o en bote al ritmo del canto de las aves. Escapa de la rutina a un ritmo pausado. La naturaleza boliviana espera a los aventureros.'
    },
    readMoreLink: {
      type: String,
      default: null
    }
  },
  computed: {
    // Computed para obtener el link del botón "LEER MÁS"
    // Prioridad: 1) prop readMoreLink, 2) link del primer artículo, 3) fallback a /lugares
    computedReadMoreLink() {
      if (this.readMoreLink) {
        return this.readMoreLink
      }
      // Si no hay prop, usar el link del primer artículo si existe
      if (this.firstItem?.link) {
        return this.firstItem.link
      }
      return null // null activará el fallback a /lugares
    },
    hasDynamic() {
      return Array.isArray(this.articles) && this.articles.length > 0
    },
    firstItem() {
      return this.hasDynamic ? this.articles[0] : null
    },
    secondItem() {
      return this.hasDynamic ? this.articles[1] : null
    },
    thirdItem() {
      return this.hasDynamic ? this.articles[2] : null
    },
    remainingItems() {
      return this.hasDynamic && this.articles.length > 3 ? this.articles.slice(3) : []
    }
  },
  methods: {
    normalizeUrl(url) {
      if (!url || typeof url !== 'string') return ''
      // Si es absoluta, no modificar
      if (/^https?:\/\//i.test(url)) return url
      // Si es relativa, prefijar con VUE_APP_API_URL si está definida
      if (url.startsWith('/')) {
        const base = process.env.VUE_APP_API_URL || ''
        return base ? `${base.replace(/\/$/, '')}${url}` : url
      }
      return url
    },
    getImage(article) {
      if (!article) return ''

      const images = Array.isArray(article.images) ? article.images : []

      // 🔹 prioridad 1: media_url directo
      if (article.media_url) {
        return this.$buildImg ? this.$buildImg(article.media_url) : article.media_url
      }

      // 🔹 prioridad 2: imagen principal
      const mainImg = images.find(
        img => Number(img?.is_main) === 1 || img?.is_main === true || img?.is_main === '1'
      )
      if (mainImg?.image_url) {
        return this.$buildImg ? this.$buildImg(mainImg.image_url) : mainImg.image_url
      }

      // 🔹 prioridad 3: cualquier otra del array
      if (images.length > 0 && images[0].image_url) {
        return this.$buildImg ? this.$buildImg(images[0].image_url) : images[0].image_url
      }

      // 🔹 prioridad 4: otros campos comunes
      const candidates = [
        article.image_url,
        article.cover_image,
        article.cover,
        article.thumbnail,
        article.thumb,
        article.photo,
        article.picture
      ]
      const chosen = candidates.find(v => typeof v === 'string' && v.trim().length > 0)

      return this.$buildImg ? this.$buildImg(chosen || '/images/10.png') : (chosen || '/images/10.png')
    }
//     getImage(article) {
//   if (!article) return ''

//   const images = Array.isArray(article.images) ? article.images : []

//   // 🔹 prioridad 1: media_url directo
//   if (article.media_url) {
//     return this.normalizeUrl(article.media_url)
//   }

//   // 🔹 prioridad 2: imagen principal
//   const mainImg = images.find(
//     img => Number(img?.is_main) === 1 || img?.is_main === true || img?.is_main === '1'
//   )
//   if (mainImg?.image_url) {
//     return this.normalizeUrl(mainImg.image_url)
//   }

//   // 🔹 prioridad 3: cualquier otra del array
//   if (images.length > 0 && images[0].image_url) {
//     return this.normalizeUrl(images[0].image_url)
//   }

//   // 🔹 prioridad 4: otros campos comunes
//   const candidates = [
//     article.image_url,
//     article.cover_image,
//     article.cover,
//     article.thumbnail,
//     article.thumb,
//     article.photo,
//     article.picture
//   ]
//   const chosen = candidates.find(v => typeof v === 'string' && v.trim().length > 0)

//   return this.normalizeUrl(chosen) || '/images/10.png'
// }
    ,
    getText(article) {
      if (!article) return ''
      const base = article.description || article.subtitle || article.content || ''
      if (typeof base !== 'string') return ''
      const trimmed = base.trim()
      return trimmed.length > 180 ? trimmed.slice(0, 177) + '…' : trimmed
    },
    
    handleArticleClick(article) {
      if (!article) return
      
      console.log('🖱️ ArticleGrid: Click en artículo:', article)
      
      // Si el artículo tiene un link específico, usarlo
      if (article.link) {
        this.handleLinkClick(article.link)
        return
      }
      
      // Navegar a CulturaPage con el article_id para que muestre el contenido del artículo
      const route = {
        name: 'Cultura',
        params: { slug: 'cultura' }, // usar slug estándar de cultura
        query: {
          articleId: article.id,
          title: article.title,
          subtitle: article.subtitle || article.description,
          description: article.description || article.content,
          media_url: article.media_url || (article.images && article.images[0]?.image_url),
          categorySlug: 'cultura' // usar categoría por defecto
        }
      }
      
      console.log('🧭 ArticleGrid: Navegando con datos del artículo:', route)
      this.$router.push(route)
    },
    
    handleLinkClick(link) {
      if (!link) return
      
      if (link.startsWith('http')) {
        window.open(link, '_blank')
      } else {
        this.$router.push(link)
      }
    },
    
    generateSlug(title) {
      return title
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // Remover acentos
        .replace(/[^a-z0-9\s-]/g, '') // Solo letras, números, espacios y guiones
        .replace(/\s+/g, '-') // Reemplazar espacios con guiones
        .replace(/-+/g, '-') // Múltiples guiones por uno solo
        .trim()
    }
    // getImage(article) {
    //   if (!article) return ''
    //   const images = Array.isArray(article.images) ? article.images : []
    //   // 1) Imagen principal marcada como is_main
    //   const mainImg = images.find(img => Number(img?.is_main) === 1 || img?.is_main === true || img?.is_main === '1')
    //   const candidates = []
    //   if (mainImg) {
    //     candidates.push(mainImg.image_url, mainImg.url, mainImg.src, mainImg.image, mainImg.path)
    //   }
    //   // 2) Cualquier otra del array de imágenes
    //   for (const img of images) {
    //     candidates.push(img?.image_url, img?.url, img?.src, img?.image, img?.path)
    //   }
    //   // 3) Campos comunes a nivel de artículo
    //   candidates.push(
    //     article.image_url,
    //     article.cover_image,
    //     article.cover,
    //     article.thumbnail,
    //     article.thumb,
    //     article.media_url,
    //     article.photo,
    //     article.picture
    //   )
    //   const chosen = candidates.find(v => typeof v === 'string' && v.trim().length > 0)
    //   const normalized = this.normalizeUrl(chosen)
    //   // 4) Fallback a una imagen local si nada funcionó
    //   return normalized || '/images/10.png'
    // }
  }
}
</script>

<style scoped>
.section { 
  padding: 4rem 0;
  font-family: var(--font-family-base) !important;
}

.section.light { background: var(--bg-secondary); }

/* Estilos para la sección highlights - copiados de FoodWhatToDoSection */
.highlights {
  background: var(--bg-secondary) !important;
  padding: 8rem 0;
}

.highlights-header {
  max-width: 800px;
}

.highlights-title {
  font-size: 3.5rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  line-height: 1.05;
  letter-spacing: -0.03em;
  margin-bottom: 3rem;
  font-family: var(--font-family-base) !important;
}

.highlights-subtitle {
  font-size: 1.125rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: var(--text-secondary) !important;
  line-height: 1.6;
  margin: 0;
  font-family: var(--font-family-base) !important;
}

/* Estilos específicos para las cards de aventura - copiados de food */
.adventure-card-large {
  background: var(--white);
  border-radius: 16px;
  min-height: 400px;
  box-shadow: var(--shadow-md);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid var(--border-light);
  cursor: pointer;
}

.adventure-card-large:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
  border-color: var(--border-color);
  border-radius: 70px 50px 90px 30px;
}

.adventure-card-image-container {
  position: relative;
  height: 100%;
  min-height: 400px;
  overflow: hidden;
}

.adventure-card-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: transform 0.3s ease;
}

.adventure-card-large:hover .adventure-card-image {
  transform: scale(1.1);
  
}

.adventure-card-large:hover .adventure-card-overlay {
  transform: none;
}

.adventure-card-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
  padding: 2rem;
  color: var(--white);
  z-index: 2;
  transform: none;
  transition: none;
}

.adventure-card-title {
  font-size: 1.5rem !important;
  font-weight: var(--font-weight-semibold) !important;
  color: var(--white) !important;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
  font-family: var(--font-family-base) !important;
}

.adventure-card-text {
  font-size: 1rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: rgba(255, 255, 255, 0.9) !important;
  margin: 0;
  line-height: 1.5;
  font-family: var(--font-family-base) !important;
}

.adventure-card-pill {
  background: var(--white);
  border-radius: 16px;
  min-height: 180px;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid var(--border-light);
  cursor: pointer;
}

.adventure-card-pill:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: var(--border-color);
  border-radius: 50px 30px 70px 10px;
}

.adventure-card-pill-image-container {
  position: relative;
  height: 100%;
  min-height: 180px;
  overflow: hidden;
}

.adventure-card-pill-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: transform 0.3s ease;
}

.adventure-card-pill:hover .adventure-card-pill-image {
  transform: scale(1.1);
}

.adventure-card-pill:hover .adventure-card-pill-overlay {
  transform: none;
}

.adventure-card-pill-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
  padding: 1.25rem;
  border-radius: 0 0 16px 16px;
  z-index: 2;
  transform: none;
  transition: none;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.adventure-card-pill-text {
  font-size: 1rem !important;
  font-weight: var(--font-weight-medium) !important;
  color: var(--white) !important;
  margin: 0;
  line-height: 1.4;
  font-family: var(--font-family-base) !important;
}

/* Botón para cards grandes */
.adventure-card-button {
  background: var(--light-gray);
  color: var(--text-primary);
  padding: 0.875rem 1.5rem;
  font-size: 0.95rem;
  font-weight: var(--font-weight-medium);
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.25s ease;
  font-family: var(--font-family-base);
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  width: auto;
  text-decoration: none;
  margin-top: 1rem;
  color: var(--white) !important;
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px);
}

.adventure-card-button:hover {
  background: rgba(255, 255, 255, 0.3);
  color: var(--white) !important;
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

/* Botón para cards pequeñas (pills) */
.adventure-card-pill-button {
  background: rgba(255, 255, 255, 0.2);
  color: var(--white) !important;
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
  font-weight: var(--font-weight-medium);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.25s ease;
  font-family: var(--font-family-base);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: var(--shadow-sm);
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  width: auto;
  text-decoration: none;
  margin-top: 0.75rem;
  backdrop-filter: blur(10px);
}

.adventure-card-pill-button:hover {
  background: rgba(255, 255, 255, 0.3);
  color: var(--white) !important;
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

/* Asegurar que las cards pequeñas tengan la misma altura */
.col-md-6 .row.h-100 {
  height: 100% !important;
}

.col-md-6 .row.h-100 .col-12 {
  height: 50%;
}

.btn-read { 
  display: inline-block; 
  background: transparent;
  color: var(--primary-blue);
  padding: 0.625rem 1.25rem;
  border: 1px solid var(--primary-blue);
  border-radius: 25px;
  text-decoration: none; 
  font-weight: var(--font-weight-medium) !important;
  font-family: var(--font-family-base) !important;
  font-size: 0.85rem !important;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-read:hover { 
  background: var(--primary-blue);
  color: var(--white);
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(30, 64, 175, 0.2);
  border-radius: 50px 30px 70px 10px;
}

/* Responsive */
@media (max-width: 991.98px) {
  .highlights {
    padding: 6rem 0;
  }
  
  .highlights-title {
    font-size: 2.75rem !important;
    line-height: 1.1;
    margin-bottom: 2.5rem;
  }
  
  .highlights-subtitle {
    font-size: 1.05rem !important;
  }
  
  .btn-read {
    margin-top: 1rem;
  }
  
  .adventure-card-large {
    min-height: 350px;
  }
  
  .adventure-card-image-container {
    min-height: 350px;
  }
  
  .adventure-card-pill {
    min-height: 160px;
  }
  
  .adventure-card-pill-image-container {
    min-height: 160px;
  }
}

@media (max-width: 767.98px) {
  .highlights {
    padding: 5rem 0;
  }
  
  .highlights-title {
    font-size: 2.25rem !important;
    line-height: 1.15;
    margin-bottom: 2rem;
  }
  
  .highlights-subtitle {
    font-size: 1rem !important;
  }
  
  .adventure-card-large {
    min-height: 300px;
  }
  
  .adventure-card-image-container {
    min-height: 300px;
  }
  
  .adventure-card-pill {
    min-height: 140px;
  }
  
  .adventure-card-pill-image-container {
    min-height: 140px;
  }
  
  .adventure-card-overlay {
    padding: 1.5rem;
  }
  
  .adventure-card-title {
    font-size: 1.25rem !important;
  }
  
  .adventure-card-pill-overlay {
    padding: 1rem;
  }
  
  .adventure-card-pill-text {
    font-size: 0.9rem !important;
  }
  
  .adventure-card-button {
    font-size: 0.85rem;
    padding: 0.75rem 1.25rem;
    margin-top: 0.75rem;
  }
  
  .adventure-card-pill-button {
    font-size: 0.75rem;
    padding: 0.4rem 0.85rem;
    margin-top: 0.5rem;
  }
}
</style>
