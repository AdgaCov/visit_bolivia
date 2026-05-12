<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-list text-primary me-2"></i>Items de Locaciones</h1>
        <p class="text-muted mb-0">Gestión de platos, servicios, habitaciones y ofertas por locación</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div v-if="!isSuperAdmin && planLimits && planLimits.max_location_items !== null" class="text-muted small">
          <i class="fas fa-info-circle"></i>
          {{ itemsLimitMessage }}
        </div>
        <div v-else-if="!isSuperAdmin && planLimitsComposable.loadingPlan.value" class="text-muted small">
          <i class="fas fa-spinner fa-spin"></i>
          Cargando límites del plan...
        </div>
        <button 
          class="btn btn-primary" 
          @click="openForm()"
          :disabled="!canCreateNewItem"
          :title="!canCreateNewItem ? 'Has alcanzado el límite de items permitidos en tu plan' : ''"
        >
          <i class="fas fa-plus me-2"></i>Nuevo item
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3 mb-2">
          <div class="col-md-4"><input class="form-control" placeholder="Buscar por nombre o descripción..." v-model="search"></div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterLocationId">
              <option value="">Todas las locaciones</option>
              <option v-for="loc in locationOptions" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterType">
              <option value="">Todos los tipos</option>
              <option v-for="type in itemTypeOptions" :key="type.value" :value="type.value">{{ type.label }}</option>
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
                <th style="width:80px;">Imagen</th>
                <th>Nombre</th>
                <th>Locación</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Review</th>
                <th class="text-end" style="width:120px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="8" class="text-center p-4">
                  <span class="spinner-border spinner-border-sm text-primary"></span>
                  <span class="ms-2">Cargando…</span>
                </td>
              </tr>
              <tr v-else-if="paginated.length === 0">
                <td colspan="8" class="text-center p-4 text-muted">
                  <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                  No hay items para mostrar
                </td>
              </tr>
              <tr v-else v-for="item in paginated" :key="item.id">
                <td>
                  <img :src="buildImg(item.image_url)" alt="" style="width:64px;height:40px;object-fit:cover;border-radius:6px;background:var(--light-gray);">
                </td>
                <td>
                  <strong>{{ item.name || '-' }}</strong>
                </td>
                <td>
                  <div>{{ item.location?.name || '-' }}</div>
                  <small class="text-muted">{{ item.location?.type || '' }}</small>
                </td>
                <td>
                  <div class="text-muted small" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ item.description || '-' }}
                  </div>
                </td>
                <td>
                  <span class="badge" :class="getTypeBadgeClass(item.type)">{{ getTypeLabel(item.type) }}</span>
                </td>
                <td>
                  <strong class="text-success">Bs. {{ item.price || '0.00' }}</strong>
                </td>
                <td>
                  <span class="badge bg-warning text-dark">
                    <i class="fas fa-star"></i> {{ item.review || '-' }}
                  </span>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(item)" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(item.id)" title="Eliminar">
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
          <p>No hay items disponibles. <button class="btn btn-sm btn-primary" @click="openForm()">Crear el primero</button></p>
        </div>

        <div v-if="totalPages > 1" class="card-footer bg-white border-top">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <small class="text-muted">
              Mostrando {{ filtered.length ? (pagination.offset + 1) : 0 }}–{{ Math.min(pagination.offset + pagination.limit, filtered.length) }} de {{ filtered.length }} items
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

    <!-- Modal nueva/editar item -->
    <div class="modal fade show d-block" v-if="showForm" style="background:rgba(0,0,0,0.18);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ editing ? 'Editar' : 'Nueva' }} item de locación</h4>
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
                <select class="form-select" v-model="form.location_id">
                  <option value="">Selecciona una locación</option>
                  <option v-for="opt in locationOptions" :key="opt.id" :value="opt.id">{{ opt.name }} ({{ opt.type }})</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                <select class="form-select" v-model="form.type">
                  <option value="">Selecciona un tipo</option>
                  <option v-for="type in itemTypeOptions" :key="type.value" :value="type.value">{{ type.label }}</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input class="form-control" v-model="form.name" placeholder="Ej: Ceviche Andino">
              </div>
              <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" rows="3" v-model="form.description" placeholder="Descripción del item..."></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Precio (Bs.)</label>
                <input type="number" step="0.01" class="form-control" v-model="form.price" placeholder="0.00">
              </div>
              <div class="col-md-6">
                <label class="form-label">Review</label>
                <input type="number" step="0.1" min="0" max="5" class="form-control" v-model="form.review" placeholder="4.5">
              </div>
              <div class="col-12">
                <label class="form-label">Imagen</label>
                <input type="file" accept="image/*" class="form-control" @change="onFileChange">
                <input class="form-control mt-2" placeholder="o pega URL de imagen" v-model="form.image_url" />
              </div>
              <div v-if="form.preview" class="col-12 text-center mt-2">
                <img :src="form.preview" alt="preview" style="max-width: 300px; border-radius: 10px;box-shadow:0 2px 8px rgba(0,0,0,0.11);">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeForm" :disabled="formLoading">Cancelar</button>
            <button class="btn btn-primary" @click="saveItem" :disabled="formLoading">
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
import { ref, reactive, computed, onMounted, watch, getCurrentInstance } from 'vue'
import adminLocationsService from '@/services/admin/locations.admin.service'
import { usePlanLimits } from '@/composables/usePlanLimits'
import { useAuthStore } from '@/store/auth'

const planLimitsComposable = usePlanLimits()
const authStore = useAuthStore()

const items = ref([])
const loading = ref(false)
const search = ref('')
const filterLocationId = ref('')
const filterType = ref('')
const locationOptions = ref([])
const itemTypeOptions = [
  { value: 'dish', label: 'Plato' },
  { value: 'service', label: 'Servicio' },
  { value: 'offer', label: 'Oferta' },
  { value: 'room', label: 'Habitación' },
]
const pagination = reactive({ limit: 20, offset: 0 })
const showForm = ref(false)
const editing = ref(false)
const formLoading = ref(false)
const form = reactive({ 
  id: 0, 
  location_id: '', 
  name: '', 
  description: '', 
  type: '', 
  price: '', 
  review: '', 
  imageFile: null, 
  image_url: '', 
  preview: '' 
})

// Plan limits
const currentLocationItemsCount = ref(0)
const selectedLocationId = ref(null)

// Computed para límites
const isSuperAdmin = computed(() => planLimitsComposable.isSuperAdmin.value)
const planLimits = computed(() => planLimitsComposable.planLimits.value)
const canCreateNewItem = computed(() => {
  if (isSuperAdmin.value) return true
  // Si está cargando el plan, permitir crear (se validará después)
  if (planLimitsComposable.loadingPlan.value || planLimits.value.max_location_items === null) return true
  if (!selectedLocationId.value) return true // Permitir abrir el formulario
  return planLimitsComposable.canCreateLocationItem(currentLocationItemsCount.value)
})
const itemsLimitMessage = computed(() => {
  if (isSuperAdmin.value) return ''
  if (!selectedLocationId.value) return `Límite: ${planLimits.value.max_location_items} items por locación`
  return planLimitsComposable.getLimitMessage('items', currentLocationItemsCount.value)
})

const app = getCurrentInstance()
function buildImg(url) {
  if (!url) return ''
  const helper = app?.appContext?.config?.globalProperties?.$buildImg
  const built = helper ? helper(url) : (url.startsWith('http') || url.startsWith('/') ? url : '/' + url.replace(/^\//, ''))
  return built
}

const filtered = computed(() => {
  return items.value.filter(item => {
    let pass = true
    if (search.value) {
      const s = search.value.toLowerCase()
      pass = (item.name && item.name.toLowerCase().includes(s)) || 
             (item.description && item.description.toLowerCase().includes(s)) ||
             (item.location?.name && item.location.name.toLowerCase().includes(s))
    }
    if (filterLocationId.value) pass = pass && String(item.location_id) === String(filterLocationId.value)
    if (filterType.value) pass = pass && item.type === filterType.value
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

async function reload() {
  loading.value = true
  try {
    const currentUser = authStore.currentUser
    const userId = currentUser?.id
    
    // Si es super_admin (role_id = 1), listar todos los items
    // Si no, listar solo los del usuario actual
    let resItems
    if (isSuperAdmin.value) {
      resItems = await adminLocationsService.listItems()
    } else {
      // Usar endpoint específico para obtener items del usuario
      if (!userId) {
        console.error('No se pudo obtener el ID del usuario')
        items.value = []
        loading.value = false
        return
      }
      resItems = await adminLocationsService.listItemsByUser(userId)
    }
    
    console.log('Respuesta de listItems:', resItems)
    
    // Manejar diferentes estructuras de respuesta
    if (resItems && resItems.success === true && Array.isArray(resItems.data)) {
      items.value = resItems.data
    } else if (Array.isArray(resItems)) {
      items.value = resItems
    } else if (resItems && Array.isArray(resItems.data)) {
      items.value = resItems.data
    } else {
      console.warn('Estructura de respuesta inesperada:', resItems)
      items.value = []
    }
    
    console.log('Items cargados:', items.value.length)
    
    // Actualizar conteo si hay una locación seleccionada
    if (selectedLocationId.value) {
      updateLocationItemsCount(selectedLocationId.value)
    }
    
    if (!locationOptions.value.length) {
      // Listar locaciones según el rol del usuario
      let resLoc
      if (isSuperAdmin.value) {
        resLoc = await adminLocationsService.list({ limit: 9999 })
      } else {
        if (userId) {
          resLoc = await adminLocationsService.listByUser(userId, { limit: 9999 })
        } else {
          resLoc = { data: [] }
        }
      }
      
      if (resLoc && resLoc.success === true && Array.isArray(resLoc.data)) {
        locationOptions.value = resLoc.data.map(l => ({ id: l.id, name: l.name, type: l.type }))
      } else if (Array.isArray(resLoc)) {
        locationOptions.value = resLoc.map(l => ({ id: l.id, name: l.name, type: l.type }))
      } else if (resLoc && Array.isArray(resLoc.data)) {
        locationOptions.value = resLoc.data.map(l => ({ id: l.id, name: l.name, type: l.type }))
      }
    }
  } catch (e) {
    console.error('Error cargando items:', e)
    console.error('Detalles del error:', e?.response?.data || e?.message || e)
    items.value = []
  } finally {
    loading.value = false
  }
}

function getTypeLabel(type) {
  const map = {
    dish: 'Plato',
    service: 'Servicio',
    offer: 'Oferta',
    room: 'Habitación'
  }
  return map[type] || type
}

function getTypeBadgeClass(type) {
  const map = {
    dish: 'bg-danger text-white',
    service: 'bg-info text-white',
    offer: 'bg-warning text-dark',
    room: 'bg-primary text-white'
  }
  return map[type] || 'bg-light text-dark'
}

onMounted(async () => {
  // Cargar el plan del usuario si solo tiene plan_id
  if (!isSuperAdmin.value && authStore.currentUser?.plan_id && !planLimitsComposable.userPlan.value) {
    await planLimitsComposable.loadUserPlan()
  }
  await reload()
})

function updateLocationItemsCount(locationId) {
  if (!locationId) {
    currentLocationItemsCount.value = 0
    return
  }
  // Contar items de la locación seleccionada
  const count = items.value.filter(item => String(item.location_id) === String(locationId)).length
  currentLocationItemsCount.value = count
  selectedLocationId.value = locationId
}

function openForm(item = null) {
  if (item) {
    editing.value = true
    form.id = item.id
    form.location_id = item.location_id ? String(item.location_id) : ''
    form.name = item.name || ''
    form.description = item.description || ''
    form.type = item.type || ''
    form.price = item.price || ''
    form.review = item.review || ''
    form.image_url = item.image_url || ''
    form.imageFile = null
    form.preview = buildImg(item.image_url)
    updateLocationItemsCount(item.location_id)
  } else {
    editing.value = false
    form.id = 0
    form.location_id = ''
    form.name = ''
    form.description = ''
    form.type = ''
    form.price = ''
    form.review = ''
    form.image_url = ''
    form.imageFile = null
    form.preview = ''
    selectedLocationId.value = null
    currentLocationItemsCount.value = 0
  }
  showForm.value = true
}
function closeForm() { 
  if (!formLoading.value) {
    showForm.value = false 
  }
}

function onFileChange(e) {
  const f = e.target.files?.[0]
  if (f) {
    form.imageFile = f
    form.image_url = ''
    form.preview = URL.createObjectURL(f)
  } else {
    form.imageFile = null
    form.preview = form.image_url ? buildImg(form.image_url) : ''
  }
}

// Actualizar preview si se pega/cambia URL manualmente
watch(
  () => form.image_url,
  val => {
    if (!form.imageFile && val) form.preview = buildImg(val)
  }
)

// Actualizar conteo cuando cambia la locación seleccionada
watch(
  () => form.location_id,
  val => {
    updateLocationItemsCount(val)
  }
)

async function saveItem() {
  if (!form.location_id) {
    alert('Selecciona la locación')
    return
  }
  if (!form.name || !form.name.trim()) {
    alert('El nombre es requerido')
    return
  }
  if (!form.type) {
    alert('El tipo es requerido')
    return
  }
  
  // Validar límite solo al crear un nuevo item
  if (!editing.value && !isSuperAdmin.value) {
    updateLocationItemsCount(form.location_id)
    if (!planLimitsComposable.canCreateLocationItem(currentLocationItemsCount.value)) {
      const message = planLimitsComposable.getLimitMessage('items', currentLocationItemsCount.value)
      alert(message || 'Has alcanzado el límite de items permitidos para esta locación en tu plan.')
      return
    }
  }
  
  formLoading.value = true
  try {
    const fd = new FormData()
    fd.append('location_id', String(form.location_id))
    fd.append('name', form.name.trim())
    if (form.description) fd.append('description', form.description.trim())
    if (form.type) fd.append('type', form.type)
    if (form.price) fd.append('price', String(form.price))
    if (form.review) fd.append('review', String(form.review))
    if (form.imageFile) {
      fd.append('image', form.imageFile)
    } else if (form.image_url) {
      fd.append('image_url', form.image_url.trim())
    }
    
    console.log('Guardando item:', {
      editing: editing.value,
      id: form.id,
      location_id: form.location_id,
      name: form.name,
      type: form.type
    })
    
    if (editing.value && form.id) {
      console.log('Actualizando item:', form.id)
      const result = await adminLocationsService.updateItem(form.id, fd)
      console.log('Item actualizado:', result)
    } else {
      console.log('Creando nuevo item')
      const result = await adminLocationsService.createItem(fd)
      console.log('Item creado:', result)
    }
    
    formLoading.value = false
    showForm.value = false
    await reload()
  } catch (e) {
    console.error('Error guardando item:', e)
    console.error('Detalles:', e?.response?.data || e?.message || e)
    alert('No se pudo guardar el item: ' + (e?.response?.data?.message || e?.message || 'Error'))
    formLoading.value = false
  }
}

async function remove(id) {
  if (!confirm('¿Eliminar este item?')) return
  try {
    console.log('Eliminando item:', id)
    await adminLocationsService.deleteItem(id)
    console.log('Item eliminado exitosamente')
    await reload()
  } catch (e) {
    console.error('Error eliminando item:', e)
    console.error('Detalles:', e?.response?.data || e?.message || e)
    alert('No se pudo eliminar el item: ' + (e?.response?.data?.message || e?.message || 'Error'))
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>
