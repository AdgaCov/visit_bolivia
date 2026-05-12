<template>
  <div class="color-palette">
    <div class="container py-5">
      <h2 class="text-center mb-5">
        <i class="fas fa-palette text-primary me-3"></i>
        Panel de Colores - Bolivia Turismo
      </h2>

      <!-- Colores del Navbar -->
      <div class="row mb-5">
        <div class="col-12">
          <h3 class="mb-4">🚀 Colores del Navbar</h3>
          <div class="row">
            <div class="col-md-3 mb-3" v-for="color in navbarColors" :key="color.name">
              <div class="color-card">
                <div class="color-preview" :style="{ backgroundColor: color.value }"></div>
                <div class="color-info">
                  <h6>{{ color.name }}</h6>
                  <code>{{ color.value }}</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Variable: {{ color.variable }}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Colores de Categorías -->
      <div class="row mb-5">
        <div class="col-12">
          <h3 class="mb-4">🏷️ Colores de Categorías</h3>
          <div class="row">
            <div class="col-md-3 mb-3" v-for="color in categoryColors" :key="color.name">
              <div class="color-card">
                <div class="color-preview" :style="{ backgroundColor: color.value }"></div>
                <div class="color-info">
                  <h6>{{ color.name }}</h6>
                  <code>{{ color.value }}</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Categoría: {{ color.category }}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Colores de Departamentos -->
      <div class="row mb-5">
        <div class="col-12">
          <h3 class="mb-4">🗺️ Colores de Departamentos</h3>
          <div class="row">
            <div class="col-md-3 mb-3" v-for="color in departmentColors" :key="color.name">
              <div class="color-card">
                <div class="color-preview" :style="{ backgroundColor: color.value }"></div>
                <div class="color-info">
                  <h6>{{ color.name }}</h6>
                  <code>{{ color.value }}</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Departamento: {{ color.department }}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Colores Principales -->
      <div class="row mb-5">
        <div class="col-12">
          <h3 class="mb-4">🎨 Colores Principales</h3>
          <div class="row">
            <div class="col-md-3 mb-3" v-for="color in primaryColors" :key="color.name">
              <div class="color-card">
                <div class="color-preview" :style="{ backgroundColor: color.value }"></div>
                <div class="color-info">
                  <h6>{{ color.name }}</h6>
                  <code>{{ color.value }}</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Clases: {{ color.classes.join(', ') }}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Colores de Bolivia -->
      <div class="row mb-5">
        <div class="col-12">
          <h3 class="mb-4">🇧🇴 Colores de Bolivia</h3>
          <div class="row">
            <div class="col-md-3 mb-3" v-for="color in boliviaColors" :key="color.name">
              <div class="color-card">
                <div class="color-preview" :style="{ backgroundColor: color.value }"></div>
                <div class="color-info">
                  <h6>{{ color.name }}</h6>
                  <code>{{ color.value }}</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Clases: {{ color.classes.join(', ') }}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Colores de Regiones -->
      <div class="row mb-5">
        <div class="col-12">
          <h3 class="mb-4">🌄 Colores de Regiones</h3>
          <div class="row">
            <div class="col-md-3 mb-3" v-for="color in regionColors" :key="color.name">
              <div class="color-card">
                <div class="color-preview" :style="{ backgroundColor: color.value }"></div>
                <div class="color-info">
                  <h6>{{ color.name }}</h6>
                  <code>{{ color.value }}</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Clases: {{ color.classes.join(', ') }}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Colores de Estado -->
      <div class="row mb-5">
        <div class="col-12">
          <h3 class="mb-4">⚡ Colores de Estado</h3>
          <div class="row">
            <div class="col-md-3 mb-3" v-for="color in stateColors" :key="color.name">
              <div class="color-card">
                <div class="color-preview" :style="{ backgroundColor: color.value }"></div>
                <div class="color-info">
                  <h6>{{ color.name }}</h6>
                  <code>{{ color.value }}</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Clases: {{ color.classes.join(', ') }}
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gradientes -->
      <div class="row mb-5">
        <div class="col-12">
          <h3 class="mb-4">🌈 Gradientes</h3>
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="color-card">
                <div class="color-preview gradient-bolivia"></div>
                <div class="color-info">
                  <h6>Gradiente Bolivia</h6>
                  <code>bg-gradient-bolivia</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Clase: .bg-gradient-bolivia
                    </small>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="color-card">
                <div class="color-preview gradient-region"></div>
                <div class="color-info">
                  <h6>Gradiente Región</h6>
                  <code>bg-gradient-region</code>
                  <div class="color-classes">
                    <small class="text-muted">
                      Clase: .bg-gradient-region
                    </small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Información de Uso -->
      <div class="row">
        <div class="col-12">
          <div class="alert alert-info">
            <h5><i class="fas fa-info-circle me-2"></i>¿Cómo usar estos colores?</h5>
            <ul class="mb-0">
              <li><strong>En HTML:</strong> Usa las clases CSS predefinidas (ej: <code>class="bg-bolivia-blue"</code>)</li>
              <li><strong>En CSS:</strong> Usa las variables CSS (ej: <code>color: var(--bolivia-blue)</code>)</li>
              <li><strong>Personalización:</strong> Modifica los valores en <code>src/assets/styles/colors.css</code></li>
              <li><strong>Navbar:</strong> Los colores del navbar se aplican automáticamente usando variables CSS</li>
              <li><strong>Categorías:</strong> Los colores de categorías están definidos en <code>src/constants/categories.ts</code></li>
              <li><strong>Departamentos:</strong> Los colores de departamentos están en <code>src/constants/departments.ts</code></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { categories } from '@/constants/categories'
import { departments } from '@/constants/departments'

export default {
  name: 'ColorPalette',
  data() {
    return {
      navbarColors: [
        { name: 'Navbar Background', value: '#1e3a8a', variable: '--navbar-primary-bg' },
        { name: 'Navbar Text', value: '#ffffff', variable: '--navbar-primary-text' },
        { name: 'Navbar Hover', value: '#059669', variable: '--navbar-primary-hover' },
        { name: 'Navbar Active', value: '#7c3aed', variable: '--navbar-primary-active' },
        { name: 'Brand Color', value: '#d97706', variable: '--navbar-brand-color' },
        { name: 'Link Hover', value: '#d97706', variable: '--navbar-link-hover' }
      ],
      categoryColors: categories.map(cat => ({
        name: cat.name,
        value: cat.color,
        category: cat.name
      })),
      departmentColors: departments.map(dept => ({
        name: dept.name,
        value: dept.color,
        department: dept.name
      })),
      primaryColors: [
        { name: 'Primary', value: '#0d6efd', classes: ['.text-primary', '.bg-primary'] },
        { name: 'Secondary', value: '#6c757d', classes: ['.text-secondary', '.bg-secondary'] },
        { name: 'Success', value: '#198754', classes: ['.text-success', '.bg-success'] },
        { name: 'Info', value: '#0dcaf0', classes: ['.text-info', '.bg-info'] },
        { name: 'Warning', value: '#ffc107', classes: ['.text-warning', '.bg-warning'] },
        { name: 'Danger', value: '#dc3545', classes: ['.text-danger', '.bg-danger'] }
      ],
      boliviaColors: [
        { name: 'Bolivia Blue', value: '#1e3a8a', classes: ['.text-bolivia-blue', '.bg-bolivia-blue'] },
        { name: 'Bolivia Green', value: '#059669', classes: ['.text-bolivia-green', '.bg-bolivia-green'] },
        { name: 'Bolivia Yellow', value: '#d97706', classes: ['.text-bolivia-yellow', '.bg-bolivia-yellow'] },
        { name: 'Bolivia Red', value: '#dc2626', classes: ['.text-bolivia-red', '.bg-bolivia-red'] },
        { name: 'Bolivia Purple', value: '#7c3aed', classes: ['.text-bolivia-purple', '.bg-bolivia-purple'] }
      ],
      regionColors: [
        { name: 'Altiplano', value: '#1e40af', classes: ['.bg-altiplano'] },
        { name: 'Valles', value: '#059669', classes: ['.bg-valles'] },
        { name: 'Yungas', value: '#d97706', classes: ['.bg-yungas'] },
        { name: 'Llanos', value: '#0891b2', classes: ['.bg-llanos'] },
        { name: 'Amazonía', value: '#374151', classes: ['.bg-amazonia'] }
      ],
      stateColors: [
        { name: 'Hover', value: '#0a58ca', classes: ['--hover-color'] },
        { name: 'Active', value: '#084298', classes: ['--active-color'] },
        { name: 'Disabled', value: '#6c757d', classes: ['--disabled-color'] },
        { name: 'Border', value: '#dee2e6', classes: ['--border-color'] }
      ]
    }
  }
}
</script>

<style scoped>
.color-palette {
  background-color: var(--bg-secondary);
  min-height: 100vh;
}

.color-card {
  background: var(--card-bg);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 15px var(--shadow-color);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.color-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px var(--shadow-color);
}

.color-preview {
  height: 120px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 1.2rem;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.color-info {
  padding: 1rem;
}

.color-info h6 {
  margin-bottom: 0.5rem;
  color: var(--text-primary);
}

.color-info code {
  background-color: var(--bg-light);
  color: var(--text-secondary);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.875rem;
}

.color-classes {
  margin-top: 0.5rem;
}

.gradient-bolivia {
  background: linear-gradient(135deg, #0d6efd, #1e3a8a);
}

.gradient-region {
  background: linear-gradient(135deg, #0d6efd, #1e3a8a);
}

.alert {
  border-radius: 12px;
  border: none;
}

.alert-info {
  background-color: var(--alert-info-bg);
  color: var(--alert-info-text);
  border-left: 4px solid var(--alert-info-border);
}
</style>
