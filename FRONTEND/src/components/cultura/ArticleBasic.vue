<template>
  <section class="cultural-info-section">
  <div class="container">
    <div class="cultural-content">
      <!-- Título principal -->
      <h2 class="cultural-title">{{ article?.title || 'Bolivia patrimonio e identidad.' }}</h2>
      
      <!-- Subtítulo -->
      <p v-if="article?.subtitle">{{ article.subtitle }}</p>
      <p v-if="article?.description">{{ article.description }}</p>

      <!-- Contenido con párrafos -->
      <div class="cultural-text" v-if="article?.content">
        <p v-for="(paragraph, index) in article.content.split('\n')" :key="index">
          {{ paragraph }}
        </p>
      </div>
    </div>
  </div>
</section>
  <!-- <section class="cultural-info-section">
    <div class="container">
      <div class="cultural-content">
         Título principal -->
       <!-- <h2 class="cultural-title">{{ article?.title || 'Bolivia patrimonio e identidad.' }}</h2>
        
        Contenido descriptivo -->
        <!--<div class="cultural-text">
          <p v-if="article?.subtitle">{{ article.subtitle }}</p>
          <p v-if="article?.description">{{ article.description }}</p>
          <p v-if="article?.content">{{ article.content }}</p>
        </div>
      </div>
    </div>
  </section> -->
</template>

<script>
export default {
  name: 'CulturalInfoSection',
  props: {
    article: {
      type: Object,
      required: false,
      default: null
    }
  }
}
</script>

<style scoped>
.cultural-info-section {
  padding: 4rem 0;
  font-family: var(--font-family-base) !important;
}

.cultural-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
  font-family: var(--font-family-base) !important;
}

.cultural-title {
  font-size: 3rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  line-height: 1.1;
  letter-spacing: -0.02em;
  margin-bottom: 2rem;
  font-family: var(--font-family-base) !important;
}

.cultural-text {
  text-align: left;
}

.cultural-text p {
  font-size: 1.125rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: var(--text-secondary) !important;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  font-family: var(--font-family-base) !important;
}

.cultural-text p:last-child {
  margin-bottom: 0;
}

.text-link {
  color: var(--primary-blue);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.text-link:hover {
  color: var(--primary-blue-dark, #1E40AF);
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
  .cultural-info-section {
    padding: 3rem 0;
  }
  
  .cultural-content {
    padding: 0 1rem;
  }
  
  .cultural-title {
    font-size: 2.25rem !important;
    line-height: 1.2;
    margin-bottom: 1.5rem;
  }
  
  .cultural-text p {
    font-size: 1rem !important;
  }
}

@media (max-width: 480px) {
  .cultural-info-section {
    padding: 2rem 0;
  }
  
  .cultural-content {
    padding: 0 0.5rem;
  }
  
  .cultural-title {
    font-size: 1.75rem !important;
    margin-bottom: 1.5rem;
  }
  
  .cultural-text p {
    font-size: 0.95rem;
  }
}
</style>
