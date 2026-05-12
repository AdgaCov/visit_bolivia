<template>
  <section class="seasons-section" role="region" aria-label="Estaciones del año en Bolivia">
    <div class="container">
      <h2 class="seasons-title">Cada estación tiene algo especial — y una extra te da más tiempo para verlo todo.</h2>
      <p class="seasons-tip">Consejo: Explorar en temporada baja te ayudará a evitar aglomeraciones.</p>
      <div class="seasons-grid">
        <div class="season-card season-spring">
          <div class="season-icon">🌸</div>
          <div class="season-name">Primavera</div>
        </div>
        <div class="season-card season-summer">
          <div class="season-icon">☀️</div>
          <div class="season-name">Verano</div>
        </div>
        <div class="season-card season-autumn">
          <div class="season-icon">🍂</div>
          <div class="season-name">Otoño</div>
        </div>
        <div class="season-card season-winter">
          <div class="season-icon">❄️</div>
          <div class="season-name">Invierno</div>
        </div>
        <div class="season-card season-fifth">
          <div class="season-icon">≈</div>
          <div class="season-name">Quinta estación</div>
        </div>
      </div>
    </div>
  </section>
  
</template>

<script>
export default {
  name: 'SeasonsSection'
}
</script>

<style scoped>
.seasons-section { padding: 3rem 0; background: var(--bg-primary); }
.seasons-title { font-size: 2rem; font-weight: 400; color: var(--text-primary); letter-spacing: -0.01em; margin: 0 0 0.75rem 0; }
.seasons-tip { color: var(--text-secondary); margin: 0 0 1.5rem 0; }

.seasons-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; }
.season-card {
  background: #E7F0EA; /* default soft */
  border-radius: 36px; 
  padding: 2rem 1rem; 
  text-align: center; 
  color: #3b3b3b; 
  min-height: 160px;
  display: grid; 
  place-items: center; 
  gap: 0.5rem; 
}
.season-icon { font-size: 2rem; opacity: 0.85; }
.season-name { font-size: 1.25rem; font-weight: 500; letter-spacing: -0.01em; }

.season-spring { background: #DDEEE5; }
.season-summer { background: #F8EEC9; }
.season-autumn { background: #E7DAF0; }
.season-winter { background: #DDE9FA; }
.season-fifth { background: #E3E0E6; }

@media (max-width: 992px) {
  .seasons-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 600px) {
  .seasons-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>




