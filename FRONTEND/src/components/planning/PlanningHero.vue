<template>
  <section class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-4">
          <h1 class="hero-title">{{ computedTitle }}</h1>
          <p class="hero-subtitle">{{ computedSubtitle }}</p>
        </div>
        <div class="col-lg-8">
          <div class="hero-media">
            <template v-if="isVideo">
              <video class="hero-video" autoplay muted loop playsinline>
                <source :src="mediaSrc" type="video/mp4" />
              </video>
            </template>
            <template v-else>
              <img v-if="mediaSrc" :src="mediaSrc" alt="Planning media" class="hero-image" />
            </template>
          </div>
        </div>
      </div>
    </div>
  </section>
  </template>

<script>
export default {
  name: 'PlanningHero',
  props: {
    article: {
      type: Object,
      default: null
    }
  },
  computed: {
    computedTitle() {
      return this.article?.title || 'Consejos para planificar tu viaje a Bolivia'
    },
    computedSubtitle() {
      return this.article?.subtitle || 'Información útil sobre transporte, clima, seguridad, feriados y más para quitar el estrés de preparar tu viaje.'
    },
    mediaSrc() {
      return this.article?.media_url || '/videos/conocebolivia1280x720.mp4'
    },
    isVideo() {
      const src = this.mediaSrc || ''
      return /\.mp4(\?|$)/i.test(src)
    }
  }
}
</script>

<style scoped>
.hero { background: var(--bg-secondary); padding: 6rem 0; position: relative; overflow: hidden; border-bottom: 1px solid var(--border-light); min-height: 600px; }
.hero-title { font-size: 2.75rem; line-height: 1.15; font-weight: 300; color: var(--text-primary); letter-spacing: -0.02em; margin-bottom: 1.25rem; }
.hero-subtitle { font-size: 1.125rem; color: var(--text-secondary); max-width: 44ch; }
.hero-media { position: absolute; top: 0; right: 0; width: 60%; height: 100%; border-radius: 0; clip-path: ellipse(75% 95% at 100% 75%); overflow: hidden; }
.hero-video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.hero-image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
@media (max-width: 991.98px) { .hero { padding: 4.5rem 0; min-height: 500px; } .hero-title { font-size: 2.25rem; line-height: 1.2; margin-bottom: 1.5rem; } .hero-media { position: relative; clip-path: none; border-radius: 16px; margin-top: 1.25rem; width: 100%; height: 100%; min-height: 350px; } }
</style>


