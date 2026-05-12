<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1">
          <i class="fas fa-project-diagram text-primary me-2"></i>
          Relaciones entre Artículos
        </h1>
        <p class="text-muted mb-0">Configura qué artículos actúan como padres y cuáles se muestran como hijos.</p>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Buscar artículo padre</label>
            <input class="form-control" placeholder="Filtra por título..." v-model="parentSearch" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Selecciona el artículo padre</label>
            <select class="form-select" v-model="selectedParentId">
              <option value="">-- Selecciona un artículo --</option>
              <option
                v-for="article in filteredParentOptions"
                :key="article.id"
                :value="String(article.id)"
              >
                {{ article.title || `Artículo #${article.id}` }}
              </option>
            </select>
          </div>
        </div>
        <small class="text-muted d-block mt-2">
          Los hijos se cargan automáticamente desde <code>/api/articles/by-parent/{id}</code>.
        </small>
      </div>
    </div>

    <div v-if="selectedParent" class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h5 class="mb-1">{{ selectedParent.title || `Artículo #${selectedParent.id}` }}</h5>
            <p class="text-muted mb-0">ID: {{ selectedParent.id }} | Sección: {{ selectedParent.page_section || 'N/A' }}</p>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-8">
            <label class="form-label">Agregar artículo hijo</label>
            <select class="form-select" v-model="selectedChildId">
              <option value="">-- Selecciona un artículo --</option>
              <option
                v-for="article in availableChildren"
                :key="article.id"
                :value="String(article.id)"
              >
                {{ article.title || `Artículo #${article.id}` }}
              </option>
            </select>
            <small class="text-muted">Solo se muestran artículos que no son el padre ni ya están asociados como hijos.</small>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100" :disabled="!selectedChildId || savingChild" @click="addChild">
              <span v-if="savingChild" class="spinner-border spinner-border-sm me-2"></span>
              {{ savingChild ? 'Guardando...' : 'Agregar hijo' }}
            </button>
          </div>
        </div>

        <div v-if="childSuccessMessage" class="alert alert-success py-2">
          {{ childSuccessMessage }}
        </div>
        <div v-if="childErrorMessage" class="alert alert-danger py-2">
          {{ childErrorMessage }}
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Título</th>
                <th>Sección</th>
                <th>Fecha</th>
                <th class="text-end" style="width: 140px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loadingChildren">
                <td colspan="4" class="text-center p-4">
                  <span class="spinner-border spinner-border-sm text-primary"></span>
                  <span class="ms-2">Cargando hijos…</span>
                </td>
              </tr>
              <tr v-else-if="childArticles.length === 0">
                <td colspan="4" class="text-center p-4 text-muted">
                  <i class="fas fa-layer-group fa-2x d-block mb-2"></i>
                  Este artículo todavía no tiene hijos asociados.
                </td>
              </tr>
              <tr v-else v-for="child in childArticles" :key="child.id">
                <td>
                  <strong>{{ child.title || `Artículo #${child.id}` }}</strong>
                  <div class="text-muted small">ID: {{ child.id }}</div>
                </td>
                <td>{{ child.page_section || 'N/A' }}</td>
                <td>{{ formatDate(child.created_at) }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-danger" :disabled="removingChildId === child.id" @click="detachChild(child)">
                    <span v-if="removingChildId === child.id" class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="fas fa-unlink me-1"></i>
                    Quitar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import adminArticlesService, { AdminArticle } from '@/services/admin/articles.admin.service'

const articles = ref<AdminArticle[]>([])
const loadingArticles = ref(false)
const selectedParentId = ref('')
const selectedChildId = ref('')
const parentSearch = ref('')

const childArticles = ref<AdminArticle[]>([])
const loadingChildren = ref(false)
const savingChild = ref(false)
const removingChildId = ref<number | null>(null)
const childSuccessMessage = ref('')
const childErrorMessage = ref('')

const filteredParentOptions = computed(() => {
  const search = parentSearch.value.toLowerCase().trim()
  if (!search) return articles.value
  return articles.value.filter(article =>
    (article.title || '').toLowerCase().includes(search)
  )
})

const selectedParent = computed(() =>
  articles.value.find(article => String(article.id) === selectedParentId.value) || null
)

const availableChildren = computed(() => {
  if (!selectedParent.value) return []
  const childIds = new Set(childArticles.value.map(child => child.id))
  return articles.value.filter(article => {
    if (article.id === selectedParent.value!.id) return false
    if (childIds.has(article.id)) return false
    return article.parent_id == null || article.parent_id === 0
  })
})

function formatDate(date?: string | null) {
  if (!date) return '—'
  try {
    return new Date(date).toLocaleDateString()
  } catch {
    return date
  }
}

async function loadArticles() {
  loadingArticles.value = true
  try {
    const res = await adminArticlesService.list({ limit: 9999 })
    if (Array.isArray(res.data)) {
      articles.value = res.data
    } else if (res.success && Array.isArray(res.data)) {
      articles.value = res.data
    } else {
      articles.value = []
    }
  } catch (error) {
    console.error('Error cargando artículos', error)
    articles.value = []
  } finally {
    loadingArticles.value = false
  }
}

async function loadChildren(parentId: string | number) {
  loadingChildren.value = true
  childErrorMessage.value = ''
  try {
    const res = await adminArticlesService.listByParent(parentId)
    if (res && res.success && Array.isArray(res.data)) {
      childArticles.value = res.data
    } else if (Array.isArray((res as any).data)) {
      childArticles.value = (res as any).data
    } else if (Array.isArray(res as any)) {
      childArticles.value = res as unknown as AdminArticle[]
    } else {
      childArticles.value = []
    }
  } catch (error: any) {
    childErrorMessage.value = error?.response?.data?.message || error?.message || 'No se pudieron cargar los artículos hijos'
    childArticles.value = []
  } finally {
    loadingChildren.value = false
  }
}

async function addChild() {
  if (!selectedParent.value || !selectedChildId.value) return
  savingChild.value = true
  childSuccessMessage.value = ''
  childErrorMessage.value = ''
  try {
    await adminArticlesService.update(Number(selectedChildId.value), { parent_id: Number(selectedParent.value.id) })
    childSuccessMessage.value = 'Artículo hijo agregado correctamente.'
    selectedChildId.value = ''
    await loadChildren(selectedParent.value.id)
  } catch (error: any) {
    childErrorMessage.value = error?.response?.data?.message || error?.message || 'No se pudo agregar el artículo hijo'
  } finally {
    savingChild.value = false
    setTimeout(() => (childSuccessMessage.value = ''), 3500)
  }
}

async function detachChild(child: AdminArticle) {
  if (!selectedParent.value) return
  if (!confirm('¿Quitar este artículo hijo?')) return
  removingChildId.value = child.id
  childErrorMessage.value = ''
  try {
    await adminArticlesService.update(child.id, { parent_id: null })
    await loadChildren(selectedParent.value.id)
  } catch (error: any) {
    childErrorMessage.value = error?.response?.data?.message || error?.message || 'No se pudo quitar el artículo hijo'
  } finally {
    removingChildId.value = null
  }
}

watch(selectedParentId, (newVal) => {
  childArticles.value = []
  selectedChildId.value = ''
  childSuccessMessage.value = ''
  childErrorMessage.value = ''
  if (newVal) {
    loadChildren(newVal)
  }
})

onMounted(loadArticles)
</script>

<style scoped>
.card {
  border-radius: 12px;
}
.table thead th {
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: rgba(15, 23, 42, 0.6);
}
</style>


