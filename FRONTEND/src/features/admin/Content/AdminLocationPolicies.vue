<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-file-alt text-primary me-2"></i>Políticas de Locaciones</h1>
        <p class="text-muted mb-0">Gestión de políticas de reservas, cancelación, horarios y pagos por locación</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button 
          class="btn btn-primary" 
          @click="openForm()"
        >
          <i class="fas fa-plus me-2"></i>Nueva política
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3 mb-2">
          <div class="col-md-4">
            <input class="form-control" placeholder="Buscar por nombre de locación..." v-model="search">
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterLocationId">
              <option value="">Todas las locaciones</option>
              <option v-for="loc in locationOptions" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Locación</th>
                <th>Reservas</th>
                <th>Cancelación</th>
                <th>Horarios</th>
                <th>Formas de pago</th>
                <th class="text-end" style="width:120px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="6" class="text-center p-4">
                  <span class="spinner-border spinner-border-sm text-primary"></span>
                  <span class="ms-2">Cargando…</span>
                </td>
              </tr>
              <tr v-else-if="filtered.length === 0">
                <td colspan="6" class="text-center p-4 text-muted">
                  <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                  No hay políticas para mostrar
                </td>
              </tr>
              <tr v-else v-for="policy in filtered" :key="policy.id">
                <td>
                  <div><strong>{{ policy.location?.name || '-' }}</strong></div>
                  <small class="text-muted">{{ policy.location?.type || '' }}</small>
                </td>
                <td>
                  <div v-if="policy.reservation_recommended" class="text-success">
                    <i class="fas fa-check-circle"></i> Recomendadas
                  </div>
                  <div v-else class="text-muted">
                    <i class="fas fa-times-circle"></i> No requeridas
                  </div>
                  <small class="text-muted d-block" v-if="policy.reservation_notes" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ policy.reservation_notes }}
                  </small>
                </td>
                <td>
                  <div v-if="policy.cancellation_policy" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ policy.cancellation_policy }}
                  </div>
                  <div v-else class="text-muted">-</div>
                  <small class="text-muted" v-if="policy.cancellation_deadline_hours">
                    {{ formatDeadline(policy.cancellation_deadline_hours) }}
                  </small>
                </td>
                <td>
                  <div v-if="policy.opening_hours" style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ policy.opening_hours }}
                  </div>
                  <div v-else class="text-muted">-</div>
                  <small class="text-muted" v-if="policy.days_closed && policy.days_closed !== 'Ninguno'">
                    Cerrado: {{ policy.days_closed }}
                  </small>
                </td>
                <td>
                  <div class="d-flex flex-wrap gap-1">
                    <span v-if="policy.accepts_cash" class="badge bg-light text-dark">Efectivo</span>
                    <span v-if="policy.accepts_credit_card" class="badge bg-light text-dark">Crédito</span>
                    <span v-if="policy.accepts_debit_card" class="badge bg-light text-dark">Débito</span>
                    <span v-if="policy.accepts_bank_transfer" class="badge bg-light text-dark">Transferencia</span>
                    <span v-if="policy.accepts_digital_wallet" class="badge bg-light text-dark">Digital</span>
                    <span v-if="!policy.accepts_cash && !policy.accepts_credit_card && !policy.accepts_debit_card && !policy.accepts_bank_transfer && !policy.accepts_digital_wallet" class="text-muted">-</span>
                  </div>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(policy)" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(policy.id)" title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal nueva/editar política -->
    <div class="modal fade show d-block" v-if="showForm" style="background:rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ editing ? 'Editar' : 'Nueva' }} política</h4>
            <button type="button" class="btn-close" @click="closeForm" :disabled="formLoading"></button>
          </div>
          <div v-if="formLoading" class="progress" style="height: 4px; border-radius: 0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 100%;"></div>
          </div>
          <div class="modal-body">
            <div v-if="existingPolicyWarning" class="alert alert-warning">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Esta locación ya tiene una política. Si creas una nueva, se reemplazará la existente.
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Locación <span class="text-danger">*</span></label>
                <select class="form-select" v-model="form.location_id" :disabled="editing">
                  <option value="">Selecciona una locación</option>
                  <option v-for="opt in locationOptions" :key="opt.id" :value="opt.id">{{ opt.name }} ({{ opt.type }})</option>
                </select>
                <small class="text-muted">Solo se puede crear una política por locación</small>
              </div>
              
              <!-- Reservas -->
              <div class="col-12">
                <hr>
                <h5 class="mb-3"><i class="fas fa-calendar-check me-2"></i>Reservas</h5>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="form.reservation_recommended" id="reservation_recommended">
                  <label class="form-check-label" for="reservation_recommended">
                    Reservas recomendadas
                  </label>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Notas de reserva</label>
                <textarea class="form-control" rows="2" v-model="form.reservation_notes" placeholder="Ej: Reservas recomendadas todo el año, especialmente en temporada alta..."></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Enlace de reserva</label>
                <input class="form-control" v-model="form.reservation_link" placeholder="https://...">
              </div>

              <!-- Cancelación -->
              <div class="col-12">
                <hr>
                <h5 class="mb-3"><i class="fas fa-times-circle me-2"></i>Cancelación</h5>
              </div>
              <div class="col-md-6">
                <label class="form-label">Plazo de cancelación (horas)</label>
                <input type="number" class="form-control" v-model="form.cancellation_deadline_hours" placeholder="48">
                <small class="text-muted">Ej: 48 horas = 2 días</small>
              </div>
              <div class="col-12">
                <label class="form-label">Política de cancelación</label>
                <textarea class="form-control" rows="3" v-model="form.cancellation_policy" placeholder="Ej: No se realizará reembolso si la cancelación se realiza con menos de 48 horas de anticipación."></textarea>
              </div>

              <!-- Horarios -->
              <div class="col-12">
                <hr>
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Horarios</h5>
              </div>
              <div class="col-12">
                <label class="form-label">Horario de apertura</label>
                <input class="form-control" v-model="form.opening_hours" placeholder="Ej: Recepción 24 horas">
              </div>
              <div class="col-12">
                <label class="form-label">Días cerrado</label>
                <input class="form-control" v-model="form.days_closed" placeholder="Ej: Ninguno, Lunes, etc.">
              </div>

              <!-- Formas de pago -->
              <div class="col-12">
                <hr>
                <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Formas de pago</h5>
              </div>
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="form.accepts_cash" id="accepts_cash">
                  <label class="form-check-label" for="accepts_cash">Efectivo</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="form.accepts_credit_card" id="accepts_credit_card">
                  <label class="form-check-label" for="accepts_credit_card">Tarjeta de crédito</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="form.accepts_debit_card" id="accepts_debit_card">
                  <label class="form-check-label" for="accepts_debit_card">Tarjeta de débito</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="form.accepts_bank_transfer" id="accepts_bank_transfer">
                  <label class="form-check-label" for="accepts_bank_transfer">Transferencia bancaria</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="form.accepts_digital_wallet" id="accepts_digital_wallet">
                  <label class="form-check-label" for="accepts_digital_wallet">Billetera digital</label>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Notas de pago</label>
                <textarea class="form-control" rows="2" v-model="form.payment_notes" placeholder="Ej: Transferencias deben realizarse al menos 72h antes del check-in..."></textarea>
              </div>

              <!-- Campos opcionales adicionales -->
              <div class="col-12">
                <hr>
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información adicional</h5>
              </div>
              <div class="col-md-6">
                <label class="form-label">Duración del evento (horas)</label>
                <input type="number" class="form-control" v-model="form.event_duration_hours" placeholder="Ej: 3">
              </div>
              <div class="col-md-6">
                <label class="form-label">Edad mínima</label>
                <input type="number" class="form-control" v-model="form.minimum_age" placeholder="Ej: 18">
              </div>
              <div class="col-12">
                <label class="form-label">Código de vestimenta</label>
                <input class="form-control" v-model="form.dress_code" placeholder="Ej: Casual, Formal, etc.">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeForm" :disabled="formLoading">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="save" :disabled="formLoading || !form.location_id">
              <span v-if="formLoading" class="spinner-border spinner-border-sm me-2"></span>
              {{ editing ? 'Actualizar' : 'Crear' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import adminLocationPoliciesService, { type AdminLocationPolicy } from '@/services/admin/location-policies.admin.service'
import adminLocationsService from '@/services/admin/locations.admin.service'
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()
const currentUser = computed(() => authStore.currentUser)
const isSuperAdmin = computed(() => {
  const roleId = currentUser.value?.role_id
  return roleId === 1 || roleId === '1'
})

const items = ref<AdminLocationPolicy[]>([])
const loading = ref(false)
const search = ref('')
const filterLocationId = ref('')
const showForm = ref(false)
const editing = ref(false)
const formLoading = ref(false)
const existingPolicyWarning = ref(false)

const locationOptions = ref<Array<{ id: number; name: string; type: string }>>([])

const form = ref({
  id: null as number | null,
  location_id: '',
  reservation_recommended: false,
  reservation_notes: '',
  reservation_link: '',
  cancellation_deadline_hours: null as number | null,
  cancellation_policy: '',
  opening_hours: '',
  days_closed: '',
  accepts_cash: false,
  accepts_credit_card: false,
  accepts_debit_card: false,
  accepts_bank_transfer: false,
  accepts_digital_wallet: false,
  payment_notes: '',
  event_duration_hours: null as number | null,
  minimum_age: null as number | null,
  dress_code: ''
})

const filtered = computed(() => {
  let result = items.value

  if (search.value) {
    const s = search.value.toLowerCase()
    result = result.filter(p => 
      p.location?.name?.toLowerCase().includes(s) ||
      p.reservation_notes?.toLowerCase().includes(s) ||
      p.cancellation_policy?.toLowerCase().includes(s)
    )
  }

  if (filterLocationId.value) {
    result = result.filter(p => String(p.location_id) === String(filterLocationId.value))
  }

  return result
})

const formatDeadline = (hours: number | null): string => {
  if (!hours) return ''
  const days = Math.floor(hours / 24)
  const remainingHours = hours % 24
  if (days > 0) {
    return `${days} día${days > 1 ? 's' : ''}${remainingHours > 0 ? ` y ${remainingHours} hora${remainingHours > 1 ? 's' : ''}` : ''}`
  }
  return `${hours} hora${hours > 1 ? 's' : ''}`
}

const loadLocations = async () => {
  try {
    const response = isSuperAdmin.value 
      ? await adminLocationsService.list({ limit: 1000 })
      : await adminLocationsService.listByUser(currentUser.value?.id || 0, { limit: 1000 })
    
    if (response?.success && response?.data) {
      locationOptions.value = response.data.map(loc => ({
        id: loc.id,
        name: loc.name,
        type: loc.type
      }))
    }
  } catch (error) {
    console.error('Error loading locations:', error)
  }
}

const reload = async () => {
  loading.value = true
  try {
    const userId = currentUser.value?.id
    let response
    // Si es super_admin (role_id = 1), listar todas las políticas
    // Si no, listar solo las del usuario actual
    if (isSuperAdmin.value) {
      response = await adminLocationPoliciesService.list({ limit: 1000 })
    } else if (userId) {
      response = await adminLocationPoliciesService.listByUser(userId, { limit: 1000 })
    } else {
      items.value = []
      return
    }
    
    if (response?.success && response?.data) {
      items.value = response.data
    }
  } catch (error) {
    console.error('Error loading policies:', error)
  } finally {
    loading.value = false
  }
}

const openForm = async (policy?: AdminLocationPolicy) => {
  if (policy) {
    editing.value = true
    form.value = {
      id: policy.id,
      location_id: String(policy.location_id),
      reservation_recommended: policy.reservation_recommended,
      reservation_notes: policy.reservation_notes || '',
      reservation_link: policy.reservation_link || '',
      cancellation_deadline_hours: policy.cancellation_deadline_hours,
      cancellation_policy: policy.cancellation_policy || '',
      opening_hours: policy.opening_hours || '',
      days_closed: policy.days_closed || '',
      accepts_cash: policy.accepts_cash,
      accepts_credit_card: policy.accepts_credit_card,
      accepts_debit_card: policy.accepts_debit_card,
      accepts_bank_transfer: policy.accepts_bank_transfer,
      accepts_digital_wallet: policy.accepts_digital_wallet,
      payment_notes: policy.payment_notes || '',
      event_duration_hours: policy.event_duration_hours,
      minimum_age: policy.minimum_age,
      dress_code: policy.dress_code || ''
    }
    existingPolicyWarning.value = false
  } else {
    editing.value = false
    form.value = {
      id: null,
      location_id: '',
      reservation_recommended: false,
      reservation_notes: '',
      reservation_link: '',
      cancellation_deadline_hours: null,
      cancellation_policy: '',
      opening_hours: '',
      days_closed: '',
      accepts_cash: false,
      accepts_credit_card: false,
      accepts_debit_card: false,
      accepts_bank_transfer: false,
      accepts_digital_wallet: false,
      payment_notes: '',
      event_duration_hours: null,
      minimum_age: null,
      dress_code: ''
    }
    // Verificar si ya existe una política para la locación seleccionada
    checkExistingPolicy()
  }
  showForm.value = true
}

const checkExistingPolicy = async () => {
  if (!form.value.location_id) {
    existingPolicyWarning.value = false
    return
  }
  try {
    const response = await adminLocationPoliciesService.getByLocation(form.value.location_id)
    if (response?.success && response?.data) {
      existingPolicyWarning.value = true
    } else {
      existingPolicyWarning.value = false
    }
  } catch (error) {
    existingPolicyWarning.value = false
  }
}

watch(() => form.value.location_id, () => {
  if (!editing.value) {
    checkExistingPolicy()
  }
})

const closeForm = () => {
  showForm.value = false
  editing.value = false
  formLoading.value = false
  existingPolicyWarning.value = false
}

const save = async () => {
  if (!form.value.location_id) {
    alert('Por favor selecciona una locación')
    return
  }

  formLoading.value = true
  try {
    const body: any = {
      location_id: Number(form.value.location_id),
      reservation_recommended: form.value.reservation_recommended,
      reservation_notes: form.value.reservation_notes || null,
      reservation_link: form.value.reservation_link || null,
      cancellation_deadline_hours: form.value.cancellation_deadline_hours || null,
      cancellation_policy: form.value.cancellation_policy || null,
      opening_hours: form.value.opening_hours || null,
      days_closed: form.value.days_closed || null,
      accepts_cash: form.value.accepts_cash,
      accepts_credit_card: form.value.accepts_credit_card,
      accepts_debit_card: form.value.accepts_debit_card,
      accepts_bank_transfer: form.value.accepts_bank_transfer,
      accepts_digital_wallet: form.value.accepts_digital_wallet,
      payment_notes: form.value.payment_notes || null,
      event_duration_hours: form.value.event_duration_hours || null,
      minimum_age: form.value.minimum_age || null,
      dress_code: form.value.dress_code || null
    }

    console.log('💾 Guardando política:', { editing: editing.value, formId: form.value.id, body })

    let response
    if (editing.value && form.value.id) {
      // Si estamos editando, usar update
      response = await adminLocationPoliciesService.update(form.value.id, body)
    } else {
      // Para crear, usar updateOrCreate que maneja automáticamente si ya existe
      // Esto es más eficiente que verificar primero
      response = await adminLocationPoliciesService.updateOrCreate(form.value.location_id, body)
    }

    console.log('📥 Respuesta del servidor:', response)

    if (response?.success) {
      closeForm()
      await reload()
    } else {
      const errorMsg = response?.message || 'Error al guardar la política'
      console.error('❌ Error en respuesta:', errorMsg)
      alert(errorMsg)
    }
  } catch (error: any) {
    console.error('❌ Error saving policy:', error)
    console.error('Detalles del error:', {
      message: error?.message,
      response: error?.response,
      status: error?.response?.status,
      data: error?.response?.data
    })
    
    const errorMsg = error?.response?.data?.message || 
                     error?.response?.data?.error || 
                     error?.message || 
                     'Error al guardar la política'
    alert(errorMsg)
  } finally {
    formLoading.value = false
  }
}

const remove = async (id: number) => {
  if (!confirm('¿Estás seguro de eliminar esta política?')) return
  
  try {
    console.log('🗑️ Eliminando política:', id)
    const response = await adminLocationPoliciesService.remove(id)
    console.log('📥 Respuesta eliminar:', response)
    
    if (response?.success) {
      await reload()
    } else {
      const errorMsg = response?.message || 'Error al eliminar la política'
      console.error('❌ Error en respuesta:', errorMsg)
      alert(errorMsg)
    }
  } catch (error: any) {
    console.error('❌ Error removing policy:', error)
    console.error('Detalles del error:', {
      message: error?.message,
      response: error?.response,
      status: error?.response?.status,
      data: error?.response?.data
    })
    
    const errorMsg = error?.response?.data?.message || 
                     error?.response?.data?.error || 
                     error?.message || 
                     'Error al eliminar la política'
    alert(errorMsg)
  }
}

onMounted(async () => {
  await loadLocations()
  await reload()
})
</script>

<style scoped>
.modal {
  z-index: 1050;
}
</style>

