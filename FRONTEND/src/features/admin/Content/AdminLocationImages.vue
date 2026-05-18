<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-image text-primary me-2"></i>Imágenes de Locaciones</h1>
        <p class="text-muted mb-0">Gestión de todas las imágenes por locación, tipo y galería</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div v-if="!isSuperAdmin && planLimits && planLimits.max_location_images !== null" class="text-muted small">
          <i class="fas fa-info-circle"></i>
          {{ imagesLimitMessage }}
        </div>
        <div v-else-if="!isSuperAdmin && planLimitsComposable.loadingPlan.value" class="text-muted small">
          <i class="fas fa-spinner fa-spin"></i>
          Cargando límites del plan...
        </div>
        <button 
          class="btn btn-primary" 
          @click="openForm()"
          :disabled="!canCreateNewImage"
          :title="!canCreateNewImage ? 'Has alcanzado el límite de imágenes permitidas en tu plan' : ''"
        >
          <i class="fas fa-plus me-2"></i>Nueva imagen
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3 mb-2">
          <div class="col-md-4"><input class="form-control" placeholder="Buscar por alt o locación..." v-model="search"></div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterLocationId">
              <option value="">Todas las locaciones</option>
              <option v-for="loc in locationOptions" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="filterType">
              <option value="">Todos los tipos</option>
              <option v-for="type in typeOptions" :key="type.value" :value="type.value">{{ type.label }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead><tr><th>Imagen</th><th>Alt Text</th><th>Locación</th><th>Tipo</th><th>Principal</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
              <tr v-for="img in paginated" :key="img.id">
                <td><img :src="buildImg(img.image_url)" alt="" style="width:64px;height:40px;object-fit:cover;border-radius:6px;background:var(--light-gray);"></td>
                <td>{{ img.alt_text || '-' }}</td>
                <td>
                  <strong>{{ img.location?.name || '-' }}</strong>
                </td>
                <td><span class="badge bg-light text-dark">{{ img.location?.type }}</span></td>
                <td>
                  <span v-if="img.is_main" class="badge bg-primary">Principal</span>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="setMain(img)" title="Marcar principal"><i class="fas fa-star"></i></button>
                    <button class="btn btn-sm btn-outline-secondary" @click="editAlt(img)" title="Editar alt"><i class="fas fa-font"></i></button>
                    <button class="btn btn-sm btn-outline-secondary" @click="openForm(img)" title="Editar imagen"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(img.id)" title="Eliminar"><i class="fas fa-trash"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="loading" class="text-center p-4">
          <span class="spinner-border spinner-border-sm text-primary"></span>
          <span class="ms-2">Cargando…</span>
        </div>
        <div v-if="!loading && filtered.length === 0" class="text-center p-4 text-muted">Sin resultados</div>

        <div v-if="totalPages > 1" class="card-footer bg-white border-top">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <small class="text-muted">
              Mostrando {{ filtered.length ? (pagination.offset + 1) : 0 }}–{{ Math.min(pagination.offset + pagination.limit, filtered.length) }} de {{ filtered.length }} imágenes
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

    <!-- Modal edición alt_text  -->
    <div class="modal fade show d-block" v-if="showEditAlt" style="background:rgba(0,0,0,0.14);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar texto alternativo</h5>
            <button class="btn-close" @click="closeEditAlt" :disabled="savingAlt"></button>
          </div>
          <!-- Progress Bar -->
          <div v-if="savingAlt" class="progress" style="height: 4px; border-radius: 0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                 role="progressbar" 
                 style="width: 100%;">
            </div>
          </div>
          <div class="modal-body d-flex gap-4 align-items-center flex-wrap">
            <div class="flex-shrink-0">
              <img :src="buildImg(editAltImage?.image_url)" alt="" style="width:220px;max-width:100%;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.13);" class="mb-3">
            </div>
            <div class="flex-grow-1">
              <div class="mb-2">
                <strong class="text-secondary">Locación:</strong>
                <span>{{ editAltImage?.location?.name || '-' }}</span>
              </div>
              <div class="mb-2">
                <strong class="text-secondary">Texto actual:</strong>
                <div class="text-muted small">{{ editAltImage?.alt_text || '—' }}</div>
              </div>
              <label class="form-label">Nuevo texto alternativo</label>
              <input v-model="editAltText" class="form-control" placeholder="Nuevo alt">
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeEditAlt" :disabled="savingAlt">Cancelar</button>
            <button class="btn btn-primary" @click="saveAltText" :disabled="savingAlt">
              <span v-if="savingAlt" class="spinner-border spinner-border-sm me-2"></span>
              <span v-if="savingAlt">Guardando...</span>
              <span v-else>Guardar</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal nueva/editar imagen -->
    <div class="modal fade show d-block" v-if="showForm" style="background:rgba(0,0,0,0.18);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ editing ? 'Editar' : 'Nueva' }} imagen de locación</h4>
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
            <div class="mb-3">
              <label class="form-label">Locación</label>
              <select class="form-select" v-model="form.location_id">
                <option value="">Selecciona una locación</option>
                <option v-for="opt in locationOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Archivo de imagen</label>
              <input type="file" accept="image/*" class="form-control" @change="onFileChange">
              <input class="form-control mt-2" placeholder="o pega URL de imagen" v-model="form.image_url" />
            </div>
            <div class="mb-3">
              <label class="form-label">Texto alternativo</label>
              <input class="form-control" v-model="form.alt_text" />
            </div>
            <div class="mb-3 form-check">
              <input class="form-check-input" type="checkbox" id="mainCheck" v-model="form.is_main">
              <label class="form-check-label" for="mainCheck">¿Imagen principal?</label>
            </div>
            <div v-if="form.preview" class="text-center mt-3">
              <img :src="form.preview" alt="preview" style="max-width: 230px; border-radius: 10px;box-shadow:0 2px 8px rgba(0,0,0,0.11);">
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeForm" :disabled="formLoading">Cancelar</button>
            <button class="btn btn-primary" @click="saveImage" :disabled="formLoading">
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

const images = ref([])
const loading = ref(false)
const search = ref('')
const filterLocationId = ref('')
const filterType = ref('')
const locationOptions = ref([])
const typeOptions = [
  { value: 'Internacional', label: 'Internacional' },
  { value: 'Local', label: 'Local' },
  { value: 'Nacional', label: 'Nacional' },
  { value: 'Regional', label: 'Regional' },
  { value: 'destination', label: 'Destino' },
]
const pagination = reactive({ limit: 20, offset: 0 })
const showForm = ref(false)
const editing = ref(false)
const formLoading = ref(false)
const form = reactive({ id: 0, location_id: '', alt_text: '', imageFile: null, image_url: '', is_main: false, preview: '' })

const showEditAlt = ref(false)
const editAltText = ref('')
const editAltImage = ref(null)
const savingAlt = ref(false)

// Plan limits
const currentLocationImagesCount = ref(0)
const selectedLocationId = ref(null)

// Computed para límites
const isSuperAdmin = computed(() => planLimitsComposable.isSuperAdmin.value)
const planLimits = computed(() => planLimitsComposable.planLimits.value)
const canCreateNewImage = computed(() => {
  if (isSuperAdmin.value) return true
  // Si está cargando el plan, permitir crear (se validará después)
  if (planLimitsComposable.loadingPlan.value || planLimits.value.max_location_images === null) return true
  if (!selectedLocationId.value) return true // Permitir abrir el formulario
  return planLimitsComposable.canCreateLocationImage(currentLocationImagesCount.value)
})
const imagesLimitMessage = computed(() => {
  if (isSuperAdmin.value) return ''
  if (!selectedLocationId.value) return `Límite: ${planLimits.value.max_location_images} imágenes por locación`
  return planLimitsComposable.getLimitMessage('images', currentLocationImagesCount.value)
})

const app = getCurrentInstance()
function buildImg(url) {
  if (!url) return ''
  const helper = app?.appContext?.config?.globalProperties?.$buildImg
  const built = helper ? helper(url) : (url.startsWith('http') || url.startsWith('/') ? url : '/' + url.replace(/^\//, ''))
  return built
}

const filtered = computed(() => {
  return images.value.filter(img => {
    let pass = true
    if (search.value) {
      const s = search.value.toLowerCase()
      pass = (img.alt_text && img.alt_text.toLowerCase().includes(s)) || (img.location?.name && img.location.name.toLowerCase().includes(s))
    }
    if (filterLocationId.value) pass = pass && String(img.location_id) === String(filterLocationId.value)
    if (filterType.value) pass = pass && img.location?.type === filterType.value
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
    
    // Si es super_admin (role_id = 1), listar todas las imágenes
    // Si no, listar solo las del usuario actual
    let resImgs
    if (isSuperAdmin.value) {
      resImgs = await adminLocationsService.listImages()
    } else {
      // Usar endpoint específico para obtener imágenes del usuario
      if (!userId) {
        console.error('No se pudo obtener el ID del usuario')
        images.value = []
        loading.value = false
        return
      }
      resImgs = await adminLocationsService.listImagesByUser(userId)
    }
    
    // Cache-buster para reflejar cambios recientes en imágenes
    const ts = Date.now()
    images.value = (resImgs?.data || []).map(i => ({ ...i, image_url: i.image_url ? `${i.image_url}` : i.image_url, _ts: ts }))
    
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
      locationOptions.value = (resLoc?.data || []).map(l => ({ id: l.id, name: l.name, type: l.type }))
    }
    
    // Actualizar conteo si hay una locación seleccionada
    if (selectedLocationId.value) {
      updateLocationImagesCount(selectedLocationId.value)
    }
  } catch (e) {
    console.error('Error cargando imágenes:', e)
    images.value = []
  } finally {
    loading.value = false
  }
}
onMounted(async () => {
  // Cargar el plan del usuario si solo tiene plan_id
  if (!isSuperAdmin.value && authStore.currentUser?.plan_id && !planLimitsComposable.userPlan.value) {
    await planLimitsComposable.loadUserPlan()
  }
  await reload()
})

function updateLocationImagesCount(locationId) {
  if (!locationId) {
    currentLocationImagesCount.value = 0
    return
  }
  // Contar imágenes de la locación seleccionada
  const count = images.value.filter(img => String(img.location_id) === String(locationId)).length
  currentLocationImagesCount.value = count
  selectedLocationId.value = locationId
}

function openForm(img = null) {
  if (img) {
    // Edición (no implementado en backend, si lo necesitas adaptamos POST/PUT)
    editing.value = true
    form.id = img.id
    form.location_id = img.location_id
    form.alt_text = img.alt_text
    form.is_main = !!img.is_main
    form.image_url = img.image_url || ''
    form.imageFile = null
    form.preview = buildImg(img.image_url)
    updateLocationImagesCount(img.location_id)
  } else {
    editing.value = false
    form.id = 0
    form.location_id = ''
    form.alt_text = ''
    form.is_main = false
    form.image_url = ''
    form.imageFile = null
    form.preview = ''
    selectedLocationId.value = null
    currentLocationImagesCount.value = 0
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
    updateLocationImagesCount(val)
  }
)

async function saveImage() {
  if (!form.location_id) {
    alert('Selecciona la locación')
    return
  }
  if (!form.imageFile && !form.image_url && !editing.value) {
    alert('Debes seleccionar un archivo o ingresar una URL')
    return
  }
  
  // Validar límite solo al crear una nueva imagen
  if (!editing.value && !isSuperAdmin.value) {
    updateLocationImagesCount(form.location_id)
    if (!planLimitsComposable.canCreateLocationImage(currentLocationImagesCount.value)) {
      const message = planLimitsComposable.getLimitMessage('images', currentLocationImagesCount.value)
      alert(message || 'Has alcanzado el límite de imágenes permitidas para esta locación en tu plan.')
      return
    }
  }
  
  formLoading.value = true
  try {
    const fd = new FormData()
    fd.append('location_id', String(form.location_id))
    fd.append('alt_text', form.alt_text || '')
    fd.append('is_main', form.is_main ? '1' : '0')
    if (form.imageFile) {
      fd.append('image', form.imageFile)
    } else if (form.image_url) {
      fd.append('image_url', form.image_url)
    }
    if (editing.value && form.id) {
      // Actualizar imagen existente
      await adminLocationsService.updateImage(form.id, fd)
    } else {
      // Crear nueva imagen
      await adminLocationsService.createImage(fd)
    }
    // Cerrar modal antes de recargar
    formLoading.value = false
    showForm.value = false
    reload()
  } catch (e) {
    alert('No se pudo guardar la imagen: ' + (e?.response?.data?.message || e?.message || 'Error'))
  } finally {
    formLoading.value = false
  }
}

async function setMain(img) {
  if (img.is_main) return
  await adminLocationsService.setMainImage(img.id)
  reload()
}
async function remove(id) {
  if (!confirm('¿Eliminar imagen?')) return
  await adminLocationsService.deleteImage(id)
  reload()
}
function editAlt(img) {
  editAltImage.value = img
  editAltText.value = img.alt_text || ''
  showEditAlt.value = true
}
function closeEditAlt() {
  if (!savingAlt.value) {
    showEditAlt.value = false
  }
}
async function saveAltText() {
  if (!editAltImage.value) return
  savingAlt.value = true
  try {
    await adminLocationsService.updateAlt(editAltImage.value.id, editAltText.value)
    // Cerrar modal antes de recargar
    savingAlt.value = false
    showEditAlt.value = false
    reload()
  } catch (e) {
    alert('No se pudo actualizar el texto alternativo: ' + (e?.response?.data?.message || e?.message || 'Error'))
    savingAlt.value = false
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>
