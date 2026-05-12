<template>
  <!-- Sección: Mejor época para visitar -->
  <section class="seasons-section">
    <div class="container">
      <div class="seasons-content">
        <h2 class="seasons-title">Cada estación tiene algo especial y una estación extra te da tiempo extra para verlo todo</h2>
        <p class="seasons-subtitle">Consejo experto: Explorar fuera de temporada te ayudará a aliviar la presión.</p>
        
        <div class="seasons-cards">
          <div
            v-for="(season, index) in seasons"
            :key="index"
            class="season-card"
            :class="[season.type, { 'expanded': expandedIndex === index, 'hovered': hoveredIndex === index }]"
            @mouseenter="hoveredIndex = index"
            @mouseleave="hoveredIndex = null"
            @click="toggleExpand(index)"
          >
            <div class="season-card-front">
              <div class="season-icon">
                <i :class="season.icon"></i>
              </div>
              <div class="season-name">{{ season.name }}</div>
              <div class="season-description">{{ season.period }}</div>
              <div class="season-hint">
                <i class="fas fa-info-circle"></i>
                <span>Hover o click para más info</span>
              </div>
            </div>
            
            <!-- Información adicional al expandir -->
            <transition name="expand">
              <div v-if="expandedIndex === index" class="season-card-details">
                <div class="season-details-header">
                  <h3>{{ season.name }}</h3>
                  <button class="close-btn" @click.stop="expandedIndex = null">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                
                <div class="season-details-content">
                  <!-- Descripción principal -->
                  <div class="detail-section description-section" v-if="season.description">
                    <div class="description-content">
                      <i class="fas fa-info-circle"></i>
                      <p>{{ season.description }}</p>
                    </div>
                  </div>
                  
                  <div class="detail-item" v-if="season.temperature">
                    <i class="fas fa-thermometer-half"></i>
                    <div>
                      <span class="detail-label">Temperatura</span>
                      <span class="detail-value">{{ season.temperature }}</span>
                    </div>
                  </div>
                  
                  <div class="detail-item" v-if="season.precipitation">
                    <i class="fas fa-cloud-rain"></i>
                    <div>
                      <span class="detail-label">Precipitación</span>
                      <span class="detail-value">{{ season.precipitation }}</span>
                    </div>
                  </div>
                  
                  <div class="detail-item" v-if="season.humidity">
                    <i class="fas fa-tint"></i>
                    <div>
                      <span class="detail-label">Humedad</span>
                      <span class="detail-value">{{ season.humidity }}</span>
                    </div>
                  </div>
                  
                  <div class="detail-section" v-if="season.activities && season.activities.length">
                    <h4><i class="fas fa-map-marked-alt"></i> Actividades Recomendadas</h4>
                    <ul class="activities-list">
                      <li v-for="(activity, actIndex) in season.activities" :key="actIndex">
                        <i class="fas fa-check-circle"></i>
                        {{ activity }}
                      </li>
                    </ul>
                  </div>
                  
                  <div class="detail-section" v-if="season.tips && season.tips.length">
                    <h4><i class="fas fa-lightbulb"></i> Consejos de Viaje</h4>
                    <ul class="tips-list">
                      <li v-for="(tip, tipIndex) in season.tips" :key="tipIndex">
                        <i class="fas fa-star"></i>
                        {{ tip }}
                      </li>
                    </ul>
                  </div>
                  
                  <div class="detail-section" v-if="season.bestFor">
                    <h4><i class="fas fa-heart"></i> Ideal Para</h4>
                    <p class="best-for-text">{{ season.bestFor }}</p>
                  </div>
                </div>
              </div>
            </transition>
            
            <!-- Tooltip al hacer hover -->
            <transition name="tooltip">
              <div v-if="hoveredIndex === index && expandedIndex !== index" class="season-tooltip">
                <div class="tooltip-content">
                  <i :class="season.icon"></i>
                  <p>{{ season.quickInfo }}</p>
                  <span class="tooltip-hint">Click para ver más detalles</span>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'PlanningSeasons',
  setup() {
    const expandedIndex = ref(null)
    const hoveredIndex = ref(null)
    
    const seasons = [
      {
        type: 'spring',
        name: 'Primavera',
        period: 'Marzo - Mayo',
        icon: 'fas fa-seedling',
        temperature: '15°C - 25°C',
        precipitation: 'Baja a moderada',
        humidity: '50% - 70%',
        quickInfo: 'Temperaturas agradables y días soleados. Perfecto para senderismo y turismo.',
        description: 'Transición de la temporada lluviosa a la seca, con valles y montañas llenos de vegetación y flores, la Amazonía comienza a secarse un poco y los salares se muestran con cielos despejados.',
        activities: [
          'Observación de aves',
          'Senderismo en montañas',
          'Visitas a mercados locales',
          'Festivales culturales'
        ],
        tips: [
          'Lleva ropa ligera pero prepara una chaqueta para las noches',
          'Es la mejor época para visitar el Salar de Uyuni',
          'Las lluvias son esporádicas, lleva un paraguas'
        ],
        bestFor: 'Turismo cultural, fotografía, actividades al aire libre y visitas a sitios arqueológicos.'
      },
      {
        type: 'summer',
        name: 'Verano',
        period: 'Junio - Agosto',
        icon: 'fas fa-sun',
        temperature: '10°C - 20°C',
        precipitation: 'Muy baja',
        humidity: '30% - 50%',
        quickInfo: 'Temporada seca. Días despejados y excelente visibilidad en los Andes.',
        description: 'Temporada seca ideal para trekking y excursiones de montaña, con cielos despejados en el altiplano y los Andes y clima estable para la Amazonía.',
        activities: [
          'Escalada de montañas',
          'Tours al Salar de Uyuni',
          'Visitas a lagos altiplánicos',
          'Ciclismo y deportes extremos'
        ],
        tips: [
          'Protector solar es esencial debido a la alta altitud',
          'Las noches son muy frías, lleva ropa abrigada',
          'Hidrátate constantemente por la sequedad del aire'
        ],
        bestFor: 'Aventura, fotografía de paisajes, turismo extremo y visitas a sitios naturales.'
      },
      {
        type: 'autumn',
        name: 'Otoño',
        period: 'Septiembre - Noviembre',
        icon: 'fas fa-leaf',
        temperature: '12°C - 22°C',
        precipitation: 'Moderada',
        humidity: '45% - 65%',
        quickInfo: 'Clima templado y estable. Época ideal para explorar diferentes regiones.',
        description: 'Climas templados y paisajes despejados que facilitan recorrer valles y montañas.',
        activities: [
          'Trekking por diferentes altitudes',
          'Visitas a comunidades indígenas',
          'Festivales tradicionales',
          'Turismo gastronómico'
        ],
        tips: [
          'Ropa por capas es ideal para cambios de temperatura',
          'Buena época para combinar costa y montaña',
          'Menos turistas que en verano'
        ],
        bestFor: 'Turismo cultural, gastronomía, experiencias locales y exploración de regiones diversas.'
      },
      {
        type: 'winter',
        name: 'Invierno',
        period: 'Diciembre - Febrero',
        icon: 'fas fa-snowflake',
        temperature: '5°C - 18°C',
        precipitation: 'Alta en zonas altas',
        humidity: '60% - 80%',
        quickInfo: 'Temporada de lluvias en el altiplano. Nevadas en montañas altas.',
        description: 'Temporada lluviosa en la Amazonía y el oriente, los Andes y salares se mantienen secos y fríos, ideales para trekking y excursiones.',
        activities: [
          'Esquí en Chacaltaya (cuando hay nieve)',
          'Termas y aguas termales',
          'Turismo cultural en ciudades',
          'Visitas a museos y sitios cubiertos'
        ],
        tips: [
          'Ropa impermeable y abrigada es esencial',
          'Algunos caminos pueden estar cerrados',
          'Mejor época para termas y aguas calientes',
          'Reserva con anticipación en zonas turísticas'
        ],
        bestFor: 'Turismo urbano, cultura, relax en termas y experiencias gastronómicas bajo techo.'
      },
      {
        type: 'dry',
        name: 'Temporada Seca',
        period: 'Mayo - Octubre',
        icon: 'fas fa-mountain',
        temperature: '8°C - 22°C',
        precipitation: 'Mínima',
        humidity: '25% - 45%',
        quickInfo: 'La mejor época para visitar Bolivia. Clima seco y estable en todo el país.',
        description: 'La mejor época para exploración por los cielos despejados y condiciones óptimas en todo el país.',
        activities: [
          'Todas las actividades al aire libre',
          'Salar de Uyuni en su mejor momento',
          'Amazonía accesible',
          'Trekking y montañismo'
        ],
        tips: [
          'La época más popular, reserva con anticipación',
          'Días soleados y despejados garantizados',
          'Mejor visibilidad para fotografía',
          'Temperaturas extremas entre día y noche'
        ],
        bestFor: 'Todas las actividades turísticas. La temporada más recomendada para visitar Bolivia.'
      }
    ]
    
    const toggleExpand = (index) => {
      expandedIndex.value = expandedIndex.value === index ? null : index
    }
    
    return {
      seasons,
      expandedIndex,
      hoveredIndex,
      toggleExpand
    }
  }
}
</script>

<style scoped>
/* ===============================
   Sección de Estaciones
   =============================== */
.seasons-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.seasons-content {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.seasons-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  text-align:left ;
}

.seasons-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 3rem;
  max-width: 900px;
  text-align:left ;
}

.seasons-cards {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 1.5rem;
  margin-top: 2rem;
}

.season-card {
  background: var(--white);
  border-radius: 20px;
  padding: 2rem 1.5rem;
  text-align: center;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  border: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
  position: relative;
  overflow: visible;
  min-height: 200px;
  display: flex;
  flex-direction: column;
}

.season-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  transition: all 0.3s ease;
  z-index: 1;
}

.season-card:hover:not(.expanded) {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 12px 40px rgba(0,0,0,0.15);
  border-radius: 70px 30px 70px 30px;
  z-index: 10;
}

.season-card.expanded {
  min-height: auto;
  z-index: 20;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  transform: translateY(-8px) scale(1.03);
}

.season-card-front {
  position: relative;
  z-index: 2;
  transition: opacity 0.3s ease;
}

.season-card.expanded .season-card-front {
  opacity: 0;
  height: 0;
  overflow: hidden;
}

.season-hint {
  margin-top: 1rem;
  font-size: 0.75rem;
  color: var(--text-secondary);
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.season-card:hover .season-hint {
  opacity: 1;
  transform: translateY(0);
}

.season-hint i {
  font-size: 0.7rem;
}

.season-card.spring {
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.season-card.spring::before {
  background: var(--bolivia-green);
}

.season-card.summer {
  background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
}

.season-card.summer::before {
  background: var(--bolivia-yellow);
}

.season-card.autumn {
  background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
}

.season-card.autumn::before {
  background: var(--bolivia-purple);
}

.season-card.winter {
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
}

.season-card.winter::before {
  background: var(--primary-blue);
}

.season-card.dry {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.season-card.dry::before {
  background: var(--text-muted);
}

.season-icon {
  font-size: 2.5rem;
  color: var(--text-primary);
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.season-card:hover .season-icon {
  transform: scale(1.1);
  color: var(--primary-blue);
}

.season-name {
  font-size: 1.25rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  letter-spacing: -0.01em;
}

.season-description {
  font-size: 0.95rem;
  color: var(--text-secondary);
  font-weight: var(--font-weight-medium);
}

/* ===============================
   Tooltip al hacer hover
   =============================== */
.season-tooltip {
  position: absolute;
  top: -120px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 15;
  pointer-events: none;
  width: 280px;
}

.tooltip-content {
  background: white;
  padding: 1.25rem;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.15);
  text-align: center;
  position: relative;
  border: 2px solid var(--primary-blue, #007bff);
}

.tooltip-content::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
  border-top: 10px solid white;
}

.tooltip-content i {
  font-size: 2rem;
  color: var(--primary-blue, #007bff);
  margin-bottom: 0.5rem;
  display: block;
}

.tooltip-content p {
  font-size: 0.9rem;
  color: var(--text-primary);
  margin: 0.5rem 0;
  line-height: 1.5;
}

.tooltip-hint {
  font-size: 0.75rem;
  color: var(--text-secondary);
  font-style: italic;
}

/* ===============================
   Detalles expandidos
   =============================== */
.season-card-details {
  position: relative;
  width: 100%;
  animation: expandIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes expandIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.season-details-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--border-light);
}

.season-details-header h3 {
  font-size: 1.5rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin: 0;
}

.close-btn {
  background: transparent;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--text-secondary);
}

.close-btn:hover {
  background: var(--bg-secondary);
  color: var(--text-primary);
  transform: rotate(90deg);
}

.season-details-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(255,255,255,0.5);
  border-radius: 12px;
  transition: all 0.3s ease;
}

.detail-item:hover {
  background: rgba(255,255,255,0.8);
  transform: translateX(5px);
}

.detail-item i {
  font-size: 1.5rem;
  color: var(--primary-blue, #007bff);
  width: 40px;
  text-align: center;
}

.detail-item div {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.detail-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.detail-value {
  font-size: 1rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
}

.detail-section {
  padding: 1.25rem;
  background: rgba(255,255,255,0.3);
  border-radius: 12px;
  border-left: 4px solid var(--primary-blue, #007bff);
}

.description-section {
  background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.05) 100%);
  border-left: 4px solid var(--primary-blue, #007bff);
  margin-bottom: 0.5rem;
}

.description-content {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.description-content i {
  font-size: 1.5rem;
  color: var(--primary-blue, #007bff);
  margin-top: 0.25rem;
  flex-shrink: 0;
}

.description-content p {
  font-size: 1rem;
  color: var(--text-primary);
  line-height: 1.7;
  margin: 0;
  font-weight: var(--font-weight-medium, 500);
}

.detail-section h4 {
  font-size: 1.1rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin: 0 0 1rem 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.detail-section h4 i {
  color: var(--primary-blue, #007bff);
}

.activities-list,
.tips-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.activities-list li,
.tips-list li {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  font-size: 0.9rem;
  color: var(--text-primary);
  line-height: 1.5;
}

.activities-list li i {
  color: var(--bolivia-green, #10b981);
  margin-top: 0.2rem;
  flex-shrink: 0;
}

.tips-list li i {
  color: var(--bolivia-yellow, #f59e0b);
  margin-top: 0.2rem;
  flex-shrink: 0;
}

.best-for-text {
  font-size: 0.95rem;
  color: var(--text-primary);
  line-height: 1.6;
  margin: 0;
  padding: 1rem;
  background: rgba(255,255,255,0.5);
  border-radius: 8px;
}

/* ===============================
   Transiciones
   =============================== */
.expand-enter-active,
.expand-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.expand-enter-from,
.expand-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-10px);
}

.tooltip-enter-active,
.tooltip-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.tooltip-enter-from,
.tooltip-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(10px);
}

/* Responsive para estaciones */
@media (max-width: 991.98px) {
  .seasons-cards {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
  }
  
  .seasons-title {
    font-size: 2.5rem;
    line-height: 1.15;
  }
  
  .seasons-subtitle {
    font-size: 1rem;
  }
  
  .season-card {
    padding: 1.5rem 1rem;
    min-height: 180px;
  }
  
  .season-card.expanded {
    min-height: auto;
  }
  
  .season-icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
  }
  
  .season-name {
    font-size: 1.125rem;
  }
  
  .season-description {
    font-size: 0.9rem;
  }
  
  .season-tooltip {
    width: 240px;
    top: -110px;
  }
  
  .season-details-header h3 {
    font-size: 1.25rem;
  }
  
  .detail-item {
    padding: 0.75rem;
  }
  
  .detail-section {
    padding: 1rem;
  }
}

@media (max-width: 768px) {
  .seasons-cards {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .seasons-title {
    font-size: 2rem;
  }
  
  .seasons-subtitle {
    font-size: 0.95rem;
    margin-bottom: 2rem;
  }
  
  .season-card {
    padding: 1.25rem 0.75rem;
    min-height: 160px;
  }
  
  .season-card.expanded {
    min-height: auto;
  }
  
  .season-icon {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
  }
  
  .season-name {
    font-size: 1rem;
  }
  
  .season-description {
    font-size: 0.85rem;
  }
  
  .season-tooltip {
    width: 200px;
    top: -100px;
  }
  
  .tooltip-content {
    padding: 1rem;
  }
  
  .tooltip-content p {
    font-size: 0.85rem;
  }
  
  .season-details-header h3 {
    font-size: 1.1rem;
  }
  
  .detail-item {
    padding: 0.75rem;
    gap: 0.75rem;
  }
  
  .detail-item i {
    font-size: 1.25rem;
    width: 30px;
  }
  
  .detail-value {
    font-size: 0.9rem;
  }
  
  .detail-section {
    padding: 0.875rem;
  }
  
  .detail-section h4 {
    font-size: 1rem;
  }
  
  .activities-list li,
  .tips-list li {
    font-size: 0.85rem;
  }
}

@media (max-width: 575.98px) {
  .seasons-section {
    padding: 3rem 0;
  }
  
  .seasons-cards {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
  
  .seasons-title {
    font-size: 1.75rem;
    line-height: 1.2;
  }
  
  .seasons-subtitle {
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }
  
  .season-card {
    padding: 1rem;
    border-radius: 16px;
  }
  
  .season-icon {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }
  
  .season-name {
    font-size: 0.95rem;
  }
  
  .season-description {
    font-size: 0.8rem;
  }
}
</style>
