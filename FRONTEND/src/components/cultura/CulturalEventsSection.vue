<template>
  <section class="cultural-events-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">{{ titleText }}</h2>
        <p v-if="subtitleText" class="section-subtitle">{{ subtitleText }}</p>
      </div>
      
      <div class="events-grid">
        <div class="event-card" v-for="event in renderedEvents" :key="event.id">
          <div class="event-image">
            <img :src="event.image" :alt="event.name" loading="lazy">
            <div class="event-date">
              <span class="month">{{ event.month }}</span>
              <span class="day">{{ event.day }}</span>
            </div>
          </div>
          <div class="event-content">
            <h3 class="event-name">{{ event.name }}</h3>
            <p class="event-description">{{ event.description }}</p>
            <div class="event-location">
              <i class="fas fa-map-marker-alt"></i>
              <span>{{ event.location }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'CulturalEventsSection',
  props: {
    events: {
      type: Array,
      required: false,
      default: null
    },
    title: {
      type: String,
      required: false,
      default: 'Eventos Culturales'
    },
    subtitle: {
      type: String,
      required: false,
      default: 'Descubre las festividades y eventos culturales más importantes de Bolivia'
    }
  },
  data() {
    return {
      fallbackEvents: [
        {
          id: 1,
          name: 'Carnaval de Oruro',
          description: 'La festividad más importante de Bolivia, declarada Obra Maestra del Patrimonio Oral e Intangible de la Humanidad',
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/37.jpg',
          location: 'Oruro',
          month: 'FEB',
          day: '15'
        },
        {
          id: 2,
          name: 'Fiesta de la Virgen de Urkupiña',
          description: 'Una de las festividades religiosas más importantes del país',
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/38.jpeg',
          location: 'Quillacollo',
          month: 'AGO',
          day: '15'
        },
        {
          id: 3,
          name: 'Gran Poder',
          description: 'Festividad andina que combina tradición y modernidad en La Paz',
          image: 'http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/36.jpg',
          location: 'La Paz',
          month: 'MAY',
          day: '25'
        }
      ]
    }
  },
  computed: {
    renderedEvents() {
      return Array.isArray(this.events) && this.events.length > 0 ? this.events : this.fallbackEvents
    },
    titleText() {
      return this.title
    },
    subtitleText() {
      return this.subtitle || null
    }
  }
}
</script>

<style scoped>
.cultural-events-section { padding: 4rem 0; }

.section-header {
  text-align: center;
  margin-bottom: 3rem;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: 3rem;
  letter-spacing: -0.01em;
}

.section-subtitle { font-size: 1.125rem; color: var(--text-secondary); line-height: 1.6; margin: 0; }

.events-grid { display: grid; grid-auto-flow: column; grid-auto-columns: minmax(280px, 1fr); gap: 1.5rem; overflow-x: auto; padding-bottom: 2rem; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; cursor: grab; user-select: none; transition: cursor 0.2s ease; }
.events-grid::-webkit-scrollbar { display: none; }
.events-grid > * { scroll-snap-align: start; }
.events-grid.dragging { cursor: grabbing; user-select: none; scroll-snap-type: none; }
.events-grid.dragging > * { transform: scale(0.98); transition: transform 0.1s ease; }

.event-card { background: transparent; border: none; border-radius: 0; overflow: visible; box-shadow: none; display: block; text-decoration: none; color: inherit; }

.event-image { height: 260px; position: relative; border-radius: 24px; overflow: hidden; background: var(--light-gray); transition: border-radius 300ms ease; }
.event-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transform: scale(1); transition: transform 400ms ease; will-change: transform; }
.event-card:hover .event-image img { transform: scale(1.05); }
.event-card:hover .event-image { border-radius: 24px 12px 24px 24px; }

.event-date {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: var(--primary-blue);
  color: var(--white);
  padding: 0.5rem;
  border-radius: 8px;
  text-align: center;
  min-width: 50px;
}

.event-date .month {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  line-height: 1;
}

.event-date .day {
  display: block;
  font-size: 1.25rem;
  font-weight: 700;
  line-height: 1;
  margin-top: 0.25rem;
}

.event-content { padding: 0.75rem 0 0; }

.event-name { margin: 0.75rem 0 0.25rem; font-size: 1.5rem; font-weight: 400; letter-spacing: -0.02em; line-height: 1.25; color: var(--text-primary); }

.event-description { color: var(--text-muted); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; max-height: calc(1.4em * 2); text-overflow: ellipsis; word-wrap: break-word; hyphens: auto; }

.event-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--primary-blue);
  font-size: 0.875rem;
  font-weight: 500;
}

.event-location i {
  font-size: 0.75rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .cultural-events-section { padding: 3rem 0; }
  .section-title { font-size: 2rem; }
  .section-description { font-size: 1rem; }
  .events-grid { grid-auto-columns: minmax(250px, 1fr); gap: 1rem; }
  .event-content { padding: 0.75rem 0 0; }
}

@media (max-width: 480px) {
  .section-title { font-size: 1.75rem; }
  .event-content { padding: 0.75rem 0 0; }
  .event-name { font-size: 1.25rem; }
}
</style>
