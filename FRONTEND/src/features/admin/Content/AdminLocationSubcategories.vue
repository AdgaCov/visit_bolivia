<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1">
          <i class="fas fa-sitemap text-primary me-2"></i>
          Locaciones & Subcategorías
        </h1>
        <p class="text-muted mb-0">Gestiona qué subcategorías están asociadas a cada locación</p>
      </div>
      <button class="btn btn-primary" @click="openForm">
        <i class="fas fa-plus me-2"></i>
        Nueva relación
      </button>
    </div>

    <div v-if="successMessage" class="alert alert-success d-flex align-items-center gap-2">
      <i class="fas fa-check-circle"></i>
      <span>{{ successMessage }}</span>
    </div>
    <div v-if="errorMessage" class="alert alert-danger d-flex align-items-center gap-2">
      <i class="fas fa-exclamation-triangle"></i>
      <span>{{ errorMessage }}</span>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <input
              class="form-control"
              placeholder="Buscar por locación o subcategoría..."
              v-model="search"
            />
          </div>
          <div class="col-md-4">
            <select class="form-select" v-model="filterLocation">
              <option value="">Todas las locaciones</option>
              <option
                v-for="loc in locationOptions"
                :key="loc.id"
                :value="String(loc.id)"
              >
                {{ loc.name }} <span v-if="loc.type">({{ loc.type }})</span>
              </option>
            </select>
          </div>
          <div class="col-md-4">
            <select class="form-select" v-model="filterSubcategory">
              <option value="">Todas las subcategorías</option>
              <option
                v-for="sub in subcategoryOptions"
                :key="sub.id"
                :value="String(sub.id)"
              >
                {{ sub.name }}
              </option>
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
                <th>Subcategoría</th>
                <th class="text-center" style="width: 140px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="3" class="text-center p-4">
                  <span class="spinner-border spinner-border-sm text-primary"></span>
                  <span class="ms-2">Cargando relaciones…</span>
                </td>
              </tr>
              <tr v-else-if="paginated.length === 0">
                <td colspan="3" class="text-center p-4 text-muted">
                  <i class="fas fa-folder-open fa-2x d-block mb-2"></i>
                  No hay resultados con los filtros actuales
                </td>
              </tr>
              <tr
                v-else
                v-for="link in paginated"
                :key="link.id || `${link.location_id}-${link.subcategory_id}`"
              >
                <td>
                  <strong>{{ link.location_name || 'Locación sin nombre' }}</strong>
                  <div class="text-muted small">ID: {{ link.location_id }}</div>
                </td>
                <td>
                  <strong>{{ link.subcategory_name || 'Subcategoría sin nombre' }}</strong>
                  <div class="text-muted small">ID: {{ link.subcategory_id }}</div>
                </td>
                <td class="text-center">
                  <button
                    class="btn btn-sm btn-outline-danger"
                    :disabled="!link.id && removingRelationId === `${link.location_id}-${link.subcategory_id}`"
                    @click="removeRelation(link)"
                  >
                    <span v-if="removingRelationId === (link.id ? String(link.id) : `${link.location_id}-${link.subcategory_id}`)" class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="fas fa-trash"></i>
                    <span class="ms-1">Eliminar</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="totalPages > 1" class="card-footer bg-white border-top mt-3">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <small class="text-muted">
              Mostrando {{ filtered.length ? (pagination.offset + 1) : 0 }}–{{ Math.min(pagination.offset + pagination.limit, filtered.length) }}
              de {{ filtered.length }} relaciones
            </small>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item" :class="{ disabled: currentPage <= 1 }" @click="setPage(1)">
                  <span class="page-link">Primera</span>
                </li>
                <li class="page-item" :class="{ disabled: currentPage <= 1 }" @click="setPage(currentPage - 1)">
                  <span class="page-link">«</span>
                </li>
                <li
                  class="page-item"
                  v-for="p in pages"
                  :key="p"
                  :class="{ active: currentPage === p }"
                  @click="setPage(p)"
                >
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

    <div
      class="modal fade show d-block"
      v-if="showForm"
      style="background-color: rgba(0,0,0,0.18);"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Nueva relación</h4>
            <button type="button" class="btn-close" @click="closeForm" :disabled="formLoading"></button>
          </div>
          <div v-if="formLoading" class="progress" style="height: 4px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 100%;"></div>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Locación <span class="text-danger">*</span></label>
              <select class="form-select" v-model="form.location_id">
                <option value="">Selecciona una locación</option>
                <option v-for="loc in locationOptions" :key="loc.id" :value="String(loc.id)">
                  {{ loc.name }} <span v-if="loc.type">({{ loc.type }})</span>
                </option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Subcategoría <span class="text-danger">*</span></label>
              <select class="form-select" v-model="form.subcategory_id">
                <option value="">Selecciona una subcategoría</option>
                <option v-for="sub in subcategoryOptions" :key="sub.id" :value="String(sub.id)">
                  {{ sub.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeForm" :disabled="formLoading">Cancelar</button>
            <button class="btn btn-primary" @click="saveRelation" :disabled="formLoading">
              <span v-if="formLoading" class="spinner-border spinner-border-sm me-2"></span>
              <span v-if="formLoading">Guardando…</span>
              <span v-else>Guardar relación</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import adminLocationSubcategoriesService, { LocationSubcategoryLink } from '@/services/admin/location-subcategories.admin.service'
import adminLocationsService from '@/services/admin/locations.admin.service'
import adminSubcategoriesService from '@/services/admin/subcategories.admin.service'

interface SelectOption {
  id: number
  name: string
  type?: string
}

const links = ref<LocationSubcategoryLink[]>([])
const loading = ref(false)
const search = ref('')
const filterLocation = ref('')
const filterSubcategory = ref('')
const pagination = reactive({ limit: 10, offset: 0 })
const locationOptions = ref<SelectOption[]>([])
const subcategoryOptions = ref<SelectOption[]>([])
const showForm = ref(false)
const formLoading = ref(false)
const form = reactive({ location_id: '', subcategory_id: '' })
const successMessage = ref('')
const errorMessage = ref('')
const removingRelationId = ref('')

const filtered = computed(() => {
  const searchTerm = search.value.toLowerCase().trim()
  return links.value.filter(link => {
    let match = true
    if (searchTerm) {
      match = Boolean(
        (link.location_name && link.location_name.toLowerCase().includes(searchTerm)) ||
        (link.subcategory_name && link.subcategory_name.toLowerCase().includes(searchTerm))
      )
    }
    if (match && filterLocation.value) {
      match = String(link.location_id) === String(filterLocation.value)
    }
    if (match && filterSubcategory.value) {
      match = String(link.subcategory_id) === String(filterSubcategory.value)
    }
    return match
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / pagination.limit)))
const currentPage = computed(() => Math.floor(pagination.offset / pagination.limit) + 1)
const pages = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  const start = Math.max(1, current - 2)
  const end = Math.min(total, current + 2)
  const all = []
  for (let p = start; p <= end; p++) all.push(p)
  return all
})
const paginated = computed(() => filtered.value.slice(pagination.offset, pagination.offset + pagination.limit))

function setPage(page: number) {
  const safe = Math.min(Math.max(1, page), totalPages.value)
  pagination.offset = (safe - 1) * pagination.limit
}

function openForm() {
  form.location_id = ''
  form.subcategory_id = ''
  showForm.value = true
}

function closeForm() {
  if (!formLoading.value) {
    showForm.value = false
  }
}

async function loadOptions() {
  try {
    const [locationsRes, subcategoriesRes] = await Promise.all([
      adminLocationsService.list({ limit: 9999 }),
      adminSubcategoriesService.list()
    ])

    if (locationsRes && Array.isArray(locationsRes.data)) {
      locationOptions.value = locationsRes.data.map(loc => ({
        id: loc.id,
        name: loc.name,
        type: (loc as any).type || (loc.department?.name ?? '')
      }))
    }

    if (subcategoriesRes && Array.isArray(subcategoriesRes.data)) {
      subcategoryOptions.value = subcategoriesRes.data.map(sub => ({
        id: sub.id,
        name: sub.name
      }))
    }
  } catch (error) {
    console.error('Error cargando opciones', error)
  }
}

async function reload() {
  loading.value = true
  errorMessage.value = ''
  try {
    const res = await adminLocationSubcategoriesService.list()
    if (res && res.success && Array.isArray(res.data)) {
      links.value = res.data
    } else if (res && Array.isArray((res as any).data)) {
      links.value = (res as any).data
    } else if (Array.isArray((res as any))) {
      links.value = res as unknown as LocationSubcategoryLink[]
    } else {
      links.value = []
    }
  } catch (error: any) {
    errorMessage.value = error?.response?.data?.message || error?.message || 'No se pudo cargar la lista'
    links.value = []
    console.error('Error al cargar locaciones & subcategorías', error)
  } finally {
    loading.value = false
  }
}

async function saveRelation() {
  if (!form.location_id) {
    errorMessage.value = 'Debes seleccionar una locación'
    return
  }
  if (!form.subcategory_id) {
    errorMessage.value = 'Debes seleccionar una subcategoría'
    return
  }

  formLoading.value = true
  errorMessage.value = ''
  try {
    await adminLocationSubcategoriesService.create({
      location_id: Number(form.location_id),
      subcategory_id: Number(form.subcategory_id)
    })
    successMessage.value = 'Relación guardada correctamente'
    setTimeout(() => {
      successMessage.value = ''
    }, 4000)
    showForm.value = false
    await reload()
  } catch (error: any) {
    errorMessage.value = error?.response?.data?.message || error?.message || 'No se pudo guardar la relación'
  } finally {
    formLoading.value = false
  }
}

async function removeRelation(link: LocationSubcategoryLink) {
  const identifier = `${link.location_id}-${link.subcategory_id}`
  if (!confirm('¿Eliminar esta relación?')) return
  removingRelationId.value = identifier
  try {
    await adminLocationSubcategoriesService.remove(link.location_id, link.subcategory_id)
    await reload()
  } catch (error: any) {
    alert('No se pudo eliminar la relación: ' + (error?.response?.data?.message || error?.message))
  } finally {
    removingRelationId.value = ''
  }
}

onMounted(async () => {
  await Promise.all([loadOptions(), reload()])
})
</script>

<style scoped>
.card {
  border-radius: 12px;
}
.modal .modal-content {
  border-radius: 12px;
}
</style>


