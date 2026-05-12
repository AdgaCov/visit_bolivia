<template>
  <section class="planning-cards">
    <div class="container">
      <div class="planning-intro-wrap">
        <h2 class="planning-intro-title">Viaja aquí y por Bolivia</h2>
        <p class="planning-intro-subtitle">Aprovecha las carreteras bien conectadas y las conexiones puntuales de trenes y buses para llegar a cualquier lugar que quieras visitar con comodidad y seguridad.</p>
      </div>
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-8">
          <a href="#" class="planning-cards-card media-large">
            <div class="planning-cards-image" style="background-image: url('/images/viaja.jpg')"></div>
            <div class="planning-cards-overlay light">Viajar por Bolivia</div>
          </a>
        </div>
        <div class="col-lg-4">
          <div class="d-flex flex-column gap-4 h-100">
            <a href="#" class="planning-cards-card media-small">
              <div class="planning-cards-image" style="background-image: url('/images/10.png')"></div>
              <div class="planning-cards-overlay">Viajar a Bolivia</div>
            </a>
            <a href="#" class="planning-cards-card media-small">
              <div class="planning-cards-image" style="background-image: url('/images/13.png')"></div>
              <div class="planning-cards-overlay">Destinos sostenibles</div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default { name: 'PlanningCards' }
</script>

<style scoped>
.planning-cards { padding: 4.5rem 0; background: var(--white); }
.planning-intro-wrap { max-width: 1140px; }
.planning-intro-title { font-size: 3.25rem; font-weight: 300; color: var(--text-primary); letter-spacing: -0.02em; line-height: 1.05; margin-bottom: 1.25rem; }
.planning-intro-subtitle { font-size: 1.125rem; color: var(--text-secondary); max-width: 1200px; }
.planning-cards-card { position: relative; display: block; width: 100%; border-radius: 40px; overflow: hidden; box-shadow: var(--shadow-md); text-decoration: none; color: inherit; }
.planning-cards-card.media-large { min-height: 520px; }
.planning-cards-card.media-small { min-height: 260px; }
.planning-cards-image { position: absolute; inset: 0; background-size: cover; background-position: center; background-repeat: no-repeat; }
.planning-cards-overlay { position: absolute; left: 1.25rem; right: 1.25rem; bottom: 1.25rem; background: rgba(30, 41, 59, 0.9); color: var(--white); padding: 1rem 1.25rem; border-radius: 20px; font-weight: 600; font-size: 1.25rem; }
.planning-cards-overlay.light { background: rgba(233, 222, 248, 0.95); color: var(--text-primary); }
@media (max-width: 991.98px) {
  .planning-cards { padding: 3.5rem 0; }
  .planning-cards-card.media-large { min-height: 380px; }
  .planning-cards-card.media-small { min-height: 220px; }
  .planning-cards-overlay { font-size: 1.1rem; }
}
</style>



