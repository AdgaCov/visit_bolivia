<template>
  <!-- Sección: Clima y tiempo -->
  <section class="weather-section">
    <div class="container">
      <div class="weather-content">
        <div class="weather-image">
          <div class="image-container">
            <img 
              :src="getMainImage()" 
              :alt="getImageAlt()" 
              class="weather-photo"
              @error="handleImageError"
            />
            <div class="photo-credit">
              <span>Foto: {{ article?.author || 'Bolivia Turismo' }}</span>
            </div>
          </div>
        </div>
        
        <div class="weather-text">
          <h2 class="weather-title">{{ article?.title || 'Música Tradicional' }}</h2>
          <h4 class="sub-title" v-if="article?.subtitle">{{ article.subtitle }}</h4>
          <p
            v-for="(para, idx) in contentParagraphs"
            :key="idx"
            class="weather-description"
          >
            {{ para }}
          </p>
          <button class="weather-button" v-if="article?.link">
            <router-link 
              v-if="!isExternalLink(article.link)" 
              :to="article.link" 
              class="text-decoration-none text-white"
            >
              VER MÁS INFORMACIÓN
            </router-link>
            <a 
              v-else 
              :href="article.link" 
              target="_blank" 
              rel="noopener noreferrer"
              class="text-decoration-none text-white"
            >
              VER MÁS INFORMACIÓN
            </a>
          </button>
          
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ArticleMusic',
  props: {
    article: {
      type: Object,
      default: null
    }
  },
  computed: {
    contentParagraphs() {
      const fallback = 'La música en las culturas de Bolivia refleja la riqueza de sus pueblos originarios y la fusión con otras influencias.'
      const raw = (this.article?.content ?? fallback)
      return String(raw)
        .split(/\n{2,}|\r?\n/g)
        .map(s => s.trim())
        .filter(Boolean)
    }
  },
  methods: {
    isExternalLink(link) {
      if (!link || typeof link !== 'string') return false
      return link.startsWith('http://') || link.startsWith('https://')
    },
    handleImageError(event) {
      // Fallback a una imagen placeholder si la imagen no se carga
      event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0xNzUgMTI1SDIyNVYxNzVIMTc1VjEyNVoiIGZpbGw9IiNEOUQ5RDkiLz4KPHBhdGggZD0iTTE4NSAxMzVIMjE1VjE2NUgxODVWMTM1WiIgZmlsbD0iI0NDQ0NDQyIvPgo8dGV4dCB4PSIyMDAiIHk9IjIwMCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5OTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZW4gbm8gZGlzcG9uaWJsZTwvdGV4dD4KPC9zdmc+'
    },
    getMainImage() {
    let imageUrl = '/images/climatiempo.jpg'
    
    if (this.article?.media_url) {
      imageUrl = this.article.media_url
    }
    
    // ✅ Procesar la URL con $buildImg
    return this.$buildImg ? this.$buildImg(imageUrl) : imageUrl
  },
    // getMainImage() {
    //   if (this.article?.media_url) {
    //     return this.article.media_url
    //   }
    //   return '/images/climatiempo.jpg'
    // },
    
    getImageAlt() {
      if (this.article?.title) {
        return this.article.title
      }
      return 'Música Tradicional'
    }
  }
}
</script>

<style scoped>
/* ===============================
   Sección de Clima
   =============================== */
.weather-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.weather-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: stretch;
  min-height: 600px;
}

.weather-image {
  position: relative;
  height: 100%;
  min-height: 600px;
  overflow: hidden;
  margin-left: calc(-50vw + 50%);
  margin-right: 0;
  width: 100;
  margin-top: -2rem;
  margin-bottom: -2rem;
}

.weather-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 0 50px 50px 0;
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
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  height: 100%;
  /* Limitar altura al alto de la imagen y permitir scroll en desktop */
  max-height: 600px; /* coincide con .weather-image min-height */
  overflow-y: auto;
  /* Ocultar scrollbar cross-browser */
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */
  /* Asegurar que el scroll empiece desde arriba */
  align-items: flex-start;
}

.weather-text::-webkit-scrollbar { display: none; }

.weather-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.sub-title {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1rem;
}

.weather-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.weather-button {
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
  align-self: flex-start;
}

.weather-button:hover {
  background: var(--medium-gray);
  color: var(--text-primary);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

/* Asegurar que enlaces dentro del botón hereden color y no subrayen */
.weather-button :deep(a),
.weather-button :deep(.text-white),
.weather-button :deep(.text-light) {
  color: var(--text-primary) !important;
  text-decoration: none !important;
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

/* Responsive para clima */
@media (max-width: 991.98px) {
  .weather-content {
    grid-template-columns: 1fr;
    gap: 2rem;
    align-items: center;
  }
  
  .weather-text {
    padding-left: 0;
    text-align: center;
    height: auto;
    /* En layouts de una columna, flujo natural del texto */
    max-height: none;
    overflow: visible;
  }
  
  .weather-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .weather-description {
    max-width: 100%;
  }
  
  .weather-image {
    height: 400px;
    min-height: 400px;
    margin-left: -1rem;
    margin-right: -1rem;
    width: calc(100% + 2rem);
    margin-top: -1rem;
    margin-bottom: -1rem;
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
    height: 300px;
    min-height: 300px;
    margin-left: -1rem;
    margin-right: -1rem;
    width: calc(100% + 2rem);
    margin-top: -1rem;
    margin-bottom: -1rem;
  }
}
</style>
