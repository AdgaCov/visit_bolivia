<template>
  <section class="highlights section light">
    <div class="container">
      <div class="d-flex justify-content-between align-items-start mb-6">
        <div class="highlights-header">
          <h3 class="highlights-title">A un paso de la aventura</h3>
          <p class="highlights-subtitle">Muévete a pie o en bote al ritmo del canto de las aves. Escapa de la rutina a un ritmo pausado. La naturaleza boliviana espera a los aventureros.</p>
        </div>
        <router-link to="/lugares" class="btn-read">LEER MÁS</router-link>
      </div>
      <div class="row g-4 mt-4">
        <div class="col-md-6">
          <div class="adventure-card-large">
            <div class="adventure-card-image-container">
              <div class="adventure-card-image" style="background-image: url('https://riquezasdebolivia.com/wp-content/uploads/2020/10/cri%CC%81a-de-peces-en-Santa-Cruz.jpg')"></div>
              <div class="adventure-card-overlay">
                <h4 class="adventure-card-title">Pesca en Bolivia — dónde, cuándo y cómo</h4>
                <p class="adventure-card-text">Descubre los mejores lugares para pescar en ríos y lagos bolivianos, con guías expertos y equipamiento profesional.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="row g-4 h-100">
            <div class="col-12">
              <div class="adventure-card-pill">
                <div class="adventure-card-pill-image-container">
                  <div class="adventure-card-pill-image" style="background-image: url('https://magritouroperator.com/wp-content/uploads/2025/07/senderismo-en-bolivia-magri-tour-operator-05.jpg')"></div>
                  <div class="adventure-card-pill-overlay">
                    <span class="adventure-card-pill-text">Todo lo que necesitas para hacer senderismo</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="adventure-card-pill">
                <div class="adventure-card-pill-image-container">
                  <div class="adventure-card-pill-image" style="background-image: url('https://www.lorenzoexpeditions.com/wp-content/uploads/2022/09/IMG_20220719_160507-1024x766.jpg')"></div>
                  <div class="adventure-card-pill-overlay">
                    <span class="adventure-card-pill-text">3 razones para visitar Bolivia en temporada de lluvias</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HighlightsWhatToDoSection'
}
</script>

<style scoped>
.section { 
  padding: 4rem 0;
  font-family: var(--font-family-base) !important;
}

.section.light { background: var(--bg-secondary); }

/* Estilos para la sección highlights - copiados de FoodWhatToDoSection */
.highlights {
  background: var(--bg-secondary) !important;
  padding: 8rem 0;
}

.highlights-header {
  max-width: 800px;
}

.highlights-title {
  font-size: 3.5rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  line-height: 1.05;
  letter-spacing: -0.03em;
  margin-bottom: 3rem;
  font-family: var(--font-family-base) !important;
}

.highlights-subtitle {
  font-size: 1.125rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: var(--text-secondary) !important;
  line-height: 1.6;
  margin: 0;
  font-family: var(--font-family-base) !important;
}

/* Estilos específicos para las cards de aventura - copiados de food */
.adventure-card-large {
  background: var(--white);
  border-radius: 16px;
  min-height: 400px;
  box-shadow: var(--shadow-md);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid var(--border-light);
}

.adventure-card-large:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
  border-color: var(--border-color);
}

.adventure-card-image-container {
  position: relative;
  height: 100%;
  min-height: 400px;
  overflow: hidden;
}

.adventure-card-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: transform 0.3s ease;
}

.adventure-card-large:hover .adventure-card-image {
  transform: scale(1.1);
}

.adventure-card-large:hover .adventure-card-overlay {
  transform: none;
}

.adventure-card-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
  padding: 2rem;
  color: var(--white);
  z-index: 2;
  transform: none;
  transition: none;
}

.adventure-card-title {
  font-size: 1.5rem !important;
  font-weight: var(--font-weight-semibold) !important;
  color: var(--white) !important;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
  font-family: var(--font-family-base) !important;
}

.adventure-card-text {
  font-size: 1rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: rgba(255, 255, 255, 0.9) !important;
  margin: 0;
  line-height: 1.5;
  font-family: var(--font-family-base) !important;
}

.adventure-card-pill {
  background: var(--white);
  border-radius: 16px;
  min-height: 180px;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid var(--border-light);
}

.adventure-card-pill:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: var(--border-color);
}

.adventure-card-pill-image-container {
  position: relative;
  height: 100%;
  min-height: 180px;
  overflow: hidden;
}

.adventure-card-pill-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: transform 0.3s ease;
}

.adventure-card-pill:hover .adventure-card-pill-image {
  transform: scale(1.1);
}

.adventure-card-pill:hover .adventure-card-pill-overlay {
  transform: none;
}

.adventure-card-pill-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
  padding: 1.25rem;
  border-radius: 0 0 16px 16px;
  z-index: 2;
  transform: none;
  transition: none;
}

.adventure-card-pill-text {
  font-size: 1rem !important;
  font-weight: var(--font-weight-medium) !important;
  color: var(--white) !important;
  margin: 0;
  line-height: 1.4;
  font-family: var(--font-family-base) !important;
}

/* Asegurar que las cards pequeñas tengan la misma altura */
.col-md-6 .row.h-100 {
  height: 100% !important;
}

.col-md-6 .row.h-100 .col-12 {
  height: 50%;
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
@media (max-width: 991.98px) {
  .highlights {
    padding: 6rem 0;
  }
  
  .highlights-title {
    font-size: 2.75rem !important;
    line-height: 1.1;
    margin-bottom: 2.5rem;
  }
  
  .highlights-subtitle {
    font-size: 1.05rem !important;
  }
  
  .btn-read {
    margin-top: 1rem;
  }
  
  .adventure-card-large {
    min-height: 350px;
  }
  
  .adventure-card-image-container {
    min-height: 350px;
  }
  
  .adventure-card-pill {
    min-height: 160px;
  }
  
  .adventure-card-pill-image-container {
    min-height: 160px;
  }
}

@media (max-width: 767.98px) {
  .highlights {
    padding: 5rem 0;
  }
  
  .highlights-title {
    font-size: 2.25rem !important;
    line-height: 1.15;
    margin-bottom: 2rem;
  }
  
  .highlights-subtitle {
    font-size: 1rem !important;
  }
  
  .adventure-card-large {
    min-height: 300px;
  }
  
  .adventure-card-image-container {
    min-height: 300px;
  }
  
  .adventure-card-pill {
    min-height: 140px;
  }
  
  .adventure-card-pill-image-container {
    min-height: 140px;
  }
  
  .adventure-card-overlay {
    padding: 1.5rem;
  }
  
  .adventure-card-title {
    font-size: 1.25rem !important;
  }
  
  .adventure-card-pill-overlay {
    padding: 1rem;
  }
  
  .adventure-card-pill-text {
    font-size: 0.9rem !important;
  }
}
</style>
