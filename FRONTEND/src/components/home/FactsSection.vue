<template>
  <section class="facts-section">
    <div class="container">
      <h2 class="section-title">Datos fascinantes sobre Bolivia</h2>
      <div class="facts-container">
        <div
          class="facts-grid"
        >
          <div v-for="fact in facts" :key="fact.id" class="fact-item">
            <h4 class="fact-title">{{ fact.title }}</h4>
            <p class="fact-text">{{ fact.text }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'FactsSection',
  data() {
    return {
      facts: [
        { 
          id: 1, 
          title: 'Clima', 
          text: 'Bolivia es un país de gran diversidad geográfica, con climas que van del tropical amazónico al frío altiplánico y al templado de valles y llanuras.' 
        },
        { 
          id: 2, 
          title: 'Diversidad Lingüística', 
          text: 'Bolivia cuenta con 36 lenguas indígenas reconocidas, además del español, un récord mundial de diversidad lingüística.' 
        },
        { 
          id: 3, 
          title: 'Cultura viva', 
          text: 'Bolivia tiene la mayor población indígena de Sudamérica, con 36 pueblos que representan el 62% de la población.' 
        },
        { 
          id: 4, 
          title: 'Biodiversidad', 
          text: 'Bolivia cuenta con 22 parques nacionales y 11 sitios Ramsar que resguardan su diversidad biológica y paisajes.' 
        },
        { 
          id: 5, 
          title: 'Capital', 
          text: 'Bolivia tiene dos capitales, Sucre es la capital constitucional y La Paz la capital administrativa.' 
        },
        { 
          id: 6, 
          title: 'Altitud', 
          text: 'La Paz es la capital más alta del mundo, situada a más de 3.500 metros sobre el nivel del mar.' 
        }
      ],
      // Estado drag
      isDragging: false,
      startX: 0,
      scrollLeft: 0
    }
  },
  methods: {},
  mounted() {},
  beforeUnmount() {}
}
</script>

<style scoped>
/* Facts */
.facts-section { 
  padding: 4rem 0; 
}

.section-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: 3rem;
  letter-spacing: -0.01em;
}

.facts-container {
  position: relative;
}

.facts-grid { 
  display: grid; 
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.fact-item { 
  background: var(--white); 
  border: 1px solid var(--border-light); 
  border-radius: 16px; 
  padding: 1.5rem; 
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
  min-width: 280px;
}

.fact-item:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-color: var(--border-color);
}

.fact-title { 
  margin: 0 0 0.75rem; 
  font-weight: 600; 
  font-size: 1.125rem;
  color: var(--text-primary);
}

.fact-text { 
  margin: 0; 
  color: var(--text-secondary);
  line-height: 1.6;
}

/* Indicador de scroll y flechas (alineado con EventsSection) */
.scroll-indicator { display: flex; align-items: center; gap: 1rem; margin-top: 1rem; }
.scroll-track { flex: 1; height: 2px; background: var(--border-light); border-radius: 1px; position: relative; overflow: hidden; }
.scroll-progress { position: absolute; top: 0; left: 0; height: 100%; background: var(--primary-blue); border-radius: 1px; width: 25%; transition: width 0.3s ease; }
.scroll-arrows { display: flex; gap: 0.5rem; }
.scroll-arrow { width: 40px; height: 40px; border: 1px solid var(--border-light); background: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; color: var(--text-secondary); font-size: 0.875rem; }
.scroll-arrow:hover { background: var(--primary-blue); color: var(--white); border-color: var(--primary-blue); transform: scale(1.05); }
.scroll-arrow:active { transform: scale(0.95); }

/* Responsive */
@media (max-width: 768px) {
  .section-title {
    font-size: 2rem;
  }
}
</style>
