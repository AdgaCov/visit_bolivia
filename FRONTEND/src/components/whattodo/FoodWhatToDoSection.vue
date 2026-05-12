<template>
  <section class="food section">
    <div class="container">
      <div class="d-flex justify-content-between align-items-start mb-6">
        <div class="food-header">
          <h3 class="food-title">La gastronomía boliviana te sorprende</h3>
          <p class="food-subtitle">Una mezcla de tradiciones y creatividad. Descubre sabores locales y productores de pequeña escala.</p>
        </div>
        <router-link to="/gastronomia" class="btn-read">LEER MÁS</router-link>
      </div>
      <div class="row g-4 mt-4">
        <div class="col-md-6">
          <div class="food-card-large">
            <div class="food-card-image-container">
              <div class="food-card-image" style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQCubB7AhacyQ_kW3uCI6Vh9T0johWS0QUgAQ&s')"></div>
              <div class="food-card-overlay">
                <h4 class="food-card-title">Introducción a la cocina boliviana</h4>
                <p class="food-card-text">Descubre los sabores auténticos y las técnicas tradicionales que hacen única la gastronomía boliviana.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="row g-4 h-100">
            <div class="col-12">
              <div class="food-card-pill">
                <div class="food-card-pill-image-container">
                  <div class="food-card-pill-image" style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRbSxrKor22uBqS7kG35IZSaZg_LGycJYsrtQ&s')"></div>
                  <div class="food-card-pill-overlay">
                    <span class="food-card-pill-text">Come local: productores y pequeños mercados</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="food-card-pill">
                <div class="food-card-pill-image-container">
                  <div class="food-card-pill-image" style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrKDYsuDYPCP0IxxHh-kbsEknB9AllUWiKGg&s')"></div>
                  <div class="food-card-pill-overlay">
                    <span class="food-card-pill-text">Verde en los mejores restaurantes</span>
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
  name: 'FoodWhatToDoSection'
}
</script>

<style scoped>
.section { 
  padding: 4rem 0;
  font-family: var(--font-family-base) !important;
}

/* Estilos para la sección food - usando fonts.css y colors.css */
.food {
  padding: 8rem 0;
}

.food-header {
  max-width: 800px;
}

.food-title {
  font-size: 3.5rem !important;
  font-weight: var(--font-weight-light) !important;
  color: var(--text-primary) !important;
  line-height: 1.05;
  letter-spacing: -0.03em;
  margin-bottom: 3rem;
  font-family: var(--font-family-base) !important;
}

.food-subtitle {
  font-size: 1.125rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: var(--text-secondary) !important;
  line-height: 1.6;
  margin: 0;
  font-family: var(--font-family-base) !important;
}

/* Estilos específicos para las cards de gastronomía */
.food-card-large {
  background: var(--white);
  border-radius: 16px;
  min-height: 400px;
  box-shadow: var(--shadow-md);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid var(--border-light);
}

.food-card-large:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
  border-color: var(--border-color);
}

.food-card-image-container {
  position: relative;
  height: 100%;
  min-height: 400px;
  overflow: hidden;
}

.food-card-image {
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

.food-card-large:hover .food-card-image {
  transform: scale(1.1);
}

.food-card-large:hover .food-card-overlay {
  transform: none;
}

.food-card-overlay {
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

.food-card-title {
  font-size: 1.5rem !important;
  font-weight: var(--font-weight-semibold) !important;
  color: var(--white) !important;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
  font-family: var(--font-family-base) !important;
}

.food-card-text {
  font-size: 1rem !important;
  font-weight: var(--font-weight-regular) !important;
  color: rgba(255, 255, 255, 0.9) !important;
  margin: 0;
  line-height: 1.5;
  font-family: var(--font-family-base) !important;
}

.food-card-pill {
  background: var(--white);
  border-radius: 16px;
  min-height: 180px;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid var(--border-light);
}

.food-card-pill:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: var(--border-color);
}

.food-card-pill-image-container {
  position: relative;
  height: 100%;
  min-height: 180px;
  overflow: hidden;
}

.food-card-pill-image {
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

.food-card-pill:hover .food-card-pill-image {
  transform: scale(1.1);
}

.food-card-pill:hover .food-card-pill-overlay {
  transform: none;
}

.food-card-pill-overlay {
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

.food-card-pill-text {
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
  .food {
    padding: 6rem 0;
  }
  
  .food-title {
    font-size: 2.75rem !important;
    line-height: 1.1;
    margin-bottom: 2.5rem;
  }
  
  .food-subtitle {
    font-size: 1.05rem !important;
  }
  
  .btn-read {
    margin-top: 1rem;
  }
  
  .food-card-large {
    min-height: 350px;
  }
  
  .food-card-image-container {
    min-height: 350px;
  }
  
  .food-card-pill {
    min-height: 160px;
  }
  
  .food-card-pill-image-container {
    min-height: 160px;
  }
}

@media (max-width: 767.98px) {
  .food {
    padding: 5rem 0;
  }
  
  .food-title {
    font-size: 2.25rem !important;
    line-height: 1.15;
    margin-bottom: 2rem;
  }
  
  .food-subtitle {
    font-size: 1rem !important;
  }
  
  .food-card-large {
    min-height: 300px;
  }
  
  .food-card-image-container {
    min-height: 300px;
  }
  
  .food-card-pill {
    min-height: 140px;
  }
  
  .food-card-pill-image-container {
    min-height: 140px;
  }
  
  .food-card-overlay {
    padding: 1.5rem;
  }
  
  .food-card-title {
    font-size: 1.25rem !important;
  }
  
  .food-card-pill-overlay {
    padding: 1rem;
  }
  
  .food-card-pill-text {
    font-size: 0.9rem !important;
  }
}
</style>
