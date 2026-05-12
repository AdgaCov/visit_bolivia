<template>
  <section class="hero-video-section" role="region" aria-label="Presentación de Bolivia">
    <video
      class="hero-video"
      :poster="getPosterImage()"
      autoplay
      muted
      loop
      playsinline
      preload="metadata"
    >
      <!-- Video dinámico del artículo (prioridad) -->
      <source v-if="article?.media_url" :src="getVideoUrl()" type="video/mp4">
      
      <!-- Videos estáticos originales (fallback) -->
      <!-- Desktop grande (>=1600px) -->
      <source src="/videos/conocebolivia1080X192.webm" type="video/webm" media="(min-width: 1600px)">
      <source src="/videos/conocebolivia1080X192.mp4"  type="video/mp4"  media="(min-width: 1600px)">
      <!-- Desktop (>=1280px) -->
      <source src="/videos/conocebolivia1280x720.webm" type="video/webm" media="(min-width: 1280px)">
      <source src="/videos/conocebolivia1280x720.mp4"  type="video/mp4"  media="(min-width: 1280px)">
      <!-- Tablet (>=600px) -->
      <source src="/videos/conocebolivia854x480.webm"  type="video/webm" media="(min-width: 600px)">
      <source src="/videos/conocebolivia854x480.mp4"   type="video/mp4"  media="(min-width: 600px)">
      <!-- Móvil (<600px) -->
      <source src="/videos/conocebolivia640x360.webm"  type="video/webm">
      <source src="/videos/conocebolivia640x360.mp4"   type="video/mp4">
      Tu navegador no soporta videos en background.
    </video>

    <div class="hero-overlay" aria-hidden="true"></div>
    <div class="hero-content text-center">
      <p class="hero-heading">{{ article?.title || 'Bolivia te espera' }}</p>
      <p class="hero-tagline">{{ article?.subtitle || 'Un viaje al corazón de Sudamérica donde la naturaleza, aventura y cultura se entrelazan' }}</p>
      <div class="hero-actions">
        <router-link :to="article?.link || '/regiones'" class="hero-btn hero-btn-primary">Conoce Bolivia</router-link>
        <!-- <router-link :to="article?.link || '/lugares'" class="hero-btn hero-btn-secondary">Ver lugares</router-link> -->
      </div>
    </div>
  </section>








  
</template>

<script>
export default {
  name: 'HeroSection',
  props: {
    article: {
      type: Object,
      required: false,
      default: null
    }
  },
  methods: {
    getPosterImage() {
      // Si hay artículo y tiene media_url, usar como poster
      if (this.article?.media_url) {
        // Si es una imagen, construir la URL con $buildImg
        const url = this.article.media_url
        if (url.includes('.jpg') || url.includes('.jpeg') || url.includes('.png') || url.includes('.webp')) {
          return this.$buildImg ? this.$buildImg(url) : url
        }
        return this.$buildImg ? this.$buildImg(url) : url
      }
      // Fallback a imagen estática
      return '/images/41.png'
    },
    getVideoUrl() {
      if (!this.article?.media_url) {
        console.log('⚠️ HeroSection: No hay media_url en el artículo')
        return ''
      }
      // Construir la URL completa usando $buildImg para URLs relativas
      const url = this.article.media_url
      const finalUrl = this.$buildImg ? this.$buildImg(url) : url
      console.log('🎥 HeroSection - URL del video:', {
        original: url,
        final: finalUrl,
        hasBuildImg: !!this.$buildImg
      })
      return finalUrl
    }
  }
}
</script>

<style scoped>
/* Hero video (profesional) */
.hero-video-section {
  position: relative;
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  border-bottom: 1px solid var(--border-light);
}

.hero-video {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: saturate(1.05) contrast(1.05);
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.4);
}

.hero-content {
  position: relative;
  z-index: 1;
  color: #ffffff;
  padding: 2rem;
}

.hero-heading {
  font-size: 3rem;
  font-weight: 600;
  letter-spacing: -0.02em;
  margin-bottom: 0.75rem;
  color: rgba(255,255,255,0.9);
}

/* Asegurar que el color se aplique incluso con estilos globales */
:deep(.hero-heading) {
  color: #ffffff !important;
}

/* Regla adicional con mayor especificidad */
.hero-content .hero-heading {
  color: #ffffff !important;
}

.hero-tagline {
  font-size: 1.125rem;
  color: rgba(255,255,255,0.9);
  margin-bottom: 1.25rem;
}

/* Botones hero minimalistas */
.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.hero-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.875rem 2rem;
  font-size: 0.95rem;
  font-weight: 500;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  letter-spacing: 0.025em;
  min-width: 160px;
}

.hero-btn-primary {
  background: transparent;
  color: var(--white);
  border-color: var(--white);
  backdrop-filter: blur(10px);
}

.hero-btn-primary:hover {
  background: var(--white);
  color: #1a1a1a;
  border-color: var(--white);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
}

.hero-btn-secondary {
  background: transparent;
  color: var(--white);
  border-color: rgba(255, 255, 255, 0.4);
  backdrop-filter: blur(10px);
}

.hero-btn-secondary:hover {
  background: var(--white);
  color: #1a1a1a;
  border-color: var(--white);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
}

@media (max-width: 768px) {
  .hero-video-section { min-height: 48vh; }
  .hero-heading { font-size: 2rem; }
  .hero-tagline { font-size: 1rem; }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
  }
  
  .hero-btn {
    padding: 0.75rem 1.5rem;
    font-size: 0.9rem;
    min-width: 140px;
  }
}

/* Respeto a usuarios con reducción de movimiento */
@media (prefers-reduced-motion: reduce) {
  .hero-video { display: none; }
}
</style>
