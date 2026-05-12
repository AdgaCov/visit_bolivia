<template>
  <div class="container-fluid p-4">
        <!-- Header -->
        <div class="row mb-4">
          <div class="col-12">
            <h1 class="h3 mb-0">
              <i class="fas fa-tachometer-alt text-primary me-2"></i>
              Dashboard
            </h1>
            <p class="text-muted">Panel de administración de Bolivia Turismo</p>
          </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
          <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                    <i class="fas fa-map-marker-alt text-primary fs-4"></i>
                  </div>
                  <div>
                    <h4 class="mb-1">{{ lugaresTuristicos.length }}</h4>
                    <p class="text-muted mb-0">Lugares Turísticos</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                    <i class="fas fa-globe-americas text-success fs-4"></i>
                  </div>
                  <div>
                    <h4 class="mb-1">{{ regiones.length }}</h4>
                    <p class="text-muted mb-0">Regiones</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                    <i class="fas fa-star text-warning fs-4"></i>
                  </div>
                  <div>
                    <h4 class="mb-1">{{ lugaresDestacados.length }}</h4>
                    <p class="text-muted mb-0">Lugares Destacados</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                    <i class="fas fa-images text-info fs-4"></i>
                  </div>
                  <div>
                    <h4 class="mb-1">{{ totalImagenes }}</h4>
                    <p class="text-muted mb-0">Imágenes</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-transparent">
                <h5 class="mb-0">
                  <i class="fas fa-bolt text-primary me-2"></i>
                  Acciones Rápidas
                </h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <router-link to="/admin/places" class="btn btn-outline-primary w-100">
                      <i class="fas fa-plus me-2"></i>
                      Agregar Lugar
                    </router-link>
                  </div>
                  <div class="col-md-3 mb-3">
                    <router-link to="/admin/places" class="btn btn-outline-success w-100">
                      <i class="fas fa-edit me-2"></i>
                      Editar Lugares
                    </router-link>
                  </div>
                  <div class="col-md-3 mb-3">
                    <router-link to="/admin/images" class="btn btn-outline-info w-100">
                      <i class="fas fa-upload me-2"></i>
                      Subir Imágenes
                    </router-link>
                  </div>
                  <div class="col-md-3 mb-3">
                    <a href="/" target="_blank" class="btn btn-outline-secondary w-100">
                      <i class="fas fa-eye me-2"></i>
                      Ver Sitio Web
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lugares Recientes -->
        <div class="row">
          <div class="col-12">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="fas fa-clock text-primary me-2"></i>
                  Lugares Recientes
                </h5>
                <router-link to="/admin/places" class="btn btn-sm btn-outline-primary">
                  Ver Todos
                </router-link>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Nombre</th>
                        <th>Región</th>
                        <th>Destacado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="lugar in lugaresRecientes" :key="lugar.id">
                        <td>
                          <div class="d-flex align-items-center">
                            <img 
                              :src="lugar.imagenes[0]" 
                              :alt="lugar.nombre"
                              class="rounded me-2"
                              style="width: 40px; height: 40px; object-fit: cover;"
                            >
                            <span>{{ lugar.nombre }}</span>
                          </div>
                        </td>
                        <td>
                          <span class="badge bg-primary">{{ lugar.region }}</span>
                        </td>
                        <td>
                          <i v-if="lugar.destacado" class="fas fa-star text-warning"></i>
                          <span v-else class="text-muted">-</span>
                        </td>
                        <td>
                          <router-link :to="`/admin/places/edit/${lugar.id}`" class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-edit"></i>
                          </router-link>
                          <a :href="`/lugar/${lugar.id}`" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye"></i>
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
  </div>
</template>

<script>
import { lugaresTuristicos } from '@/assets/data/lugares.js'

export default {
  name: 'AdminDashboard',
  data() {
    return {
      lugaresTuristicos: lugaresTuristicos
    }
  },
  computed: {
    lugaresDestacados() {
      return this.lugaresTuristicos.filter(lugar => lugar.destacado)
    },
    regiones() {
      return [...new Set(this.lugaresTuristicos.map(lugar => lugar.region))]
    },
    totalImagenes() {
      return this.lugaresTuristicos.reduce((total, lugar) => total + lugar.imagenes.length, 0)
    },
    lugaresRecientes() {
      return this.lugaresTuristicos.slice(0, 5)
    }
  }
}
</script>

<style scoped>
/* El layout y margen se manejan en AdminLayout.vue */

.card {
  background: var(--card-bg);
  border: 1px solid var(--border-light);
  border-radius: 12px;
}

.table th {
  border-top: none;
  font-weight: 600;
}

.badge {
  font-size: 0.8rem;
}
</style> 