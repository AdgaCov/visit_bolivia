<template>
  <section class="faq section">
    <div class="container">
      <h3 class="section-title mb-3">Preguntas frecuentes</h3>
      <div class="accordion">
        <details v-for="q in faqs" :key="q.q">
          <summary>{{ q.q }}</summary>
          <p class="mt-2">{{ q.a }}</p>
        </details>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'FAQWhatToDoSection',
  data() {
    return {
      faqs: [
        { q: '¿Cuál es la mejor época para visitar?', a: 'Depende de la región: Andes (abril–nov), Amazonía (may–sep), Salar (jun–oct).' },
        { q: '¿Necesito guía para rutas de montaña?', a: 'Recomendado para seguridad y acceso local, especialmente en rutas de altura.' },
        { q: '¿Cómo viajar de forma sostenible?', a: 'Usa operadores locales, respeta senderos, reduce plásticos y apoya productos regionales.' }
      ]
    }
  }
}
</script>

<style scoped>
.section { 
  padding: 4rem 0;
  font-family: var(--font-family-base) !important;
}

.section-title { 
  font-size: 2.75rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin-bottom: 1.25rem;
  font-family: var(--font-family-base) !important;
}

.accordion {
  max-width: 800px;
  margin: 0 auto;
}

.accordion details {
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  margin-bottom: 1rem;
  overflow: hidden;
  transition: all 0.3s ease;
}

.accordion details:hover {
  border-color: var(--border-color);
  box-shadow: var(--shadow-sm);
}

.accordion summary {
  padding: 1.5rem;
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
  cursor: pointer;
  font-family: var(--font-family-base) !important;
  font-size: 1.125rem;
  transition: color 0.3s ease;
}

.accordion summary:hover {
  color: var(--primary-blue);
}

.accordion details[open] summary {
  color: var(--primary-blue);
  border-bottom: 1px solid var(--border-light);
}

.accordion p {
  padding: 0 1.5rem 1.5rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
  font-family: var(--font-family-base) !important;
}

/* Responsive */
@media (max-width: 767.98px) {
  .section-title { 
    font-size: 1.75rem; 
    font-weight: var(--font-weight-semibold);
    line-height: 1.25;
    letter-spacing: -0.01em;
  }
  
  .accordion summary {
    padding: 1rem;
    font-size: 1rem;
  }
  
  .accordion p {
    padding: 0 1rem 1rem;
    font-size: 0.95rem;
  }
}
</style>
