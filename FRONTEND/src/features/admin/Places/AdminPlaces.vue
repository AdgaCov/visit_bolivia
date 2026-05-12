<template>
  <div class="admin-layout">
    <AdminSidebar />
    
    <div class="admin-content" style="margin-left: 250px;">
      <div class="container-fluid p-4">
        <!-- Header -->
        <div class="row mb-4">
          <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
              <h1 class="h3 mb-0">
                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                Gestión de Lugares
              </h1>
              <p class="text-muted">Administra los lugares turísticos</p>
            </div>
            <button @click="showForm = true" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i>
              Agregar Lugar
            </button>
          </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
          <div class="col-md-4 mb-3">
            <input 
              type="text" 
              class="form-control" 
              placeholder="Buscar lugares..."
              v-model="busqueda"
            >
          </div>
          <div class="col-md-3 mb-3">
            <select class="form-select" v-model="filtroRegion">
              <option value="">Todas las regiones</option>
              <option v-for="region in regiones" :key="region" :value="region">
                {{ region }}
              </option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <select class="form-select" v-model="filtroDestacado">
              <option value="">Todos</option>
              <option value="true">Solo destacados</option>
              <option value="false">No destacados</option>
            </select>
          </div>
          <div class="col-md-2 mb-3">
            <button @click="exportData" class="btn btn-outline-success w-100">
              <i class="fas fa-download me-1"></i>
              Exportar
            </button>
          </div>
        </div>

        <!-- Lista de Lugares -->
        <div class="row">
          <div class="col-12">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Región</th>
                        <th>Destacado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="lugar in lugaresFiltrados" :key="lugar.id">
                        <td>
                          <img 
                            :src="lugar.imagenes[0]" 
                            :alt="lugar.nombre"
                            class="rounded"
                            style="width: 60px; height: 60px; object-fit: cover;"
                          >
                        </td>
                        <td>
                          <div>
                            <strong>{{ lugar.nombre }}</strong>
                            <br>
                            <small class="text-muted">{{ lugar.descripcion.substring(0, 50) }}...</small>
                          </div>
                        </td>
                        <td>
                          <span class="badge bg-primary">{{ lugar.region }}</span>
                        </td>
                        <td>
                          <i v-if="lugar.destacado" class="fas fa-star text-warning fs-5"></i>
                          <span v-else class="text-muted">-</span>
                        </td>
                        <td>
                          <div class="btn-group" role="group">
                            <button @click="editLugar(lugar)" class="btn btn-sm btn-outline-primary">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button @click="deleteLugar(lugar.id)" class="btn btn-sm btn-outline-danger">
                              <i class="fas fa-trash"></i>
                            </button>
                            <a :href="`/lugar/${lugar.id}`" target="_blank" class="btn btn-sm btn-outline-secondary">
                              <i class="fas fa-eye"></i>
                            </a>
                          </div>
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
    </div>

    <!-- Modal de Formulario -->
    <div v-if="showForm" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-edit me-2"></i>
              {{ editingLugar ? 'Editar' : 'Agregar' }} Lugar
            </h5>
            <button @click="closeForm" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveLugar">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nombre *</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    v-model="formData.nombre"
                    required
                  >
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Región *</label>
                  <select class="form-select" v-model="formData.region" required>
                    <option value="">Seleccionar región</option>
                    <option value="Altiplano">Altiplano</option>
                    <option value="Valles">Valles</option>
                    <option value="Llanos">Llanos</option>
                    <option value="Amazonía">Amazonía</option>
                  </select>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Descripción *</label>
                <textarea 
                  class="form-control" 
                  rows="3"
                  v-model="formData.descripcion"
                  required
                ></textarea>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Coordenadas (Latitud)</label>
                  <input 
                    type="number" 
                    class="form-control" 
                    v-model="formData.coordenadas[0]"
                    step="0.0001"
                  >
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Coordenadas (Longitud)</label>
                  <input 
                    type="number" 
                    class="form-control" 
                    v-model="formData.coordenadas[1]"
                    step="0.0001"
                  >
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Mejor Época</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    v-model="formData.informacionPractica.mejorEpoca"
                  >
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Clima</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    v-model="formData.informacionPractica.clima"
                  >
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Precio de Entrada</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    v-model="formData.informacionPractica.precioEntrada"
                  >
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Cómo Llegar</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    v-model="formData.informacionPractica.comoLlegar"
                  >
                </div>
              </div>
              
              <div class="mb-3">
                <div class="form-check">
                  <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="destacado"
                    v-model="formData.destacado"
                  >
                  <label class="form-check-label" for="destacado">
                    Lugar Destacado
                  </label>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">URLs de Imágenes (una por línea)</label>
                <textarea 
                  class="form-control" 
                  rows="3"
                  v-model="imagenesText"
                  placeholder="https://ejemplo.com/imagen1.jpg&#10;https://ejemplo.com/imagen2.jpg"
                ></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="closeForm" type="button" class="btn btn-secondary">
              Cancelar
            </button>
            <button @click="saveLugar" type="button" class="btn btn-primary">
              <i class="fas fa-save me-2"></i>
              Guardar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AdminSidebar from '@/components/AdminSidebar.vue'
import { lugaresTuristicos } from '@/assets/data/lugares.js'

export default {
  name: 'AdminPlaces',
  components: {
    AdminSidebar
  },
  data() {
    return {
      lugaresTuristicos: [...lugaresTuristicos],
      busqueda: '',
      filtroRegion: '',
      filtroDestacado: '',
      showForm: false,
      editingLugar: null,
      formData: {
        nombre: '',
        descripcion: '',
        region: '',
        coordenadas: [-16.5, -68.15],
        imagenes: [],
        informacionPractica: {
          mejorEpoca: '',
          clima: '',
          precioEntrada: '',
          comoLlegar: ''
        },
        destacado: false
      },
      imagenesText: ''
    }
  },
  computed: {
    regiones() {
      return [...new Set(this.lugaresTuristicos.map(lugar => lugar.region))]
    },
    lugaresFiltrados() {
      let filtered = this.lugaresTuristicos

      if (this.busqueda) {
        filtered = filtered.filter(lugar => 
          lugar.nombre.toLowerCase().includes(this.busqueda.toLowerCase()) ||
          lugar.descripcion.toLowerCase().includes(this.busqueda.toLowerCase())
        )
      }

      if (this.filtroRegion) {
        filtered = filtered.filter(lugar => lugar.region === this.filtroRegion)
      }

      if (this.filtroDestacado !== '') {
        filtered = filtered.filter(lugar => lugar.destacado === (this.filtroDestacado === 'true'))
      }

      return filtered
    }
  },
  methods: {
    editLugar(lugar) {
      this.editingLugar = lugar
      this.formData = JSON.parse(JSON.stringify(lugar))
      this.imagenesText = lugar.imagenes.join('\n')
      this.showForm = true
    },
    closeForm() {
      this.showForm = false
      this.editingLugar = null
      this.resetForm()
    },
    resetForm() {
      this.formData = {
        nombre: '',
        descripcion: '',
        region: '',
        coordenadas: [-16.5, -68.15],
        imagenes: [],
        informacionPractica: {
          mejorEpoca: '',
          clima: '',
          precioEntrada: '',
          comoLlegar: ''
        },
        destacado: false
      }
      this.imagenesText = ''
    },
    saveLugar() {
      // Procesar imágenes
      this.formData.imagenes = this.imagenesText
        .split('\n')
        .map(url => url.trim())
        .filter(url => url)

      if (this.editingLugar) {
        // Editar lugar existente
        const index = this.lugaresTuristicos.findIndex(l => l.id === this.editingLugar.id)
        this.lugaresTuristicos[index] = { ...this.formData }
      } else {
        // Agregar nuevo lugar
        const newId = Math.max(...this.lugaresTuristicos.map(l => l.id)) + 1
        this.lugaresTuristicos.push({
          ...this.formData,
          id: newId
        })
      }

      this.closeForm()
      this.$toast?.success('Lugar guardado exitosamente')
    },
    deleteLugar(id) {
      if (confirm('¿Estás seguro de que quieres eliminar este lugar?')) {
        this.lugaresTuristicos = this.lugaresTuristicos.filter(l => l.id !== id)
        this.$toast?.success('Lugar eliminado exitosamente')
      }
    },
    exportData() {
      const dataStr = JSON.stringify(this.lugaresTuristicos, null, 2)
      const dataBlob = new Blob([dataStr], { type: 'application/json' })
      const url = URL.createObjectURL(dataBlob)
      const link = document.createElement('a')
      link.href = url
      link.download = 'lugares-turisticos.json'
      link.click()
      URL.revokeObjectURL(url)
    }
  }
}
</script>

<style scoped>
.admin-layout {
  min-height: 100vh;
  background-color: #f8f9fa;
}

.card {
  border-radius: 12px;
}

.table th {
  border-top: none;
  font-weight: 600;
}

.modal {
  z-index: 1050;
}

.btn-group .btn {
  margin-right: 2px;
}
</style> 