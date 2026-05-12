<template>
  <section class="planning-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-6">
          <div class="planning-copy">
            <h2 class="planning-title">Empieza a planificar</h2>
            <p class="planning-subtitle">Usa nuestro mapa interactivo para encontrar destinos, descubrir nuevas actividades y diseñar un plan de viaje.</p>
            <router-link to="/lugares" class="planning-cta">Explorar Bolivia</router-link>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="planning-illustration">
            <InteractiveMap 
              :lugares="mapData" 
              :center="mapCenter" 
              :zoom="mapZoom" 
              height="320px" 
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import InteractiveMap from '@/components/InteractiveMap.vue'
import { lugaresTuristicos } from '@/assets/data/lugares.js'

export default {
  name: 'PlanningHeroSection',
  components: {
    InteractiveMap
  },
  computed: {
    mapData() {
      return lugaresTuristicos.slice(0, 10); // Mostrar solo los primeros 10 lugares
    },
    mapCenter() {
      return [-16.5, -68.15]; // Centro de Bolivia
    },
    mapZoom() {
      return 6; // Vista general de Bolivia
    }
  }
}
</script>

<style scoped>
/* Planning hero */
.planning-hero {
  background: var(--white);
  padding: 4rem 0;
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.planning-copy {
  padding-right: 1rem;
}

.planning-title {
  font-size: 2.5rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 1rem;
  letter-spacing: -0.02em;
  font-family: var(--font-family-base);
}

.planning-subtitle {
  color: var(--text-secondary);
  font-size: 1.125rem;
  margin-bottom: 1.75rem;
  font-family: var(--font-family-base);
  line-height: 1.6;
}

.planning-cta {
  display: inline-block;
  background: var(--light-gray);
  color: var(--text-primary);
  padding: 0.875rem 1.5rem;
  border-radius: 10px;
  text-decoration: none;
  font-weight: var(--font-weight-medium);
  box-shadow: var(--shadow-sm);
  transition: all 0.2s ease;
  border: 1px solid var(--border-color);
  font-family: var(--font-family-base);
}

.planning-cta:hover {
  background: var(--medium-gray);
  color: var(--text-primary);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.planning-illustration {
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}

.planning-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

@media (max-width: 991.98px) {
  .planning-title { 
    font-size: 2rem; 
  }
  
  .planning-subtitle { 
    font-size: 1rem; 
  }
  
  .planning-copy {
    padding-right: 0;
    margin-bottom: 2rem;
  }
}
</style>

