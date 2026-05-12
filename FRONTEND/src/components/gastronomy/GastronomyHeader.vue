<template>
  <header class="gastronomy-header text-center mb-4">
    <h1 class="title">{{ title }}</h1>
    <div class="actions" v-if="showFavourite">
      <button 
        type="button" 
        class="fav-btn"
        :class="{ active: favoriteActive }"
        :disabled="favoriteLoading"
        @click="$emit('toggle-favorite')"
      >
        <i :class="[favoriteActive ? 'fas fa-heart' : 'far fa-heart', 'me-2']"></i>
        <span v-if="favoriteLoading">Guardando...</span>
        <span v-else>{{ favouriteText }}</span>
      </button>
    </div>
    <p class="subtitle" v-if="subtitle">{{ subtitle }}</p>
  </header>
</template>

<script>
export default {
  name: 'GastronomyHeader',
  emits: ['toggle-favorite'],
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: '' },
    showFavourite: { type: Boolean, default: true },
    favouriteText: { type: String, default: 'AGREGAR A FAVORITOS' },
    favoriteActive: { type: Boolean, default: false },
    favoriteLoading: { type: Boolean, default: false }
  }
}
</script>

<style scoped>
.gastronomy-header { padding-top: 0.5rem; }
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
.fav-btn:hover:not(:disabled) { border-color: var(--primary-blue); color: var(--primary-blue); }
.fav-btn.active {
  border-color: var(--primary-blue);
  background: var(--primary-blue);
  color: #fff;
}
.fav-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
.subtitle { color: var(--text-secondary); margin: 0; }

@media (max-width: 768px) {
  .title { font-size: 2rem; }
}
</style>








<!-- ALTER TABLE articles ADD COLUMN page_section ENUM('home','whattodo','wherego','planning') DEFAULT 'home';
ALTER TABLE articles ADD COLUMN section_name VARCHAR(50); -- 'hero', 'highlights', 'categories'
ALTER TABLE articles ADD COLUMN sort_order INT DEFAULT 0;
ALTER TABLE articles ADD COLUMN is_featured BOOLEAN DEFAULT FALSE;


-- Artículos para HomePage
INSERT INTO articles (title, content, page_section, section_name, template_id) VALUES
('Bienvenidos a Bolivia', 'Contenido...', 'home', 'hero', 1),
('Destinos Destacados', 'Contenido...', 'home', 'highlights', 2);

-- Artículos para WhatDoPage
INSERT INTO articles (title, content, page_section, section_name, template_id) VALUES
('Aventuras en Uyuni', 'Contenido...', 'whattodo', 'activities', 3),
('Festivales Culturales', 'Contenido...', 'whattodo', 'events', 4);

-- Artículos para WhereGoPage
INSERT INTO articles (title, content, page_section, section_name, template_id) VALUES
('Ciudades Imperdibles', 'Contenido...', 'wherego', 'cities', 5),
('Rutas Recomendadas', 'Contenido...', 'wherego', 'routes', 6); -->