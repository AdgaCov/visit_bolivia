<template>
  <component
    :is="to ? 'router-link' : 'div'"
    v-bind="to ? { to } : {}"
    class="interest-card"
    @click="handleClick"
    @touchstart="handleTouchStart"
    @touchend="handleTouchEnd"
  >
    <div class="interest-image">
      <div
        class="interest-image-media"
        :style="{ background: `linear-gradient(0deg, rgba(0,0,0,0.2), rgba(0,0,0,0.0)), url(${cover}) center/cover` }"
      />
      <span v-if="badge" class="interest-badge">{{ badge }}</span>
    </div>
    <div class="interest-content">
      <div class="interest-icon" v-if="icon">
        <i :class="icon"></i>
      </div>
      <h3 class="interest-title">{{ title }}</h3>
      <p v-if="subtitle" class="interest-subtitle">{{ subtitle }}</p>
      <p v-if="description" class="interest-description">{{ description }}</p>
    </div>
  </component>
</template>

<script>
export default {
  name: 'InterestCard',
  emits: ['click'],
  props: {
    icon: { type: String, required: false, default: '' },
    color: { type: String, required: false, default: '' },
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: '' },
    description: { type: String, required: false, default: '' },
    cover: { type: String, default: '/images/placeholder.jpg' },
    badge: { type: String, required: false, default: '' },
    categorySlug: { type: String, required: false, default: '' },
    to: { type: [String, Object], default: null }
  },
  data() {
    return {
      touchStartTime: 0,
      touchStartX: 0,
      touchStartY: 0,
      touchMoved: false
    }
  },
  methods: {
    handleTouchStart(e) {
      this.touchStartTime = Date.now()
      this.touchStartX = e.touches[0].clientX
      this.touchStartY = e.touches[0].clientY
      this.touchMoved = false
    },
    handleTouchEnd(e) {
      const touchEndTime = Date.now()
      const touchDuration = touchEndTime - this.touchStartTime
      const touchEndX = e.changedTouches[0].clientX
      const touchEndY = e.changedTouches[0].clientY
      
      const deltaX = Math.abs(touchEndX - this.touchStartX)
      const deltaY = Math.abs(touchEndY - this.touchStartY)
      
      // Si el toque fue corto y sin movimiento significativo, es un tap
      if (touchDuration < 300 && deltaX < 10 && deltaY < 10) {
        this.handleClick(e)
      }
    },
    handleClick(e) {
      // Prevenir doble emisión en dispositivos táctiles
      if (e.type === 'touchend' && this.touchMoved) {
        return
      }
      
      this.$emit('click')
    }
  }
}
</script>

<style scoped>
.interest-card {
  background: transparent;
  border: none;
  border-radius: 0;
  overflow: visible;
  box-shadow: none;
  transition: all 0.3s ease;
  position: relative;
  cursor: pointer;
  display: block;
  text-decoration: none;
  color: inherit;
}

.interest-card::before {
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

.interest-card:hover::before {
  opacity: 1;
}

.interest-image {
  position: relative;
  height: 260px;
  overflow: hidden;
  border-radius: 24px;
  transition: border-radius 300ms ease;
  background: var(--light-gray);
}

.interest-image-media {
  position: absolute;
  inset: 0;
  background-size: cover !important;
  background-position: center !important;
  background-repeat: no-repeat !important;
  transform: scale(1);
  transition: transform 400ms ease;
  will-change: transform;
}

.interest-card:hover .interest-image-media { transform: scale(1.05); }

.interest-card:hover .interest-image {
  border-radius: 24px 50px 24px 24px;
}

.interest-image::after {
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

.interest-card:hover .interest-image::after {
  opacity: 1;
}

/* Badge */
.interest-badge {
  position: absolute;
  left: 12px;
  bottom: 12px;
  background: rgba(0,0,0,0.65);
  color: #fff;
  border-radius: 999px;
  padding: 4px 10px;
  font-size: 0.75rem;
  line-height: 1;
  backdrop-filter: saturate(1.2) blur(2px);
}

.interest-content { 
  padding: 0.75rem 0 0;
}

.interest-icon { 
  font-size: 1.125rem; 
  margin-bottom: 0.75rem;
  color: var(--text-secondary);
  opacity: 0.8;
  transition: all 0.3s ease;
}

.interest-card:hover .interest-icon {
  color: var(--text-primary);
  opacity: 1;
}

.interest-title { 
  margin: 1rem 0 0.5rem; 
  font-family: var(--font-family-base);
  font-size: 1.75rem;
  font-weight: 400;
  letter-spacing: -0.02em;
  line-height: 1.25;
  color: var(--text-primary);
  transition: color 0.3s ease;
}

.interest-card:hover .interest-title {
  color: var(--primary-blue);
}

.interest-subtitle {
  margin: 0 0 0.5rem 0;
  font-family: var(--font-family-base);
  color: var(--text-secondary);
  font-size: 1rem;
  font-weight: var(--font-weight-regular);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.interest-card:hover .interest-subtitle {
  opacity: 1;
  color: var(--primary-blue);
}

.interest-description { 
  color: var(--text-muted);
  margin: 0 0 1rem 0;
  font-family: var(--font-family-base);
  font-size: 1.125rem;
  line-height: 1.5;
  font-weight: var(--font-weight-regular);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: color 0.3s ease;
}

.interest-card:hover .interest-description {
  color: var(--text-secondary);
}
</style>



