<template>
  <div class="container-fluid p-4">
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h3 mb-1">
            <i class="fas fa-tags text-primary me-2"></i>
            Planes
          </h1>
          <p class="text-muted">Gestión de planes y sus límites</p>
        </div>
        <button class="btn btn-primary" @click="openForm()">
          <i class="fas fa-plus me-2"></i>
          Nuevo plan
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="loading" class="text-center p-4">
          <span class="spinner-border spinner-border-sm text-primary"></span>
          <span class="ms-2">Cargando planes...</span>
        </div>
        <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Límites</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="plan in plans" :key="plan.id">
                <td>
                  <strong>{{ plan.name }}</strong>
                </td>
                <td>{{ plan.description }}</td>
                <td>
                  <span v-if="plan.price === '0.00' || plan.price === '0'">Gratis</span>
                  <span v-else>{{ plan.price }} {{ plan.price_currency }}</span>
                </td>
                <td>
                  <small class="d-block">
                    <i class="fas fa-bed text-muted me-1"></i>
                    Locaciones: <strong>{{ plan.max_locations }}</strong>
                  </small>
                  <small class="d-block">
                    <i class="fas fa-image text-muted me-1"></i>
                    Imágenes: <strong>{{ plan.max_location_images }}</strong>
                  </small>
                  <small class="d-block">
                    <i class="fas fa-list text-muted me-1"></i>
                    Items: <strong>{{ plan.max_location_items }}</strong>
                  </small>
                </td>
                <td>
                  <button 
                    type="button"
                    :class="['badge', 'border-0', 'btn', plan.is_active ? 'bg-success' : 'bg-secondary']"
                    @click="toggleActive(plan)"
                    :disabled="loading"
                    :title="plan.is_active ? 'Desactivar plan' : 'Activar plan'"
                    style="cursor: pointer;"
                  >
                    {{ plan.is_active ? 'Activo' : 'Inactivo' }}
                  </button>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(plan)">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(plan.id)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="!loading && plans.length === 0" class="text-center p-4 text-muted">
            No hay planes registrados
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de formulario -->
    <div v-if="show" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editing ? 'Editar' : 'Nuevo' }} plan</h5>
            <button type="button" class="btn-close" @click="close" :disabled="formLoading"></button>
          </div>
          <!-- Progress Bar -->
          <div v-if="formLoading" class="progress" style="height: 4px; border-radius: 0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                 role="progressbar" 
                 style="width: 100%;">
            </div>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control" 
                  v-model="form.name" 
                  :disabled="formLoading"
                  placeholder="Ej: Plan Básico"
                >
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Moneda</label>
                <select class="form-select" v-model="form.price_currency" :disabled="formLoading">
                  <option value="BOB">BOB</option>
                  <option value="USD">USD</option>
                  <option value="EUR">EUR</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea 
                class="form-control" 
                v-model="form.description" 
                :disabled="formLoading"
                rows="2"
                placeholder="Ej: Ideal para empezar"
              ></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Precio <span class="text-danger">*</span></label>
              <input 
                type="number" 
                step="0.01" 
                min="0"
                class="form-control" 
                v-model.number="form.price" 
                :disabled="formLoading"
                placeholder="0.00"
              >
              <small class="text-muted">Use 0.00 para planes gratuitos</small>
            </div>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Máx. Locaciones <span class="text-danger">*</span></label>
                <input 
                  type="number" 
                  min="0"
                  class="form-control" 
                  v-model.number="form.max_locations" 
                  :disabled="formLoading"
                >
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Máx. Imágenes <span class="text-danger">*</span></label>
                <input 
                  type="number" 
                  min="0"
                  class="form-control" 
                  v-model.number="form.max_location_images" 
                  :disabled="formLoading"
                >
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Máx. Items <span class="text-danger">*</span></label>
                <input 
                  type="number" 
                  min="0"
                  class="form-control" 
                  v-model.number="form.max_location_items" 
                  :disabled="formLoading"
                >
              </div>
            </div>
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="checkbox" 
                id="is_active" 
                v-model="form.is_active" 
                :disabled="formLoading"
              >
              <label class="form-check-label" for="is_active">Plan activo</label>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="close" :disabled="formLoading">Cancelar</button>
            <button class="btn btn-primary" @click="save" :disabled="formLoading">
              <span v-if="formLoading" class="spinner-border spinner-border-sm me-2"></span>
              <span v-if="formLoading">{{ editing ? 'Actualizando...' : 'Guardando...' }}</span>
              <span v-else>{{ editing ? 'Actualizar' : 'Crear' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import adminPlansService from '@/services/admin/plans.admin.service'

export default {
  name: 'AdminPlans',
  data() {
    return {
      loading: true,
      error: '',
      show: false,
      editing: false,
      formLoading: false,
      plans: [],
      form: {
        id: 0,
        name: '',
        description: '',
        price: '0.00',
        price_currency: 'BOB',
        max_locations: 1,
        max_location_images: 3,
        max_location_items: 3,
        is_active: true
      }
    }
  },
  mounted() {
    this.reload()
  },
  methods: {
    async reload() {
      this.loading = true
      this.error = ''
      try {
        const res = await adminPlansService.list()
        this.plans = res.data || []
      } catch (e) {
        this.error = e?.response?.data?.message || e?.message || 'Error al cargar planes'
        this.plans = []
        console.error('Error al cargar planes:', e)
      } finally {
        this.loading = false
      }
    },
    openForm(plan) {
      if (plan) {
        this.editing = true
        this.form = {
          id: plan.id,
          name: plan.name || '',
          description: plan.description || '',
          price: plan.price || '0.00',
          price_currency: plan.price_currency || 'BOB',
          max_locations: plan.max_locations || 1,
          max_location_images: plan.max_location_images || 3,
          max_location_items: plan.max_location_items || 3,
          is_active: plan.is_active !== undefined ? plan.is_active : true
        }
      } else {
        this.editing = false
        this.form = {
          id: 0,
          name: '',
          description: '',
          price: '0.00',
          price_currency: 'BOB',
          max_locations: 1,
          max_location_images: 3,
          max_location_items: 3,
          is_active: true
        }
      }
      this.show = true
    },
    close() {
      if (!this.formLoading) {
        this.show = false
      }
    },
    async save() {
      if (!this.form.name || !this.form.name.trim()) {
        alert('El nombre del plan es requerido')
        return
      }
      if (this.form.price === null || this.form.price === undefined || this.form.price < 0) {
        alert('El precio debe ser un número válido mayor o igual a 0')
        return
      }
      if (this.form.max_locations === null || this.form.max_locations === undefined || this.form.max_locations < 0) {
        alert('El máximo de locaciones debe ser un número válido mayor o igual a 0')
        return
      }
      if (this.form.max_location_images === null || this.form.max_location_images === undefined || this.form.max_location_images < 0) {
        alert('El máximo de imágenes debe ser un número válido mayor o igual a 0')
        return
      }
      if (this.form.max_location_items === null || this.form.max_location_items === undefined || this.form.max_location_items < 0) {
        alert('El máximo de items debe ser un número válido mayor o igual a 0')
        return
      }

      this.formLoading = true
      try {
        const payload = {
          name: this.form.name.trim(),
          description: this.form.description.trim(),
          price: String(this.form.price),
          price_currency: this.form.price_currency,
          max_locations: Number(this.form.max_locations),
          max_location_images: Number(this.form.max_location_images),
          max_location_items: Number(this.form.max_location_items),
          is_active: this.form.is_active
        }

        if (this.editing && this.form.id > 0) {
          await adminPlansService.update(this.form.id, payload)
        } else {
          await adminPlansService.create(payload)
        }
        this.show = false
        await this.reload()
      } catch (e) {
        const action = this.editing ? 'actualizar' : 'crear'
        alert(`No se pudo ${action} el plan: ` + (e?.response?.data?.message || e?.message))
      } finally {
        this.formLoading = false
      }
    },
    async remove(id) {
      if (!confirm('¿Eliminar este plan?')) return
      try {
        await adminPlansService.remove(id)
        await this.reload()
      } catch (e) {
        alert('No se pudo eliminar el plan: ' + (e?.response?.data?.message || e?.message))
      }
    },
    async toggleActive(plan) {
      try {
        await adminPlansService.toggleActive(plan.id)
        const index = this.plans.findIndex(p => p.id === plan.id)
        if (index !== -1) {
          this.plans[index].is_active = !plan.is_active
        }
      } catch (e) {
        alert('No se pudo cambiar el estado del plan: ' + (e?.response?.data?.message || e?.message))
        await this.reload()
      }
    }
  }
}
</script>

<style scoped>
.card {
  border-radius: 12px;
}
</style>

