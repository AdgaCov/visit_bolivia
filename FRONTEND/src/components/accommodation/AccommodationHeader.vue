<template>
  <header class="accommodation-header text-center mb-4">
    <h1 class="title">{{ title }}</h1>
    <div class="actions" v-if="showFavourite">
      <button type="button" class="fav-btn">
        <i class="far fa-heart me-2"></i>
        {{ favouriteText }}
      </button>
    </div>
    <p class="subtitle" v-if="subtitle">{{ subtitle }}</p>
  </header>
</template>

<script>
export default {
  name: 'AccommodationHeader',
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: '' },
    showFavourite: { type: Boolean, default: true },
    favouriteText: { type: String, default: 'ADD TO FAVOURITES' }
  }
}
</script>

<style scoped>
.accommodation-header { padding-top: 0.5rem; }
.title {
  font-size: 2.75rem;
  font-weight: var(--font-weight-light);
  letter-spacing: -0.025em;
  margin: 0 0 0.75rem;
}
.actions { display: flex; justify-content: center; margin-bottom: 1rem; }
.fav-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.1rem;
  border-radius: 999px;
  border: 2px solid var(--border-color);
  background: var(--white);
  color: var(--text-primary);
  font-weight: 500;
  box-shadow: var(--shadow-sm);
}
.fav-btn:hover { border-color: var(--primary-blue); color: var(--primary-blue); }
.subtitle { color: var(--text-secondary); margin: 0; }

@media (max-width: 768px) {
  .title { font-size: 2rem; }
}
</style>