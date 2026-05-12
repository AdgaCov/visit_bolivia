<template>
  <section class="itineraries section light">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title">Itinerarios recomendados</h3>
        <router-link to="/planificacion" class="btn-read">Ver todos</router-link>
      </div>
      <div class="row g-4">
        <div class="col-md-4" v-for="it in itineraries" :key="it.slug">
          <router-link class="itin-card" :to="it.to">
            <div class="itin-media" :style="{ backgroundImage: `url('${it.image}')` }"></div>
            <div class="itin-body">
              <h4 class="itin-title">{{ it.title }}</h4>
              <p class="itin-text">{{ it.text }}</p>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ItinerariesWhatToDoSection',
  data() {
    return {
      itineraries: [
        { title: '3 días en La Paz', slug: 'la-paz-3-dias', to: '/planificacion/itinerarios/la-paz-3-dias', image: 'https://www.todoturismosrl.com/images/283603-la-paz2.jpg', text: 'Altiplano, cultura y miradores.' },
        { title: 'Selva y aventura', slug: 'selva-aventura', to: '/planificacion/itinerarios/selva-aventura', image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/naturaleza/6.jpg', text: 'Ríos, fauna y caminatas.' },
        { title: 'Salar esencial', slug: 'salar-esencial', to: '/planificacion/itinerarios/salar-esencial', image: 'https://i0.wp.com/quetedenpasaporte.com/wp-content/uploads/2019/01/Salar-de-Uyuni.jpg?fit=1199%2C675&ssl=1', text: 'Lagunas, salares y estrellas.' }
      ]
    }
  }
}
</script>

<style scoped>
.section { 
  padding: 4rem 0;
  font-family: var(--font-family-base) !important;
}

.section.light { background: var(--bg-secondary); }

.section-title { 
  font-size: 2.75rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin-bottom: 1.25rem;
  font-family: var(--font-family-base) !important;
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
}

.itin-card { 
  display: block; 
  text-decoration: none; 
  color: inherit; 
  background: var(--white); 
  border-radius: 16px; 
  box-shadow: var(--shadow-sm); 
  overflow: hidden; 
  transition: all 0.3s ease;
}

.itin-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  text-decoration: none;
  color: inherit;
}

.itin-media { 
  height: 160px; 
  background-size: cover; 
  background-position: center; 
}

.itin-body { 
  padding: 1rem; 
}

.itin-title { 
  font-size: 1.125rem; 
  font-weight: var(--font-weight-semibold); 
  margin-bottom: .25rem; 
  color: var(--text-primary);
  font-family: var(--font-family-base) !important;
}

.itin-text { 
  color: var(--text-secondary); 
  font-family: var(--font-family-base) !important;
}

/* Responsive */
@media (max-width: 991.98px) {
  .btn-read {
    margin-top: 1rem;
  }
}
</style>
