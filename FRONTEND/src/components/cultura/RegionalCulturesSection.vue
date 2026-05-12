<template>
  <section class="regional-cultures-section">
    <div class="cultures-container">
      <!-- Header -->
      <div class="cultures-header">
        <h2 class="cultures-title">Descubre culturas regionales únicas</h2>
        <p class="cultures-description">
          A pesar del tamaño compacto de Bolivia, existen varias regiones donde diferentes idiomas, costumbres y tradiciones se han transmitido a través de generaciones.
        </p>
      </div>
      
      <!-- Carrusel de Culturas -->
      <div class="cultures-carousel-wrapper">
        <div class="cultures-carousel" ref="culturesCarousel">
          <div 
            v-for="(culture, index) in regionalCultures" 
            :key="index"
            class="culture-card"
          >
            <div class="culture-image">
              <img :src="culture.image" :alt="culture.name" loading="lazy" />
            </div>
            <div class="culture-info">
              <h3 class="culture-name">{{ culture.name }}</h3>
            </div>
          </div>
        </div>
        
        <!-- Indicador de progreso -->
        <div class="carousel-progress">
          <div class="progress-line">
            <div class="progress-fill" :style="{ width: progressPercentage }"></div>
          </div>
        </div>
        
        <!-- Botones de navegación -->
        <div class="carousel-navigation">
          <button 
            class="nav-button prev-button" 
            @click="scrollCulturesLeft"
            :disabled="culturesCurrentIndex === 0"
            aria-label="Anterior"
          >
            <i class="fas fa-chevron-left"></i>
          </button>
          <button 
            class="nav-button next-button" 
            @click="scrollCulturesRight"
            :disabled="culturesCurrentIndex >= culturesMaxIndex"
            aria-label="Siguiente"
          >
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'RegionalCulturesSection',
  data() {
    return {
      regionalCultures: [
        {
          name: 'Altiplano Andinos',
          description: 'Tradiciones ancestrales y música folklórica',
          image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Altiplano_de_La_Paz_Bolivia.jpg/330px-Altiplano_de_La_Paz_Bolivia.jpg',
          label: 'Altiplano Andinos'
        },
        {
          name: 'Valle Central',
          description: 'Cultura mestiza y gastronomía única',
          image: 'https://upload.wikimedia.org/wikipedia/commons/6/62/Valle_de_las_%C3%A1nimas_La_Paz_Bolivia_%283%29.jpg',
          label: 'Valle Central'
        },
        {
          name: 'Amazonía Boliviana',
          description: 'Rituales ancestrales y biodiversidad cultural',
          image: "https://wp-content.miviaje.com/2019/03/amazonia-boliviana.jpg",
          label: 'Amazonía Boliviana'
        },
        {
          name: 'Chaco Boliviano',
          description: 'Tradiciones ganaderas y música chaqueña',
          image: "https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Rio_Orthon%2C_Pando%2C_Bolivia.jpg/1200px-Rio_Orthon%2C_Pando%2C_Bolivia.jpg",
          label: 'Chaco Boliviano'
        },
        {
          name: 'Oriente Boliviano',
          description: 'Cultura cruceña y carnaval de Santa Cruz',
          image: "https://www.opinion.com.bo/asset/thumbnail,992,558,center,center/media/opinion/images/2023/10/19/2023101915363529824.jpg",
          label: 'Oriente Boliviano'
        }
      ],
      // Variables para el carrusel de culturas
      culturesCurrentIndex: 0,
      culturesCardWidth: 280,
      culturesGap: 20,
      culturesVisibleCards: 4
    }
  },
  computed: {
    culturesMaxIndex() {
      return Math.max(0, this.regionalCultures.length - this.culturesVisibleCards)
    },
    progressPercentage() {
      if (this.regionalCultures.length <= this.culturesVisibleCards) return '100%'
      return `${((this.culturesCurrentIndex + this.culturesVisibleCards) / this.regionalCultures.length) * 100}%`
    }
  },
  methods: {
    scrollCulturesLeft() {
      if (this.culturesCurrentIndex > 0) {
        this.culturesCurrentIndex--
        this.updateCulturesPosition()
      }
    },
    scrollCulturesRight() {
      if (this.culturesCurrentIndex < this.culturesMaxIndex) {
        this.culturesCurrentIndex++
        this.updateCulturesPosition()
      }
    },
    updateCulturesPosition() {
      const carousel = this.$refs.culturesCarousel
      if (carousel) {
        const translateX = -(this.culturesCurrentIndex * (this.culturesCardWidth + this.culturesGap))
        carousel.style.transform = `translateX(${translateX}px)`
      }
    }
  },
  mounted() {
    this.updateCulturesPosition()
  }
}
</script>

<style scoped>
.regional-cultures-section {
  padding: 4rem 0;
  background: var(--bg-primary);
}

.cultures-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.cultures-header {
  text-align: center;
  margin-bottom: 3rem;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.cultures-title {
  font-size: 2.5rem;
  color: var(--text-primary);
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
}

.cultures-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
}

.cultures-carousel-wrapper {
  position: relative;
}

.cultures-carousel {
  display: flex;
  gap: 1.25rem;
  transition: transform 0.3s ease;
  overflow: hidden;
}

.culture-card {
  flex: 0 0 280px;
  background: var(--white);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
}

.culture-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.culture-image {
  height: 180px;
  overflow: hidden;
}

.culture-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.culture-card:hover .culture-image img {
  transform: scale(1.05);
}

.culture-info {
  padding: 1.25rem;
}

.culture-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.carousel-progress {
  margin-top: 2rem;
  display: flex;
  justify-content: center;
}

.progress-line {
  width: 200px;
  height: 4px;
  background: var(--border-light);
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: var(--primary-blue);
  border-radius: 2px;
  transition: width 0.3s ease;
}

.carousel-navigation {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.nav-button {
  width: 44px;
  height: 44px;
  border: 2px solid var(--border-color);
  background: var(--white);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--text-secondary);
  box-shadow: var(--shadow-sm);
}

.nav-button:hover:not(:disabled) {
  border-color: var(--primary-blue);
  color: var(--primary-blue);
  transform: scale(1.1);
  box-shadow: var(--shadow-md);
}

.nav-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 768px) {
  .regional-cultures-section {
    padding: 3rem 0;
  }
  
  .cultures-container {
    padding: 0 1rem;
  }
  
  .cultures-title {
    font-size: 2rem;
  }
  
  .cultures-description {
    font-size: 1rem;
  }
  
  .culture-card {
    flex: 0 0 260px;
  }
  
  .carousel-navigation {
    gap: 0.75rem;
  }
  
  .nav-button {
    width: 40px;
    height: 40px;
  }
}

@media (max-width: 480px) {
  .cultures-title {
    font-size: 1.75rem;
  }
  
  .culture-card {
    flex: 0 0 240px;
  }
  
  .culture-info {
    padding: 1rem;
  }
  
  .culture-name {
    font-size: 1rem;
  }
}
</style>
