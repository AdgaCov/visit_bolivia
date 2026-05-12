<template>
  <section class="cta section light">
    <div class="container text-center">
      <h3 class="section-title">¿Listo para planificar tu viaje?</h3>
      <p class="section-text">Explora lugares, crea tu lista de favoritos y descubre rutas únicas.</p>
      <router-link to="/lugares" class="btn-read">Explorar lugares</router-link>
    </div>
  </section>
</template>

<script>
export default {
  name: 'CTAWhatToDoSection'
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

.section-text { 
  color: var(--text-secondary) !important; 
  max-width: 820px;
  font-size: 1.125rem !important;
  line-height: 1.6;
  font-weight: var(--font-weight-regular) !important;
  font-family: var(--font-family-base) !important;
  margin: 0 auto 2rem;
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

/* Responsive */
@media (max-width: 767.98px) {
  .section-title { 
    font-size: 1.75rem; 
    font-weight: var(--font-weight-semibold);
    line-height: 1.25;
    letter-spacing: -0.01em;
  }
  
  .section-text {
    font-size: 1rem;
    line-height: 1.6;
  }
}
</style>
