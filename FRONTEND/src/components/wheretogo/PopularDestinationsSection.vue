<template>
  <section class="destinations">
    <div class="container">
      <h2 class="section-title">Destinos populares</h2>
      <div class="row g-4">
        <div class="col-md-6" v-for="destination in popularDestinations" :key="destination.id">
          <router-link
            :to="`/lugar/${destination.id}`"
            class="destination-card"
            :aria-label="`Ver detalles de ${destination.nombre}`"
          >
            <div class="destination-image">
              <img :src="destination.image" :alt="destination.nombre" class="destination-img" loading="lazy" />
              <span class="destination-badge">Destacado</span>
            </div>
            <div class="destination-content">
              <h3 class="destination-title">{{ destination.nombre }}</h3>
              <p class="destination-location">{{ destination.ubicacion }}</p>
              <div class="destination-tags" v-if="destination.tags && destination.tags.length">
                <span v-for="tag in destination.tags" :key="tag" class="tag">{{ tag }}</span>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PopularDestinationsSection',
  data() {
    return {
      popularDestinations: [
        {
          id: 1,
          nombre: "Salar de Uyuni",
          ubicacion: "Potosí, Altiplano",
          descripcion: "El salar más grande del mundo, un desierto blanco infinito que refleja el cielo como un espejo gigante.",
          image: "https://images.myguide-cdn.com/bolivia/companies/from-uyuni-3-day-salt-flats-and-lagoons-tour-to-san-pedro/large/from-uyuni-3-day-salt-flats-and-lagoons-tour-to-san-pedro-4525240.jpg",
          tags: ["Naturaleza", "Fotografía", "Aventura"]
        },
        {
          id: 17,
          nombre: "La Paz",
          ubicacion: "La Paz, Altiplano",
          descripcion: "La capital administrativa más alta del mundo, con una mezcla única de tradición andina y modernidad.",
          image: "https://upload.wikimedia.org/wikipedia/commons/4/4f/Altiplano_de_La_Paz_Bolivia.jpg",
          tags: ["Cultura", "Ciudad", "Gastronomía"]
        },
        {
          id: 4,
          nombre: "Sucre",
          ubicacion: "Chuquisaca, Valles",
          descripcion: "La ciudad blanca, capital constitucional con arquitectura colonial perfectamente conservada.",
          image: "https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Vista_de_Sopachuy.jpg/1100px-Vista_de_Sopachuy.jpg",
          tags: ["Historia", "Arquitectura", "Cultura"]
        },
        {
          id: 6,
          nombre: "Santa Cruz",
          ubicacion: "Santa Cruz, Llanos",
          descripcion: "La ciudad más grande de Bolivia, centro económico con clima tropical y gastronomía única.",
          image: "https://cdn.bolivia.com/sdi/2022/09/20/los-mejores-lugares-turisticos-de-santa-cruz-1068076.jpg",
          tags: ["Ciudad", "Gastronomía", "Comercio"]
        }
      ]
    }
  }
}
</script>

<style scoped>
/* Destinations section */
.destinations {
  padding: 4rem 0;
  background: var(--bg-primary);
}

.section-title {
  font-size: 2rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  margin-bottom: 2rem;
  font-family: var(--font-family-base) !important;
  text-align: center;
  letter-spacing: -0.02em;
}

.destination-card {
  display: block;
  text-decoration: none;
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s ease;
  height: 100%;
  box-shadow: var(--shadow-sm);
}

.destination-card:hover {
  border-color: var(--border-color);
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.destination-image { 
  position: relative; 
  height: 220px; 
  overflow: hidden; 
}

.destination-img { 
  width: 100%; 
  height: 100%; 
  object-fit: cover; 
  display: block; 
  transition: transform 0.4s ease; 
}

.destination-card:hover .destination-img { 
  transform: scale(1.04); 
}

.destination-badge { 
  position: absolute; 
  top: 12px; 
  left: 12px; 
  background: rgba(255,255,255,0.92); 
  border: 1px solid var(--border-light); 
  color: var(--text-primary); 
  padding: 0.35rem 0.6rem; 
  border-radius: 999px; 
  font-size: 0.75rem; 
  font-weight: 500; 
  box-shadow: var(--shadow-sm); 
}

.destination-content {
  padding: 1.5rem;
}

.destination-title {
  font-size: 1.25rem !important;
  font-weight: var(--font-weight-semibold) !important;
  color: var(--text-primary) !important;
  margin-bottom: 0.5rem;
  font-family: var(--font-family-base) !important;
}

.destination-location {
  font-size: 0.875rem !important;
  color: var(--text-secondary) !important;
  margin-bottom: 1rem;
  font-family: var(--font-family-base) !important;
  font-weight: var(--font-weight-medium) !important;
}

.destination-tags { 
  display: flex; 
  flex-wrap: wrap; 
  gap: 0.5rem; 
}

.tag { 
  background: var(--bg-secondary); 
  color: var(--text-secondary); 
  border: 1px solid var(--border-light); 
  border-radius: 999px; 
  padding: 0.25rem 0.6rem; 
  font-size: 0.75rem; 
  font-weight: 500; 
}

/* Responsive */
@media (max-width: 767.98px) {
  .destinations {
    padding: 3rem 0;
  }
  
  .section-title {
    font-size: 1.75rem !important;
  }
  
  .destination-content {
    padding: 1.25rem;
  }
}
</style>

