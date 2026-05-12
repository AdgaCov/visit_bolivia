<template>
  <section class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-4">
          <h1 class="hero-title">Bolivia te da el tiempo y el espacio…</h1>
          <p class="hero-subtitle">Para sumergirte en nuestros paisajes, historia, gastronomía y comunidad local. Tiempo para experiencias memorables y sostenibles.</p>
        </div>
        <div class="col-lg-8">
          <div class="hero-media">
            <video
              class="hero-video"
              autoplay
              muted
              loop
              playsinline
              poster="/images/huatajata.jpg"
            >
              <source src="/videos/conocebolivia1280x720.mp4" type="video/mp4" />
              <source src="/videos/conocebolivia854x480.mp4" type="video/mp4" />
              <source src="/videos/conocebolivia640x360.mp4" type="video/mp4" />
            </video>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroWhatToDoSection'
}
</script>

<style scoped>
/* Hero rediseñado estilo referencia, conservando paleta */
.hero { 
  background: var(--bg-secondary); 
  padding: 6rem 0; 
  position: relative; 
  overflow: hidden; 
  border-bottom: 1px solid var(--border-light); 
  min-height: 600px; 
}

.hero-title { 
  font-size: 2.75rem !important; 
  font-weight: var(--font-weight-light) !important; 
  color: var(--text-primary) !important;
  letter-spacing: -0.02em;
  line-height: 1.15;
  margin-bottom: 1.25rem;
  font-family: var(--font-family-base) !important;
}

.hero-subtitle { 
  color: var(--text-secondary) !important; 
  margin-top: 1rem; 
  font-size: 1.125rem !important; 
  max-width: 640px;
  font-family: var(--font-family-base) !important;
}

.hero-media { 
  position: absolute; 
  top: 0; 
  right: 0; 
  width: 60%; 
  height: 100%; 
  border-radius: 0; 
  clip-path: ellipse(75% 95% at 100% 75%); 
  overflow: hidden; 
}

.hero-video { 
  position: absolute; 
  inset: 0; 
  width: 100%; 
  height: 100%; 
  object-fit: cover; 
}

/* Tablet */
@media (max-width: 991.98px) {
  .hero-media { 
    position: relative; 
    clip-path: none; 
    border-radius: 16px; 
    margin-top: 1.25rem; 
    width: 100%;
    height: 100%;
    min-height: 350px;
  }
  
  .hero-title {
    font-size: 2.25rem !important;
    line-height: 1.2;
    margin-bottom: 1.5rem;
  }
}

/* Móvil */
@media (max-width: 767.98px) {
  .hero { padding: 4rem 0; min-height: 500px; }
  .hero-title { 
    font-size: 1.875rem !important;
    font-weight: var(--font-weight-light) !important;
    color: var(--text-primary) !important;
    letter-spacing: -0.02em;
    line-height: 1.2;
    margin-bottom: 1.5rem;
  }
  .hero-media { 
    position: relative; 
    height: 100%; 
    min-height: 300px;
    clip-path: none; 
    border-radius: 16px; 
    margin-top: 1.25rem; 
    width: 100%;
  }
}
</style>
