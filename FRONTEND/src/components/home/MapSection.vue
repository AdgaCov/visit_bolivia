<template>
  <section class="map-section">
    <div class="container">
      <div class="map-grid">
        <div class="map-hero-content">
          <h2 class="section-title">{{ title || 'Empieza a planear' }}</h2>
          <p class="map-subtitle">{{ subtitle || 'Usa el mapa interactivo para descubrir destinos, actividades y diseñar tu plan de viaje' }}</p>
          <div class="map-cta">
            <router-link to="/mapa" class="cta-btn">
              Explorar mapa interactivo
              <i class="fas fa-arrow-right"></i>
            </router-link>
          </div>
          <div class="map-legend side">
            <div class="legend-chip">
              <span class="dot cultura"></span>
              <span>Cultura & Historia</span>
            </div>
            <div class="legend-chip">
              <span class="dot naturaleza"></span>
              <span>Naturaleza</span>
            </div>
            <div class="legend-chip">
              <span class="dot aventura"></span>
              <span>Aventura</span>
            </div>
            <div class="legend-chip">
              <span class="dot gastronomia"></span>
              <span>Gastronomía</span>
            </div>
          </div>
        </div>

        <div class="map-card">
          <div class="map-card-inner,map-embed">
            <iframe
            class="map-embed"
            src="https://www.google.com/maps/d/embed?mid=1yi1cTOKRITfcwbC_4R9uX80cRAWovqc&ehbc=2E312F&noprof=1"
            title="Mapa de lugares destacados de Bolivia"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
            <!-- <InteractiveMap 
              :lugares="lugaresTuristicos" 
              :center="[-16.5, -68.15]" 
              :zoom="6" 
              height="520px" 
            /> -->
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import InteractiveMap from '@/components/InteractiveMap.vue'

export default {
  name: 'MapSection',
  components: {
    InteractiveMap
  },
  props: {
    title: {
      type: String,
      default: ''
    },
    subtitle: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      lugaresTuristicos: [
        {
          id: 1,
          nombre: 'Salar de Uyuni',
          descripcion: 'El desierto de sal más grande del mundo, un espejo natural que refleja el cielo',
          imagenes: ['/images/salar-uyuni.jpg'],
          region: 'Potosí',
          coordenadas: [-20.1339, -67.4891],
          destacado: true
        },
        {
          id: 2,
          nombre: 'Tiwanaku',
          descripcion: 'Sitio arqueológico precolombino, centro espiritual de la cultura Tiwanaku',
          imagenes: ['/images/tiwanaku.jpg'],
          region: 'La Paz',
          coordenadas: [-16.5547, -68.6734],
          destacado: true
        },
        {
          id: 3,
          nombre: 'Lago Titicaca',
          descripcion: 'El lago navegable más alto del mundo, cuna de civilizaciones andinas',
          imagenes: ['/images/titicaca.jpg'],
          region: 'La Paz',
          coordenadas: [-16.1667, -69.0833],
          destacado: true
        },
        {
          id: 4,
          nombre: 'Cristo de la Concordia',
          descripcion: 'Estatua de Cristo más alta del mundo, ubicada en el cerro San Pedro',
          imagenes: ['/images/cristo-cochabamba.jpg'],
          region: 'Cochabamba',
          coordenadas: [-17.3895, -66.1568],
          destacado: false
        },
        {
          id: 5,
          nombre: 'Parque Nacional Amboró',
          descripcion: 'Reserva natural con selva amazónica, cascadas y biodiversidad excepcional',
          imagenes: ['/images/amboro.jpg'],
          region: 'Santa Cruz',
          coordenadas: [-18.1667, -63.8333],
          destacado: false
        },
        {
          id: 6,
          nombre: 'Carnaval de Oruro',
          descripcion: 'Fiesta folklórica declarada Patrimonio de la Humanidad por la UNESCO',
          imagenes: ['/images/carnaval-oruro.jpg'],
          region: 'Oruro',
          coordenadas: [-17.9754, -67.1105],
          destacado: true
        },
        {
          id: 7,
          nombre: 'Cerro Rico',
          descripcion: 'Montaña histórica de la plata, símbolo de la riqueza colonial',
          imagenes: ['/images/cerro-rico.jpg'],
          region: 'Potosí',
          coordenadas: [-19.5833, -65.75],
          destacado: true
        },
        {
          id: 8,
          nombre: 'Valle de Tarija',
          descripcion: 'Tierra del vino y el singani, con clima mediterráneo y paisajes de viñedos',
          imagenes: ['/images/valle-tarija.jpg'],
          region: 'Tarija',
          coordenadas: [-21.5355, -64.7296],
          destacado: false
        }
      ]
    }
  }
}
</script>

<style scoped>
/* Sección del Mapa */
.map-section {
  padding: 4rem 0;
  background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
  border-bottom: 1px solid var(--border-light);
}

.map-hero {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}
.map-grid {
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  gap: 2rem;
  align-items: center;
}


.map-hero-content {
  text-align: center;
  max-width: 860px;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  letter-spacing: -0.01em;
}

.map-subtitle {
  color: var(--text-secondary);
  font-size: 1.125rem;
  margin-bottom: 1rem;
}

.map-cta {
  display: flex;
  justify-content: center;
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--primary-blue);
  color: var(--white);
  text-decoration: none;
  padding: 0.75rem 1.25rem;
  border-radius: 10px;
  transition: all 0.25s ease;
}

.cta-btn:hover {
  background: var(--primary-blue-dark);
  transform: translateY(-2px);
}

.map-card {
  background: var(--white);
  border-radius: 16px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-light);
  overflow: hidden;
}

.map-card-inner,.map-embed {
  padding: 1rem;
  width: 100%;
  min-height: 520px;
}

/* Leyenda del Mapa */
.map-legend {
  display: flex;
  justify-content: center;
  gap: 0.75rem;
  flex-wrap: wrap;
  padding: 0.75rem 1rem 1.25rem;
}

.legend-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.875rem;
  border: 1px solid var(--border-light);
  background: var(--bg-primary);
  color: var(--text-secondary);
  border-radius: 999px;
  font-size: 0.85rem;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  box-shadow: var(--shadow-sm);
}

.dot.cultura { background: var(--category-cultura); }
.dot.naturaleza { background: var(--category-naturaleza); }
.dot.aventura { background: var(--category-aventura); }
.dot.gastronomia { background: var(--category-gastronomia); }

/* Responsive */
@media (max-width: 768px) {
  .map-grid { grid-template-columns: 1fr; gap: 1.25rem; }
  .map-card-inner { min-height: 420px; padding: 0.75rem; }
  .section-title { font-size: 2rem; }
  .legend-chip { font-size: 0.8rem; }
}
</style>
