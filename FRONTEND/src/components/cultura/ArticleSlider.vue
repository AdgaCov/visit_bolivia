<template>
  <section class="autumn-section">
    <div class="autumn-background" :style="{ '--bg-image': `url('${currentAutumnImage}')` }">
      <div class="autumn-overlay">
        <div class="autumn-content">
          <div class="autumn-text">
            <h2 class="autumn-title">{{ currentAutumnTitle }}</h2>
            <p class="autumn-description">{{ currentAutumnDescription }}</p>
            <router-link v-if="currentAutumnLink" :to="currentAutumnLink" class="autumn-btn">LEER MÁS</router-link>
          </div>
          <div class="autumn-navigation">
            <div class="autumn-dots">
              <span 
                v-for="(slide, idx) in computedSlides" 
                :key="idx" 
                class="dot" 
                :class="{ active: idx === currentAutumnIndex }"
                @click="goToAutumn(idx)"
              ></span>
            </div>
            <div class="autumn-arrows">
              <button class="autumn-arrow autumn-left" @click="prevAutumn">
                <i class="fas fa-chevron-left"></i>
              </button>
              <button class="autumn-arrow autumn-right" @click="nextAutumn">
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'AutumnSliderSection',
  props: {
    article: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      autumnSlides: [
        {
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/26.jpg',
          title: 'Otoño en Bolivia',
          description: 'El otoño boliviano está definitivamente ACTIVO. Desde una caminata por los valles hasta relajarte en un spa de lujo.',
          link: '/experiencias/otoño'
        },
        {
          image: 'https://images.unsplash.com/photo-1471193945509-9ad0617afabf?w=1200&h=500&fit=crop',
          title: 'Escapadas a los valles',
          description: 'Rutas suaves, clima templado y viñedos: diseña una escapada con calma y buena mesa.',
          link: '/experiencias/valles'
        },
        {
          image: 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=1200&h=500&fit=crop',
          title: 'Bienestar y spa',
          description: 'Hoteles boutique, termas y tratamientos con ingredientes andinos para renovar energía.',
          link: '/experiencias/bienestar'
        }
      ],
      currentAutumnIndex: 0
    }
  },
  computed: {
    // computedSlides() {
    //   const images = this.article?.images
    //   if (Array.isArray(images) && images.length > 0) {
    //     const title = this.article?.title || ''
    //     const description = this.article?.description || this.article?.subtitle || ''
    //     const link = this.article?.link || null
    //     return images.map(img => ({
    //       image: img?.image_url || '',
    //       title: title,
    //       description: img?.alt_text || description,
    //       link: link
    //     }))
    //   }
    //   return this.autumnSlides
    // },
    computedSlides() {
      const images = this.article?.images
      if (Array.isArray(images) && images.length > 0) {
        const title = this.article?.title || ''
        const description = this.article?.description || this.article?.subtitle || ''
        const link = this.article?.link || null
        return images.map(img => ({
          image: this.$buildImg ? this.$buildImg(img?.image_url || '') : (img?.image_url || ''),
          title: title,
          description: img?.alt_text || description,
          link: link
        }))
      }
      
      // ✅ Procesar también las imágenes del fallback (autumnSlides)
      return this.autumnSlides.map(slide => ({
        ...slide,
        image: this.$buildImg ? this.$buildImg(slide.image) : slide.image
      }))
    },
    currentAutumnImage() {
      if (!this.computedSlides || this.computedSlides.length === 0) return '';
      return this.computedSlides[this.currentAutumnIndex].image;
    },
    currentAutumnTitle() {
      if (!this.computedSlides || this.computedSlides.length === 0) return '';
      return this.computedSlides[this.currentAutumnIndex].title;
    },
    currentAutumnDescription() {
      if (!this.computedSlides || this.computedSlides.length === 0) return '';
      return this.computedSlides[this.currentAutumnIndex].description;
    },
    currentAutumnLink() {
      if (!this.computedSlides || this.computedSlides.length === 0) return null;
      return this.computedSlides[this.currentAutumnIndex].link || null;
    }
  },
  methods: {
    nextAutumn() {
      if (this.computedSlides.length === 0) return;
      this.currentAutumnIndex = (this.currentAutumnIndex + 1) % this.computedSlides.length;
    },
    prevAutumn() {
      if (this.computedSlides.length === 0) return;
      this.currentAutumnIndex = (this.currentAutumnIndex - 1 + this.computedSlides.length) % this.computedSlides.length;
    },
    goToAutumn(index) {
      if (index >= 0 && index < this.computedSlides.length) {
        this.currentAutumnIndex = index;
      }
    }
  }
}
</script>

<style scoped>
/* Autumn section - Estilo refinado */
.autumn-section {
  position: relative;
  height: 520px;
  overflow: hidden;
  margin: 4rem 0;
  margin-left: 1rem;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
  transition: border-radius 600ms cubic-bezier(0.4, 0, 0, 1);
}

.autumn-section:hover {
  border-radius: 20px 50px 24px 20px;
}

.autumn-background {
  width: 100%;
  height: 100%;
  background-color: #000; /* fallback para evitar parpadeo */
  position: relative;
  overflow: hidden;
}

.autumn-background::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: var(--bg-image);
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  transform: scale(1);
  transition: transform 1200ms cubic-bezier(0.25, 0.1, 0.25, 1);
  will-change: transform;
  z-index: 0;
}

.autumn-section:hover .autumn-background::before {
  transform: scale(1.05);
}

/* Vignette sutil inferior para legibilidad del panel, sin tapar la imagen */
.autumn-background::after {
  content: '';
  position: absolute;
  inset: 0;
  /* Overlays más suaves para no oscurecer demasiado la imagen */
  background: 
    radial-gradient(1200px 500px at 75% 60%, rgba(0,0,0,0.25), transparent 60%),
    linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.06) 60%, rgba(0,0,0,0.2) 100%),
    linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 65%, rgba(0,0,0,0.22) 85%, rgba(0,0,0,0.28) 100%);
  z-index: 1;
  pointer-events: none;
}

.autumn-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(120deg, rgba(255,255,255,0.06), rgba(255,255,255,0.02));
  /* Eliminar blur para evitar suavizado de la imagen de fondo */
  /* backdrop-filter: blur(2px); */
  z-index: 2;
  pointer-events: none;
}

.autumn-content {
  position: absolute;
  bottom: 2rem;
  right: 2rem;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(14px) saturate(120%);
  -webkit-backdrop-filter: blur(14px) saturate(120%);
  border-radius: 20px;
  padding: 2rem;
  max-width: 460px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.35);
  z-index: 3;
  transition: transform 300ms ease, box-shadow 300ms ease;
  pointer-events: auto;
}

.autumn-content:hover {
  transform: translateY(-2px);
  box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
}

.autumn-title {
  font-size: 2.5rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  font-family: var(--font-family-base);
  letter-spacing: -0.02em;
  line-height: 1.2;
}

.autumn-description {
  color: var(--text-secondary);
  font-size: 1.125rem;
  font-family: var(--font-family-base);
  line-height: 1.6;
  margin-bottom: 1.25rem;
}

.autumn-btn {
  display: inline-block;
  background: var(--light-gray);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  padding: 0.875rem 1.5rem;
  border-radius: 10px;
  text-decoration: none;
  font-weight: var(--font-weight-medium);
  font-family: var(--font-family-base);
  font-size: 0.95rem;
  letter-spacing: 0.02em;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
  margin-bottom: 0.75rem;
  box-shadow: var(--shadow-sm);
}

.autumn-btn:hover {
  background: var(--medium-gray);
  color: var(--text-primary);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.autumn-btn:active { transform: translateY(-1px); }
.autumn-btn:focus { outline: none; box-shadow: 0 0 0 3px rgba(0, 55, 255, 0.25); }

.autumn-navigation {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.autumn-dots {
  display: flex;
  gap: 0.5rem;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 10px;
  background: rgba(255,255,255,0.6);
  transition: all 0.25s ease;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.dot.active {
  background: var(--primary-blue);
  transform: scale(1.15);
  box-shadow: 0 4px 10px rgba(0, 55, 255, 0.25);
}

.autumn-arrows {
  display: flex;
  gap: 0.5rem;
}

.autumn-arrow {
  width: 40px;
  height: 40px;
  border: 1px solid rgba(255,255,255,0.6);
  background: rgba(255,255,255,0.9);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
  color: var(--text-secondary);
  font-size: 0.85rem;
  box-shadow: 0 6px 14px rgba(0,0,0,0.12);
}

.autumn-arrow:hover {
  background: var(--primary-blue);
  color: var(--white);
  border-color: var(--primary-blue);
  transform: translateY(-1px) scale(1.05);
}

.autumn-arrow:active { transform: translateY(0) scale(1.02); }

/* Responsive */
@media (max-width: 1024px) {
  .autumn-section { height: 480px; border-radius: 16px; margin-left: 0.75rem; }
  .autumn-content { bottom: 1.5rem; right: 1.5rem; max-width: 420px; }
  .autumn-title { font-size: 1.75rem; }
}

@media (max-width: 768px) {
  .autumn-section { height: 420px; margin: 3rem 0; border-radius: 14px; margin-left: 0.5rem; }
  .autumn-background { background-position: center 35%; }
  .autumn-content {
    bottom: 1rem;
    right: 1rem;
    left: 1rem;
    max-width: none;
    padding: 1.25rem 1.25rem 1.1rem;
  }
  .autumn-title { font-size: 1.5rem; }
  .autumn-description { font-size: 0.9rem; margin-bottom: 1rem; }
  .autumn-arrow { width: 36px; height: 36px; }
}

@media (max-width: 480px) {
  .autumn-section { height: 360px; border-radius: 12px; margin-left: 0.5rem; }
  .autumn-title { font-size: 1.35rem; }
  .autumn-description { font-size: 0.875rem; }
  .autumn-btn { padding: 0.6rem 1.1rem; font-size: 0.8rem; border-radius: 10px; }
}
</style>

