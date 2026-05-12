<template>
  <component :is="linkComponent" v-bind="linkProps" class="accommodation-card">
    <div class="accommodation-image">
      <div class="accommodation-image-media" :style="{ backgroundImage: `url(${image})` }" />
      <div class="accommodation-badge">{{ badge }}</div>
    </div>
    <div class="accommodation-content">
      <div class="accommodation-icon">
        <i class="fas fa-bed"></i>
      </div>
      <h3 class="accommodation-title">{{ title }}</h3>
      <div class="accommodation-meta">
        <span><i class="fas fa-map-marker-alt me-1"></i>{{ location }}</span>
        <span><i class="fas fa-dollar-sign ms-3 me-1"></i>{{ price }}</span>
      </div>
      <p class="accommodation-description">{{ description }}</p>
    </div>
  </component>
</template>

<script>
import { RouterLink } from 'vue-router'

export default {
  name: 'AccommodationCard',
  props: {
    image: { type: String, required: true },
    title: { type: String, required: true },
    location: { type: String, required: true },
    price: { type: String, required: true },
    description: { type: String, required: true },
    badge: { type: String, required: true },
    to: { type: [String, Object], required: false, default: null }
  },
  computed: {
    linkComponent() {
      return this.to ? RouterLink : 'div'
    },
    linkProps() {
      return this.to ? { to: this.to } : {}
    }
  }
}
</script>

<style scoped>
.accommodation-card { 
  background: transparent; 
  border: none; 
  border-radius: 0; 
  overflow: visible; 
  box-shadow: none; 
  display: block; 
  text-decoration: none; 
  color: inherit; 
  position: relative;
  cursor: pointer;
}

.accommodation-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--border-light) 0%, var(--border-color) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.accommodation-card:hover::before {
  opacity: 1;
}

.accommodation-image { 
  height: 260px; 
  position: relative; 
  border-radius: 24px; 
  overflow: hidden; 
  background: var(--light-gray); 
  transition: border-radius 300ms ease; 
}

.accommodation-image-media { 
  position: absolute; 
  inset: 0; 
  background-size: cover; 
  background-position: center; 
  background-repeat: no-repeat; 
  transform: scale(1); 
  transition: transform 400ms ease; 
  will-change: transform; 
}

.accommodation-card:hover .accommodation-image-media { 
  transform: scale(1.05); 
}

.accommodation-card:hover .accommodation-image { 
  border-radius: 24px 50px 24px 24px; 
}

.accommodation-image::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(0deg, rgba(0,0,0,0.1), rgba(0,0,0,0.0));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.accommodation-card:hover .accommodation-image::after {
  opacity: 1;
}

.accommodation-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: var(--white);
  color: var(--text-primary);
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: var(--font-weight-medium);
  font-family: var(--font-family-base);
  border: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
  backdrop-filter: blur(10px);
  z-index: 2;
}

.accommodation-content { 
  padding: 0.75rem 0 0; 
}

.accommodation-icon {
  font-size: 1.125rem;
  margin-bottom: 0.75rem;
  color: var(--text-secondary);
  opacity: 0.8;
  transition: all 0.3s ease;
}

.accommodation-card:hover .accommodation-icon {
  color: var(--text-primary);
  opacity: 1;
}

.accommodation-title { 
  margin: 0.75rem 0 0.25rem; 
  font-size: 1.5rem; 
  font-weight: 400; 
  letter-spacing: -0.02em; 
  line-height: 1.25; 
  color: var(--text-primary); 
}

.accommodation-meta { 
  color: var(--text-muted); 
  font-size: 0.875rem; 
  margin-bottom: 0.5rem; 
}

.accommodation-description { 
  color: var(--text-muted); 
  margin: 0; 
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
  max-height: calc(1.4em * 2);
  text-overflow: ellipsis;
  word-wrap: break-word;
  hyphens: auto;
}

/* Fallback para navegadores que no soportan -webkit-line-clamp */
@supports not (-webkit-line-clamp: 2) {
  .accommodation-description {
    display: block;
    max-height: calc(1.4em * 2);
    overflow: hidden;
    position: relative;
  }
  
  .accommodation-description::after {
    content: '...';
    position: absolute;
    bottom: 0;
    right: 0;
    background: var(--white);
    padding-left: 0.5rem;
  }
}
</style>
