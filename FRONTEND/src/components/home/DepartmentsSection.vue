<template>
  <section class="departments-section">
    <div class="container">
      <h2 class="section-title">Descubre los Departamentos</h2>
      <div class="departments-grid">
        <div 
          class="department-card" 
          v-for="dept in departments" 
          :key="dept.id"
          @click="navigateToDepartment(dept)"
        >
          <div class="department-header">
            <div class="department-icon">
              <i :class="dept.icon"></i>
            </div>
            <div class="department-info">
              <h3 class="department-name">{{ dept.name }}</h3>
              <span class="department-short">{{ dept.shortName }}</span>
            </div>
          </div>
          <p class="department-description">{{ dept.description }}</p>
          <div class="department-highlights">
            <span 
              class="highlight-tag" 
              v-for="highlight in dept.highlights.slice(0, 3)" 
              :key="highlight"
            >
              {{ highlight }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { departments } from '@/constants/departments'

export default {
  name: 'DepartmentsSection',
  data() {
    return {
      departments
    }
  },
  methods: {
    navigateToDepartment(department) {
      // Navegar a la página específica del departamento
      const slug = this.getDepartmentSlug(department.name)
      this.$router.push(`/departamento/${slug}`)
    },
    getDepartmentSlug(departmentName) {
      return departmentName
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
/* Sección de Departamentos */
.departments-section {
  padding: 4rem 0;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: 3rem;
  letter-spacing: -0.01em;
}

/* Grid de Departamentos */
.departments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.department-card {
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  padding: 1.75rem;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.department-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--border-light) 0%, var(--border-color) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.department-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-sm);
  border-color: var(--border-color);
}

.department-card:hover::before {
  opacity: 1;
}

.department-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.department-icon {
  font-size: 1.5rem;
  color: var(--text-secondary);
  opacity: 0.8;
  transition: all 0.3s ease;
}

.department-card:hover .department-icon {
  color: var(--text-primary);
  opacity: 1;
}

.department-name {
  font-size: 1.125rem;
  font-weight: 500;
  color: var(--text-primary);
  margin: 0;
  letter-spacing: -0.01em;
}

.department-short {
  color: var(--text-muted);
  font-size: 0.875rem;
  font-weight: 400;
  opacity: 0.8;
}

.department-description {
  color: var(--text-secondary);
  margin-bottom: 1.25rem;
  line-height: 1.6;
  font-size: 0.95rem;
}

.department-highlights {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.highlight-tag {
  background: var(--bg-secondary);
  color: var(--text-secondary);
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 400;
  border: 1px solid var(--border-light);
  transition: all 0.3s ease;
}

.department-card:hover .highlight-tag {
  background: var(--white);
  color: var(--text-primary);
  border-color: var(--border-color);
}

/* Responsive */
@media (max-width: 768px) {
  .departments-grid {
    grid-template-columns: 1fr;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .department-card {
    padding: 1.5rem;
  }
  
  .department-header {
    gap: 0.75rem;
  }
  
  .department-icon {
    font-size: 1.25rem;
  }
}
</style>
