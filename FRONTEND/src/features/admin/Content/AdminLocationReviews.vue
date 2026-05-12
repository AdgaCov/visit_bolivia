<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-star text-primary me-2"></i>Reviews de Locaciones</h1>
        <p class="text-muted mb-0">Gestión de calificaciones y comentarios de usuarios sobre locaciones</p>
      </div>
      <button class="btn btn-primary" @click="openForm()"><i class="fas fa-plus me-2"></i>Nuevo review</button>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3 mb-2">
          <div class="col-md-4"><input class="form-control" placeholder="Buscar por comentario o usuario..." v-model="search"></div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterLocationId">
              <option value="">Todas las locaciones</option>
              <option v-for="loc in locationOptions" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterRating">
              <option value="">Todas las calificaciones</option>
              <option value="5">5 estrellas</option>
              <option value="4">4 estrellas</option>
              <option value="3">3 estrellas</option>
              <option value="2">2 estrellas</option>
              <option value="1">1 estrella</option>
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
                <th>Usuario</th>
                <th>Calificación</th>
                <th>Comentario</th>
                <th>Fecha</th>
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
              <tr v-else-if="paginated.length === 0">
                <td colspan="6" class="text-center p-4 text-muted">
                  <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                  No hay reviews para mostrar
                </td>
              </tr>
              <tr v-else v-for="review in paginated" :key="review.id">
                <td>
                  <div><strong>{{ review.location?.name || 'N/A' }}</strong></div>
                  <small class="text-muted">{{ review.location?.type || '' }}</small>
                </td>
                <td>
                  <div>{{ review.user?.name || 'Anónimo' }}</div>
                  <small class="text-muted">{{ review.user?.email || '' }}</small>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-1">
                    <span class="badge bg-warning text-dark">
                      <i class="fas fa-star"></i> {{ review.rating }}
                    </span>
                    <div class="text-warning">
                      <i v-for="n in 5" :key="n" 
                         :class="n <= review.rating ? 'fas fa-star' : 'far fa-star'"
                         style="font-size: 0.8rem;"></i>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="text-muted small" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ review.comment || '-' }}
                  </div>
                </td>
                <td>
                  <span class="text-muted small">{{ formatDate(review.created_at) }}</span>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(review)" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(review.id)" title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="!loading && filtered.length === 0 && items.length > 0" class="text-center p-4 text-muted">
          <i class="fas fa-search mb-2"></i>
          <p>No se encontraron resultados con los filtros aplicados</p>
        </div>
        <div v-if="!loading && items.length === 0" class="text-center p-4 text-muted">
          <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
          <p>No hay reviews disponibles. <button class="btn btn-sm btn-primary" @click="openForm()">Crear el primero</button></p>
        </div>

        <div v-if="totalPages > 1" class="card-footer bg-white border-top">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <small class="text-muted">
              Mostrando {{ filtered.length ? (pagination.offset + 1) : 0 }}–{{ Math.min(pagination.offset + pagination.limit, filtered.length) }} de {{ filtered.length }} reviews
            </small>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item" :class="{ disabled: currentPage <= 1 }" @click="setPage(1)">
                  <span class="page-link">Primera</span>
                </li>
                <li class="page-item" :class="{ disabled: currentPage <= 1 }" @click="setPage(currentPage - 1)">
                  <span class="page-link">«</span>
                </li>
                <li class="page-item" v-for="p in pages" :key="p" :class="{ active: currentPage === p }" @click="setPage(p)">
                  <span class="page-link">{{ p }}</span>
                </li>
                <li class="page-item" :class="{ disabled: currentPage >= totalPages }" @click="setPage(currentPage + 1)">
                  <span class="page-link">»</span>
                </li>
                <li class="page-item" :class="{ disabled: currentPage >= totalPages }" @click="setPage(totalPages)">
                  <span class="page-link">Última</span>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal nueva/editar review -->
    <div class="modal fade show d-block" v-if="showForm" style="background:rgba(0,0,0,0.18);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ editing ? 'Editar' : 'Nuevo' }} review</h4>
            <button type="button" class="btn-close" @click="closeForm" :disabled="formLoading"></button>
          </div>
          <!-- Progress Bar -->
          <div v-if="formLoading" class="progress" style="height: 4px; border-radius: 0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                 role="progressbar" 
                 style="width: 100%;">
            </div>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Locación <span class="text-danger">*</span></label>
                <select class="form-select" v-model="form.location_id" :disabled="formLoading">
                  <option value="">Selecciona una locación</option>
                  <option v-for="opt in locationOptions" :key="opt.id" :value="opt.id">{{ opt.name }} ({{ opt.type }})</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Usuario (opcional)</label>
                <select class="form-select" v-model="form.user_id" :disabled="formLoading">
                  <option value="">Sin usuario (anónimo)</option>
                  <option v-for="user in userOptions" :key="user.id" :value="user.id">{{ user.name }} ({{ user.email }})</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Calificación <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center gap-3">
                  <select class="form-select" v-model="form.rating" :disabled="formLoading" style="max-width: 150px;">
                    <option value="">Selecciona</option>
                    <option value="1">1 estrella</option>
                    <option value="2">2 estrellas</option>
                    <option value="3">3 estrellas</option>
                    <option value="4">4 estrellas</option>
                    <option value="5">5 estrellas</option>
                  </select>
                  <div v-if="form.rating" class="text-warning">
                    <i v-for="n in 5" :key="n" 
                       :class="n <= Number(form.rating) ? 'fas fa-star' : 'far fa-star'"
                       style="font-size: 1.5rem;"></i>
                  </div>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Comentario</label>
                <textarea 
                  class="form-control" 
                  rows="4" 
                  v-model="form.comment" 
                  placeholder="Escribe un comentario sobre la locación..."
                  :disabled="formLoading"
                ></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeForm" :disabled="formLoading">Cancelar</button>
            <button class="btn btn-primary" @click="saveReview" :disabled="formLoading">
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

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import locationReviewsService from '@/services/admin/location-reviews.admin.service'
import adminLocationsService from '@/services/admin/locations.admin.service'
import adminUsersService from '@/services/admin/users.admin.service'

const items = ref([])
const loading = ref(false)
const search = ref('')
const filterLocationId = ref('')
const filterRating = ref('')
const locationOptions = ref([])
const userOptions = ref([])
const pagination = reactive({ limit: 20, offset: 0 })
const showForm = ref(false)
const editing = ref(false)
const formLoading = ref(false)
const form = reactive({ 
  id: 0, 
  location_id: '', 
  user_id: '', 
  rating: '', 
  comment: '' 
})

const filtered = computed(() => {
  return items.value.filter(review => {
    let pass = true
    if (search.value) {
      const s = search.value.toLowerCase()
      pass = (review.comment && review.comment.toLowerCase().includes(s)) || 
             (review.user?.name && review.user.name.toLowerCase().includes(s)) ||
             (review.user?.email && review.user.email.toLowerCase().includes(s)) ||
             (review.location?.name && review.location.name.toLowerCase().includes(s))
    }
    if (filterLocationId.value) pass = pass && String(review.location_id) === String(filterLocationId.value)
    if (filterRating.value) pass = pass && String(review.rating) === String(filterRating.value)
    return pass
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / pagination.limit)))
const currentPage = computed(() => Math.floor(pagination.offset / pagination.limit) + 1)
const pages = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  const start = Math.max(1, current - 2)
  const end = Math.min(total, current + 2)
  const arr = []
  for (let i = start; i <= end; i++) arr.push(i)
  return arr
})
const paginated = computed(() => filtered.value.slice(pagination.offset, pagination.offset + pagination.limit))

function setPage(page) {
  const p = Math.min(Math.max(1, page), totalPages.value)
  pagination.offset = (p - 1) * pagination.limit
}

function formatDate(dateString) {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('es-BO', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

async function reload() {
  loading.value = true
  try {
    console.log('🔄 Iniciando carga de reviews...')
    const response = await locationReviewsService.list({ limit: 9999 })
    console.log('✅ Respuesta recibida:', response)
    console.log('📊 Tipo de respuesta:', typeof response)
    console.log('📦 response.success:', response?.success)
    console.log('📦 response.data existe?:', !!response?.data)
    console.log('📦 response.data es array?:', Array.isArray(response?.data))
    
    // La API devuelve: { success: true, data: [{ location: {...}, reviews: [...], ... }] }
    // Necesitamos aplanar para mostrar cada review individualmente
    const flattenedReviews = []
    
    if (response && response.success && Array.isArray(response.data)) {
      console.log(`📋 Procesando ${response.data.length} grupos de locaciones`)
      
      response.data.forEach((locationGroup, index) => {
        console.log(`📍 Grupo ${index + 1}:`, {
          locationId: locationGroup.location?.id,
          locationName: locationGroup.location?.name,
          reviewsCount: locationGroup.reviews?.length || 0
        })
        
        // Cada locationGroup tiene: location, reviews[], average_rating, review_count
        if (locationGroup.reviews && Array.isArray(locationGroup.reviews) && locationGroup.reviews.length > 0) {
          locationGroup.reviews.forEach(review => {
            flattenedReviews.push({
              ...review,
              location_id: locationGroup.location?.id,
              location: locationGroup.location
            })
          })
        } else {
          console.warn(`⚠️ Grupo ${index + 1} no tiene reviews`)
        }
      })
    } else {
      console.error('❌ Estructura de respuesta inválida:', response)
    }
    
    items.value = flattenedReviews
    console.log(`✅ Total reviews cargados: ${flattenedReviews.length}`)
    if (flattenedReviews.length > 0) {
      console.log('📋 Primer review:', flattenedReviews[0])
    }
    
    // Cargar locaciones si no están cargadas
    if (!locationOptions.value.length) {
      const resLoc = await adminLocationsService.list({ limit: 9999 })
      if (resLoc && resLoc.success === true && Array.isArray(resLoc.data)) {
        locationOptions.value = resLoc.data.map(l => ({ id: l.id, name: l.name, type: l.type }))
      } else if (Array.isArray(resLoc)) {
        locationOptions.value = resLoc.map(l => ({ id: l.id, name: l.name, type: l.type }))
      } else if (resLoc && Array.isArray(resLoc.data)) {
        locationOptions.value = resLoc.data.map(l => ({ id: l.id, name: l.name, type: l.type }))
      }
    }
    
    // Cargar usuarios si no están cargados
    if (!userOptions.value.length) {
      try {
        const resUsers = await adminUsersService.list()
        if (resUsers && resUsers.success === true && Array.isArray(resUsers.data)) {
          userOptions.value = resUsers.data.map(u => ({ id: u.id, name: u.name, email: u.email }))
        } else if (Array.isArray(resUsers)) {
          userOptions.value = resUsers.map(u => ({ id: u.id, name: u.name, email: u.email }))
        } else if (resUsers && Array.isArray(resUsers.data)) {
          userOptions.value = resUsers.data.map(u => ({ id: u.id, name: u.name, email: u.email }))
        }
      } catch (e) {
        console.warn('No se pudieron cargar usuarios:', e)
        userOptions.value = []
      }
    }
  } catch (e) {
    console.error('❌ Error cargando reviews:', e)
    console.error('❌ Detalles del error:', e?.response?.data || e?.message || e)
    console.error('❌ Stack:', e?.stack)
    items.value = []
    alert('Error al cargar reviews: ' + (e?.response?.data?.message || e?.message || 'Error desconocido'))
  } finally {
    loading.value = false
    console.log('✅ Carga completada. Estado final:', {
      itemsCount: items.value.length,
      loading: loading.value
    })
  }
}

onMounted(reload)

function openForm(review = null) {
  if (review) {
    editing.value = true
    form.id = review.id
    form.location_id = review.location_id ? String(review.location_id) : ''
    form.user_id = review.user_id ? String(review.user_id) : ''
    form.rating = review.rating ? String(review.rating) : ''
    form.comment = review.comment || ''
  } else {
    editing.value = false
    form.id = 0
    form.location_id = ''
    form.user_id = ''
    form.rating = ''
    form.comment = ''
  }
  showForm.value = true
}

function closeForm() { 
  if (!formLoading.value) {
    showForm.value = false 
  }
}

async function saveReview() {
  if (!form.location_id) {
    alert('Selecciona la locación')
    return
  }
  if (!form.rating || !form.rating.trim()) {
    alert('La calificación es requerida')
    return
  }
  const ratingNum = Number(form.rating)
  if (ratingNum < 1 || ratingNum > 5) {
    alert('La calificación debe estar entre 1 y 5')
    return
  }
  
  formLoading.value = true
  try {
    // Construir payload solo con los campos necesarios (sin timestamps)
    const payload = {
      location_id: Number(form.location_id),
      user_id: form.user_id ? Number(form.user_id) : null,
      rating: ratingNum,
      comment: form.comment ? form.comment.trim() : null
    }
    
    // Asegurar que no se envíen campos extra
    const cleanPayload = {
      location_id: payload.location_id,
      user_id: payload.user_id,
      rating: payload.rating,
      comment: payload.comment || null
    }
    
    console.log('📤 Payload a enviar:', cleanPayload)
    console.log('📋 Campos del payload:', Object.keys(cleanPayload))
    
    if (editing.value && form.id) {
      console.log('✏️ Actualizando review:', form.id)
      const updatePayload = {
        rating: cleanPayload.rating,
        comment: cleanPayload.comment
      }
      const result = await locationReviewsService.update(form.id, updatePayload)
      console.log('✅ Review actualizado:', result)
    } else {
      console.log('➕ Creando nuevo review')
      console.log('📤 Enviando a POST /api/location-reviews:', cleanPayload)
      const result = await locationReviewsService.create(cleanPayload)
      console.log('✅ Review creado:', result)
    }
    
    formLoading.value = false
    showForm.value = false
    await reload()
  } catch (e) {
    console.error('Error guardando review:', e)
    console.error('Detalles:', e?.response?.data || e?.message || e)
    alert('No se pudo guardar el review: ' + (e?.response?.data?.message || e?.message || 'Error'))
    formLoading.value = false
  }
}

async function remove(id) {
  if (!confirm('¿Eliminar este review?')) return
  try {
    console.log('Eliminando review:', id)
    await locationReviewsService.remove(id)
    console.log('Review eliminado exitosamente')
    await reload()
  } catch (e) {
    console.error('Error eliminando review:', e)
    console.error('Detalles:', e?.response?.data || e?.message || e)
    alert('No se pudo eliminar el review: ' + (e?.response?.data?.message || e?.message || 'Error'))
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>

