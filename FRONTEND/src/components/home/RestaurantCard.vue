<template>
  <component :is="linkComponent" v-bind="linkProps" class="restaurant-card">
    <div class="restaurant-image">
      <div class="restaurant-image-media" :style="{ backgroundImage: `url(${image})` }" />
    </div>
    <div class="restaurant-content">
      <h3 class="restaurant-title">{{ title }}</h3>
      <div class="restaurant-meta">
        <span><i class="fas fa-utensils me-1"></i>{{ cuisine }}</span>
        <span><i class="fas fa-map-marker-alt ms-3 me-1"></i>{{ location }}</span>
      </div>
      <p class="restaurant-description">{{ description }}</p>
    </div>
  </component>
  
</template>

<script>
import { RouterLink } from 'vue-router'

export default {
  name: 'RestaurantCard',
  props: {
    image: { type: String, required: true },
    title: { type: String, required: true },
    cuisine: { type: String, required: true },
    location: { type: String, required: true },
    description: { type: String, required: true },
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
.restaurant-card { background: transparent; border: none; border-radius: 0; overflow: visible; box-shadow: none; display: block; text-decoration: none; color: inherit; }
.restaurant-image { height: 260px; position: relative; border-radius: 24px; overflow: hidden; background: var(--light-gray); transition: border-radius 300ms ease; }
.restaurant-image-media { position: absolute; inset: 0; background-size: cover; background-position: center; background-repeat: no-repeat; transform: scale(1); transition: transform 400ms ease; will-change: transform; }
.restaurant-card:hover .restaurant-image-media { transform: scale(1.05); }
.restaurant-card:hover .restaurant-image { border-radius: 24px 12px 24px 24px; }
.restaurant-content { padding: 0.75rem 0 0; }
.restaurant-title { margin: 0.75rem 0 0.25rem; font-size: 1.5rem; font-weight: 400; letter-spacing: -0.02em; line-height: 1.25; color: var(--text-primary); }
.restaurant-meta { color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.5rem; }
.restaurant-description { 
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
@supports not (-webkit-line-clamp: 3) {
  .restaurant-description {
    display: block;
    max-height: calc(1.4em * 3);
    overflow: hidden;
    position: relative;
  }
  
  .restaurant-description::after {
    content: '...';
    position: absolute;
    bottom: 0;
    right: 0;
    background: var(--white);
    padding-left: 0.5rem;
  }
}
</style>


