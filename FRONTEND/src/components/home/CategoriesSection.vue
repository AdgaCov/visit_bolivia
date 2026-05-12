<template>
  <section class="categories-section">
    <div class="container">
      <h2 class="section-title">Explora por Categoría</h2>
      <div class="categories-grid">
        <div 
          class="category-card" 
          v-for="category in categories" 
          :key="category.id"
          @click="navigateToCategory(category)"
        >
          <div class="category-icon">
            <i :class="category.icon"></i>
          </div>
          <h3 class="category-name">{{ category.name }}</h3>
          <p class="category-description">{{ category.description }}</p>
          <span class="category-count">{{ category.count }} lugares</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { categories } from '@/constants/categories'

export default {
  name: 'CategoriesSection',
  data() {
    return {
      categories
    }
  },
  methods: {
    navigateToCategory(category) {
      // Navegar a la página específica de la categoría
      const slug = this.getCategorySlug(category.name)
      this.$router.push(`/que-hacer/${slug}`)
    },
    getCategorySlug(categoryName) {
      return categoryName
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // Remover acentos
        .replace(/[^a-z0-9\s-]/g, '') // Solo letras, números, espacios y guiones
        .replace(/\s+/g, '-') // Reemplazar espacios con guiones
        .replace(/-+/g, '-') // Múltiples guiones por uno solo
        .trim()
    }
  }
}
</script>

<style scoped>
/* Sección de Categorías */
.categories-section {
  padding: 4rem 0;
  background: var(--bg-secondary);
}

.section-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: 3rem;
  letter-spacing: -0.01em;
}

/* Grid de Categorías */
.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.category-card {
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  padding: 2.5rem 2rem;
  text-align: center;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.category-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--border-light) 0%, var(--border-color) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.category-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-sm);
  border-color: var(--border-color);
}

.category-card:hover::before {
  opacity: 1;
}

.category-icon {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
  color: var(--text-secondary);
  opacity: 0.7;
  transition: all 0.3s ease;
}

.category-card:hover .category-icon {
  color: var(--text-primary);
  opacity: 1;
  transform: scale(1.05);
}

.category-name {
  font-size: 1.125rem;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 1rem;
  letter-spacing: -0.01em;
}

.category-description {
  color: var(--text-secondary);
  margin-bottom: 1.5rem;
  line-height: 1.6;
  font-size: 0.95rem;
}

.category-count {
  color: var(--text-muted);
  font-weight: 400;
  font-size: 0.875rem;
  padding: 0.5rem 1rem;
  background: var(--bg-secondary);
  border-radius: 6px;
  display: inline-block;
  border: 1px solid var(--border-light);
  transition: all 0.3s ease;
}

.category-card:hover .category-count {
  background: var(--white);
  color: var(--text-primary);
  border-color: var(--border-color);
}

/* Responsive */
@media (max-width: 768px) {
  .categories-grid {
    grid-template-columns: 1fr;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .category-card {
    padding: 2rem 1.5rem;
  }
  
  .category-icon {
    font-size: 2rem;
  }
}
</style>
