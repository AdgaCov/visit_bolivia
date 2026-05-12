<template>
  <section class="sustainable section">
    <div class="container">
      <div class="text-center">
        <div class="sustainable-header">
          <h3 class="sustainable-title">Viajar de forma sostenible es tradición</h3>
          <p class="sustainable-subtitle">Explora paisajes únicos y comunidades que prosperan con prácticas responsables. Aprende a viajar de manera consciente y deja una huella positiva.</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'SustainableWhatToDoSection'
}
</script>

<style scoped>
.section { 
  padding: 4rem 0;
  font-family: var(--font-family-base) !important;
}

/* Estilos para la sección sustainable - estilo referencia */
.sustainable {
  padding: 8rem 0;
}

.sustainable-header {
  max-width: 800px;
  margin: 0 auto;
}

.sustainable-title {
  font-size: 3.5rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  line-height: 1.05;
  letter-spacing: -0.03em;
  margin-bottom: 3rem;
  font-family: var(--font-family-base) !important;
}

.sustainable-subtitle {
  font-size: 1.125rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: var(--text-primary) !important;
  line-height: 1.6;
  margin: 0;
  font-family: var(--font-family-base) !important;
}

/* Responsive */
@media (max-width: 991.98px) {
  .sustainable {
    padding: 6rem 0;
  }
  
  .sustainable-title {
    font-size: 2.75rem !important;
    line-height: 1.1;
    margin-bottom: 2.5rem;
  }
  
  .sustainable-subtitle {
    font-size: 1.05rem !important;
  }
}

@media (max-width: 767.98px) {
  .sustainable {
    padding: 5rem 0;
  }
  
  .sustainable-title {
    font-size: 2.25rem !important;
    line-height: 1.15;
    margin-bottom: 2rem;
  }
  
  .sustainable-subtitle {
    font-size: 1rem !important;
  }
}
</style>
