<template>
  <section class="gastronomy-hero">
    <div class="grid">
      <div class="gallery-card">
        <div class="counter" v-if="imageCount">{{ currentImageIndex + 1 }}/{{ imageCount }}</div>
        
        <!-- Carrusel de imágenes -->
        <div class="gallery-container" ref="galleryContainer">
          <div class="gallery-track" :style="{ transform: `translateX(-${currentImageIndex * 100}%)` }">
            <img 
              v-for="(image, index) in galleryImages" 
              :key="index"
              :src="image" 
              :alt="`Restaurante destacado - Imagen ${index + 1}`" 
              class="gallery-img" 
            />
          </div>
        </div>

        <!-- Botones de navegación -->
        <button 
          v-if="imageCount > 1"
          class="gallery-nav prev" 
          @click="previousImage"
          :disabled="currentImageIndex === 0"
          aria-label="Imagen anterior"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <button 
          v-if="imageCount > 1"
          class="gallery-nav next" 
          @click="nextImage"
          :disabled="currentImageIndex === imageCount - 1"
          aria-label="Siguiente imagen"
        >
          <i class="fas fa-chevron-right"></i>
        </button>

        <!-- Indicadores de puntos -->
        <div class="gallery-dots" v-if="imageCount > 1">
          <button 
            v-for="(image, index) in galleryImages" 
            :key="index"
            class="dot" 
            :class="{ active: currentImageIndex === index }"
            @click="goToImage(index)"
            :aria-label="`Ir a imagen ${index + 1}`"
          ></button>
        </div>

        <button class="expand" aria-label="Ampliar imagen" @click="openLightbox">
          <i class="fas fa-expand"></i>
        </button>
      </div>

      <div class="side">
        <div class="map-card">
          <div class="map-header">Ubicación del Restaurante</div>
          <div class="map-container">
            <SimpleMap
              :center="restaurantCenter"
              :zoom="restaurantZoom"
              :lugares="restaurantMarkers"
              height="100%"
            />
          </div>
        </div>

        <!-- <div class="region-card">
          <div class="region-illustration" :style="regionIllustrationStyle"></div>
          <div class="region-name">{{ regionName }}</div>
        </div> -->
      </div>
    </div>
  </section>

  <!-- Lightbox de imagen -->
  <div v-if="lightboxOpen" class="lightbox" @click.self="closeLightbox" role="dialog" aria-modal="true">
    <button class="lightbox-close" @click="closeLightbox" aria-label="Cerrar">
      <i class="fas fa-times"></i>
    </button>
    <div class="lightbox-content">
      <img :src="galleryImages[currentImageIndex]" :alt="`Restaurante destacado - Imagen ${currentImageIndex + 1}`" />
      <button v-if="imageCount > 1" class="lightbox-nav prev" @click.stop="previousImage" aria-label="Anterior">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button v-if="imageCount > 1" class="lightbox-nav next" @click.stop="nextImage" aria-label="Siguiente">
        <i class="fas fa-chevron-right"></i>
      </button>
      <div class="lightbox-counter">{{ currentImageIndex + 1 }} / {{ imageCount }}</div>
    </div>
  </div>
</template>

<script>


import SimpleMap from '@/components/SimpleMap.vue'

export default {
  name: 'GastronomyHero',
  components: { SimpleMap },
  props: {
    featured: { type: Object, required: false, default: null }
  },
  data() {
    return {
      currentImageIndex: 0,
      autoPlayInterval: null,
      lightboxOpen: false
    }
  },
  computed: {
    regionIllustrationStyle() {
      const url = this.featured?.regionImage || this.featured?.region_image || null
      if (!url) return {}
      return {
        backgroundImage: `url(${url})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center'
      }
    },
    galleryImages() {
      const processImage = (url) => {
        if (!url) return null
        return this.$buildImg ? this.$buildImg(url) : url
      }

      if (Array.isArray(this.featured?.images) && this.featured.images.length > 0) {
        return this.featured.images
          .map(img => img.image_url || img)
          .map(processImage)
          .filter(Boolean)
      }
      
      if (this.featured?.image) {
        const processed = processImage(this.featured.image)
        return processed ? [processed] : []
      }
      
      return ['https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=1600&auto=format&fit=crop']
    },
    // galleryImages() {
    //   // Si el restaurante tiene múltiples imágenes de la API, las usamos
    //   if (Array.isArray(this.featured?.images) && this.featured.images.length > 0) {
    //     return this.featured.images.map(img => img.image_url || img)
    //   }
      
    //   // Si el restaurante tiene una imagen principal, la usamos
    //   if (this.featured?.image) {
    //     return [this.featured.image]
    //   }
      
    //   // Fallback: imagen por defecto
    //   return ['https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=1600&auto=format&fit=crop']
    // },
    regionName() {
      return this.featured?.place?.name || this.featured?.location || 'Bolivia'
    },
    imageCount() {
      return this.galleryImages.length
    },
    restaurantCenter() {
      // Usar las coordenadas del restaurante si están disponibles
      if (this.featured?.latitude && this.featured?.longitude) {
        return [
          parseFloat(this.featured.latitude),
          parseFloat(this.featured.longitude)
        ]
      }
      // Fallback a coordenadas de Bolivia
      return [-16.2902, -63.5887]
    },
    restaurantMarkers() {
      if (!this.featured) return []
      
      // Si el restaurante tiene coordenadas, crear marcador
      if (this.featured.latitude && this.featured.longitude) {
        return [{
          id: `restaurant-${this.featured.id}`,
          nombre: this.featured.name || 'Restaurante',
          coordenadas: this.restaurantCenter,
          descripcion: this.featured.description || '',
          region: this.featured.place?.name || 'Bolivia',
          tipo: 'restaurante',
          destacado: true
        }]
      }
      
      return []
    },
    restaurantZoom() {
      // Zoom dinámico basado en el tipo de ubicación
      if (!this.featured?.place?.name) return 17
      
      const placeName = this.featured.place.name.toLowerCase()
      
      // Para lugares remotos como el Salar de Uyuni, usar zoom más bajo
      if (placeName.includes('salar') || placeName.includes('uyuni')) {
        return 8
      }
      
      // Para lugares en ciudades, usar zoom medio
      if (placeName.includes('oruro') || placeName.includes('la paz') || 
          placeName.includes('cochabamba') || placeName.includes('santa cruz')) {
        return 12
      }
      
      // Para otros lugares, zoom por defecto
      return 10
    }
  },
  methods: {
    nextImage() {
      if (this.currentImageIndex < this.imageCount - 1) {
        this.currentImageIndex++
      } else {
        this.currentImageIndex = 0 // Loop al inicio
      }
    },
    previousImage() {
      if (this.currentImageIndex > 0) {
        this.currentImageIndex--
      } else {
        this.currentImageIndex = this.imageCount - 1 // Loop al final
      }
    },
    goToImage(index) {
      this.currentImageIndex = index
    },
    startAutoPlay() {
      this.autoPlayInterval = setInterval(() => {
        this.nextImage()
      }, 5000) // Cambia cada 5 segundos
    },
    stopAutoPlay() {
      if (this.autoPlayInterval) {
        clearInterval(this.autoPlayInterval)
        this.autoPlayInterval = null
      }
    },
    openLightbox() {
      this.lightboxOpen = true
      try { document.body.style.overflow = 'hidden' } catch (e) {}
    },
    closeLightbox() {
      this.lightboxOpen = false
      try { document.body.style.overflow = '' } catch (e) {}
    }
  },
  mounted() {
    // Iniciar auto-play si hay más de una imagen
    if (this.imageCount > 1) {
      this.startAutoPlay()
    }
  },
  beforeUnmount() {
    this.stopAutoPlay()
  }
}
</script>

<style scoped>
.gastronomy-hero { 
  margin-bottom: 1.5rem; 
  /* full-bleed: ocupar todo el ancho de la ventana */
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
  width: calc(100vw);
  /* width: calc(100vw - 2rem); */
  padding-inline: 1rem;
}

.grid {
  display: grid;
  grid-template-columns: 3fr 1.2fr;
  gap: 1.25rem;
  align-items: stretch;
}

.gallery-card {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: var(--shadow-lg);
  background: var(--bg-card);
  height: 460px; /* Altura fija para igualar con el mapa */
}

.gallery-container {
  position: relative;
  width: 100%;
  height: 460px;
  overflow: hidden;
}

.gallery-track {
  display: flex;
  width: 100%;
  height: 100%;
  transition: transform 0.5s ease-in-out;
}

.gallery-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  flex-shrink: 0;
}

.counter {
  position: absolute;
  top: 10px;
  left: 10px;
  background: rgba(0,0,0,0.6);
  color: var(--white);
  padding: 0.25rem 0.5rem;
  border-radius: 8px;
  font-weight: 500;
  z-index: 11;
  pointer-events: none;
}

.gallery-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 50%;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: var(--shadow-md);
  transition: all 0.2s ease;
  z-index: 10;
  color: var(--text-primary);
  font-size: 1.1rem;
}

.gallery-nav:hover:not(:disabled) {
  background: var(--primary-blue);
  color: var(--white);
  transform: translateY(-50%) scale(1.05);
}

.gallery-nav:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.gallery-nav.prev {
  left: 15px;
}

.gallery-nav.next {
  right: 15px;
}

.gallery-dots {
  position: absolute;
  bottom: 15px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  z-index: 10;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition: all 0.2s ease;
}

.dot.active {
  background: var(--white);
  transform: scale(1.2);
}

.dot:hover {
  background: rgba(255, 255, 255, 0.8);
}

.expand {
  position: absolute;
  right: 10px;
  bottom: 10px;
  background: rgba(255,255,255,0.9);
  border: 0;
  border-radius: 10px;
  padding: 0.5rem 0.6rem;
  box-shadow: var(--shadow-md);
  cursor: pointer;
  z-index: 10;
}

/* Lightbox */
.lightbox {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.lightbox-content {
  position: relative;
  max-width: 98vw;
  max-height: 95vh;
}
.lightbox-content img {
  display: block;
  max-width: 98vw;
  max-height: 95vh;
  width: auto;
  height: auto;
  object-fit: contain;
  border-radius: 10px;
  box-shadow: var(--shadow-xl);
}
.lightbox-close {
  position: absolute;
  top: 20px;
  right: 20px;
  background: rgba(255,255,255,0.95);
  border: 0;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1001;
}
.lightbox-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255,255,255,0.95);
  border: 0;
  border-radius: 50%;
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.lightbox-nav.prev { left: 24px; }
.lightbox-nav.next { right: 24px; }
.lightbox-counter {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  color: #fff;
  background: rgba(0,0,0,0.5);
  padding: 6px 10px;
  border-radius: 12px;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .lightbox-nav { width: 48px; height: 48px; }
  .lightbox-nav.prev { left: 12px; }
  .lightbox-nav.next { right: 12px; }
  .lightbox-counter { bottom: 12px; }
}

.side { 
  display: flex; 
  flex-direction: column; 
  height: 460px; /* Altura fija para igualar con la galería */
}

.map-card, .region-card {
  border-radius: 16px;
  overflow: hidden;
  background: var(--bg-card);
  box-shadow: var(--shadow-lg);
}

.map-card { 
  position: relative; 
  display: flex; 
  flex-direction: column; 
  height: 100%; /* Ocupar todo el alto del contenedor padre */
}
.map-header { 
  position: absolute; 
  top: 12px; 
  right: 12px; 
  background: var(--white); 
  border-radius: 22px; 
  padding: 0.35rem 0.9rem; 
  font-weight: 600; 
  box-shadow: var(--shadow-sm); 
  z-index: 2; 
}
.map-container { 
  flex: 1; /* Ocupar todo el espacio disponible */
  height: 100%; 
}

.region-card {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  background: var(--bg-secondary);
  padding: 1rem;
}

.region-illustration { width: 72%; height: 120px; background: var(--bolivia-purple); border-radius: 14px; opacity: 0.9; }
.region-illustration[style*="background-image"] { background: none; opacity: 1; }
.region-name { margin-top: 0.75rem; font-weight: 600; font-size: 1.25rem; }

@media (max-width: 992px) {
  .grid { grid-template-columns: 1fr; }
  .gallery-card { height: 300px; }
  .side { height: 300px; }
  .gallery-container { height: 300px; }
  .map-container { height: 100%; }
  .gallery-nav {
    width: 40px;
    height: 40px;
    font-size: 0.9rem;
  }
  .gallery-nav.prev {
    left: 10px;
  }
  .gallery-nav.next {
    right: 10px;
  }
  .gallery-dots {
    bottom: 10px;
  }
  .dot {
    width: 8px;
    height: 8px;
  }
}
</style>