<template>
  <div class="place-card">
    <div class="place-image">
      <img :src="image" :alt="name">
      <div class="place-badge">{{ category }}</div>
    </div>
    <div class="place-content">
      <h3 class="place-name">{{ name }}</h3>
      <p class="place-description">{{ description }}</p>
      <div class="place-meta">
        <span class="place-location"><i class="fas fa-map-marker-alt"></i>{{ location }}</span>
        <span class="place-rating"><i class="fas fa-star"></i>{{ rating }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FeaturedPlaceCard',
  props: {
    image: { type: String, required: true },
    category: { type: String, required: true },
    categoryColor: { type: String, required: true },
    name: { type: String, required: true },
    description: { type: String, required: true },
    location: { type: String, required: true },
    rating: { type: [Number, String], required: true }
  }
}
</script>

<style scoped>
.place-card { 
  background: var(--white); 
  border: 1px solid var(--border-light); 
  border-radius: 12px; 
  overflow: hidden; 
  transition: all 0.3s ease; 
  box-shadow: var(--shadow-sm);
  position: relative;
}

.place-card:hover { 
  transform: translateY(-3px); 
  box-shadow: var(--shadow-sm);
  border-color: var(--border-color); 
}

.place-image { 
  position: relative; 
  height: 200px; 
  overflow: hidden; 
}

.place-image img { 
  width: 100%; 
  height: 100%; 
  object-fit: cover; 
  transition: transform 0.3s ease; 
}

.place-card:hover .place-image img { 
  transform: scale(1.02); 
}

.place-badge { 
  position: absolute; 
  top: 1rem; 
  right: 1rem; 
  padding: 0.375rem 0.875rem; 
  border-radius: 6px; 
  color: var(--text-secondary);
  font-size: 0.75rem; 
  font-weight: 400; 
  text-transform: uppercase;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid var(--border-light);
  letter-spacing: 0.5px;
}

.place-content { 
  padding: 1.75rem; 
}

.place-name { 
  font-size: 1.125rem; 
  font-weight: 500; 
  color: var(--text-primary); 
  margin-bottom: 0.75rem;
  letter-spacing: -0.01em;
}

.place-description { 
  color: var(--text-secondary); 
  margin-bottom: 1.25rem; 
  line-height: 1.6;
  font-size: 0.95rem;
}

.place-meta { 
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
  font-size: 0.875rem; 
  color: var(--text-muted);
  padding-top: 1rem;
  border-top: 1px solid var(--border-light);
}

.place-meta i { 
  margin-right: 0.375rem; 
  color: var(--text-secondary);
  opacity: 0.7;
}
</style>


