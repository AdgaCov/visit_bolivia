<template>
  <section class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-4">
          <h1 class="hero-title">{{ title || 'Bolivia te da el tiempo y el espacio…' }}</h1>
          <p class="hero-subtitle">{{ subtitle || 'Para sumergirte en nuestros paisajes, historia, gastronomía y comunidad local. Tiempo para experiencias memorables y sostenibles.' }}</p>
        </div>
        <div class="col-lg-8">
          <div class="hero-media">
            <template v-if="isVideo && mediaUrl">
              <video
                class="hero-video"
                autoplay
                muted
                loop
                playsinline
                :poster="posterFallback"
                @error="handleVideoError"
                @loadstart="handleVideoLoad"
                @canplay="handleVideoCanPlay"
                @loadeddata="handleVideoLoaded"
              >
                <source :src="mediaUrl" type="video/mp4" media="(min-width: 1600px)" />
                <source :src="mediaUrl" type="video/mp4" />
                Tu navegador no soporta videos HTML5.
              </video>
            </template>
            <template v-else>
              <img 
                class="hero-video" 
                :src="mediaUrl || posterFallback" 
                :alt="title || 'Hero media'" 
                @error="handleImageError"
                @load="handleImageLoad"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroWhatToDoSection',
  props: {
    title: {
      type: String,
      default: ''
    },
    subtitle: {
      type: String,
      default: ''
    },
    mediaUrl: {
      type: String,
      default: ''
    }
  },
  computed: {
    isVideo() {
      if (!this.mediaUrl) return false
      const url = this.mediaUrl.toLowerCase()
      return url.endsWith('.mp4') || url.includes('/videos/') || url.includes('video')
    },
    posterFallback() {
      return '/images/feriado.png'
    }
  },
  methods: {
    handleVideoError(event) {
      console.error('Error cargando video:', event)
      console.error('URL del video:', this.mediaUrl)
    },
    handleVideoLoad(event) {
      console.log('Video cargando:', event)
      console.log('URL del video:', this.mediaUrl)
    },
    handleVideoCanPlay(event) {
      console.log('Video puede reproducirse:', event)
      console.log('URL del video:', this.mediaUrl)
    },
    handleVideoLoaded(event) {
      console.log('Video datos cargados:', event)
      console.log('URL del video:', this.mediaUrl)
    },
    handleImageError(event) {
      console.error('Error cargando imagen:', event)
      console.error('URL de la imagen:', this.mediaUrl)
    },
    handleImageLoad(event) {
      console.log('Imagen cargada:', event)
      console.log('URL de la imagen:', this.mediaUrl)
    },
    testVideoUrl() {
      if (this.mediaUrl) {
        const fullUrl = window.location.origin + this.mediaUrl
        console.log('Probando URL del video:', fullUrl)
        
        // Crear un elemento video temporal para probar
        const testVideo = document.createElement('video')
        testVideo.src = fullUrl
        testVideo.onloadstart = () => console.log('Video test: Cargando...')
        testVideo.oncanplay = () => console.log('Video test: Puede reproducirse')
        testVideo.onerror = (e) => console.error('Video test: Error', e)
        testVideo.load()
      }
    }
  },
  mounted() {
    console.log('HeroWhatToDoSection mounted')
    console.log('mediaUrl:', this.mediaUrl)
    console.log('isVideo:', this.isVideo)
    console.log('posterFallback:', this.posterFallback)
    
    // Verificar si la URL es válida
    if (this.mediaUrl) {
      console.log('URL completa:', window.location.origin + this.mediaUrl)
      console.log('¿Termina en .mp4?', this.mediaUrl.toLowerCase().endsWith('.mp4'))
      console.log('¿Contiene /videos/?', this.mediaUrl.includes('/videos/'))
      
      // Probar la URL del video
      this.testVideoUrl()
    }
  }
}
</script>

<style scoped>
/* Hero rediseñado estilo referencia, conservando paleta */
.hero { 
  background: var(--bg-secondary); 
  padding: 6rem 0; 
  position: relative; 
  overflow: hidden; 
  border-bottom: 1px solid var(--border-light); 
  min-height: 600px; 
}

.hero-title { 
  font-size: 2.75rem !important; 
  font-weight: var(--font-weight-light) !important; 
  color: var(--text-primary) !important;
  letter-spacing: -0.02em;
  line-height: 1.15;
  margin-bottom: 1.25rem;
  font-family: var(--font-family-base) !important;
}

.hero-subtitle { 
  color: var(--text-secondary) !important; 
  margin-top: 1rem; 
  font-size: 1.125rem !important; 
  max-width: 640px;
  font-family: var(--font-family-base) !important;
}

.hero-media { 
  position: absolute; 
  top: 0; 
  right: 0; 
  width: 60%; 
  height: 100%; 
  border-radius: 0; 
  clip-path: ellipse(75% 95% at 100% 75%); 
  overflow: hidden; 
}

.hero-video { 
  position: absolute; 
  inset: 0; 
  width: 100%; 
  height: 100%; 
  object-fit: cover; 
}

/* Tablet */
@media (max-width: 991.98px) {
  .hero-media { 
    position: relative; 
    clip-path: none; 
    border-radius: 16px; 
    margin-top: 1.25rem; 
    width: 100%;
    height: 100%;
    min-height: 350px;
  }
  
  .hero-title {
    font-size: 2.25rem !important;
    line-height: 1.2;
    margin-bottom: 1.5rem;
  }
}

/* Móvil */
@media (max-width: 767.98px) {
  .hero { padding: 4rem 0; min-height: 500px; }
  .hero-title { 
    font-size: 1.875rem !important;
    font-weight: var(--font-weight-light) !important;
    color: var(--text-primary) !important;
    letter-spacing: -0.02em;
    line-height: 1.2;
    margin-bottom: 1.5rem;
  }
  .hero-media { 
    position: relative; 
    height: 100%; 
    min-height: 300px;
    clip-path: none; 
    border-radius: 16px; 
    margin-top: 1.25rem; 
    width: 100%;
  }
}
</style>
