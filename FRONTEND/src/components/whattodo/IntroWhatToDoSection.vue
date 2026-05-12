<template>
  <section class="intro section">
    <div class="container">
      <div class="intro-content">
        <h2 class="intro-title">Somos una nación con una gran historia que contar</h2>
        <p class="intro-subtitle">Bolivia ha estado encrucijada de culturas por siglos.</p>
        <div class="intro-text">
          <p>Durante siglos, hemos sido parte de las rutas comerciales andinas. Caminos que conectaban el Pacífico con el Atlántico, transportando bienes y personas de norte a sur y viceversa. Ahora, Bolivia se encuentra en el corazón de Sudamérica, combinando lo mejor de los Andes, la Amazonía y los valles interandinos.</p>
          <p>Bolivia es un lugar que te ayuda a entender tu lugar en el mundo. Un lugar que, para muchas personas, no está en su radar. Pero es hora de que lo esté.</p>
          <p>Déjanos mostrarte por qué.</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'IntroWhatToDoSection'
}
</script>

<style scoped>
.section { 
  padding: 4rem 0;
  font-family: var(--font-family-base) !important;
}

/* Estilos para la sección intro - estilo referencia */
.intro {
  padding: 6rem 0;
}

.intro-content {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 2rem;
}

.intro-title {
  font-size: 3rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  line-height: 1.1;
  letter-spacing: -0.02em;
  margin-bottom: 2rem;
  font-family: var(--font-family-base) !important;
}

.intro-subtitle {
  font-size: 1.5rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: var(--text-primary) !important;
  line-height: 1.3;
  margin-bottom: 2rem;
  font-family: var(--font-family-base) !important;
}

.intro-text {
  font-size: 1.125rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: var(--text-secondary) !important;
  line-height: 1.6;
  font-family: var(--font-family-base) !important;
}

.intro-text p {
  margin-bottom: 1.5rem;
  font-family: var(--font-family-base) !important;
}

.intro-text p:last-child {
  margin-bottom: 0;
}

/* Responsive */
@media (max-width: 767.98px) {
  .intro {
    padding: 4rem 0;
  }
  
  .intro-content {
    padding: 0 1rem;
  }
  
  .intro-title {
    font-size: 2.25rem !important;
    line-height: 1.2;
    margin-bottom: 1.5rem;
  }
  
  .intro-subtitle {
    font-size: 1.25rem !important;
    margin-bottom: 1.5rem;
  }
  
  .intro-text {
    font-size: 1rem !important;
  }
}
</style>
