<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-layer-group text-primary me-2"></i>Subcategorías</h1>
        <p class="text-muted mb-0">Gestión de subcategorías</p>
      </div>
      <button class="btn btn-primary" @click="openForm()"><i class="fas fa-plus me-2"></i>Nueva subcategoría</button>
    </div>
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3 mb-2">
          <div class="col-md-4"><input class="form-control" placeholder="Buscar..." v-model="search"></div>
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
                <th>Categoría</th>
                <th>Descripción</th>
                <th class="text-end" style="width:110px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="subcat in paginated" :key="subcat.id">
                <td><img :src="$buildImg ? $buildImg(subcat.image_url) : subcat.image_url" alt="" style="width:64px;height:40px;object-fit:cover;border-radius:6px;background:var(--light-gray);"></td>
                <td><strong>{{ subcat.name }}</strong></td>
                <td>
                  <span class="badge bg-secondary" v-if="subcat.category">
                    {{ subcat.category.name }}
                  </span>
                  <span class="text-muted" v-else>-</span>
                </td>
                <td><div class="text-muted small" style="max-width:300px;white-space:pre-line;">{{ subcat.description }}</div></td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(subcat)" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(subcat.id)" title="Eliminar"><i class="fas fa-trash"></i></button>
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
              Mostrando {{ filtered.length ? (pagination.offset + 1) : 0 }}–{{ Math.min(pagination.offset + pagination.limit, filtered.length) }} de {{ filtered.length }} subcategorías
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
    <!-- Modal edición/alta -->
    <div v-if="show" class="modal fade show d-block" style="background-color:rgba(0,0,0,0.12);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{ editing ? 'Editar' : 'Nueva' }} subcategoría</h4>
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
            <div class="mb-3 text-center">
              <div class="ratio ratio-16x9 mb-2" style="border-radius: 8px; overflow: hidden; background: var(--bg-secondary); max-width:340px; margin:0 auto;">
                <img v-if="form.cover" :src="form.cover" alt="Imagen subcategoría" style="width:100%;height:100%;object-fit:cover;">
                <div v-else class="d-flex align-items-center justify-content-center text-muted">Sin imagen</div>
              </div>
              <div class="d-grid gap-2">
                <input type="file" accept="image/*" class="form-control" @change="onCover">
                <input class="form-control" placeholder="o pega URL de imagen" v-model="form.cover">
                <button class="btn btn-outline-secondary btn-sm" type="button" @click="form.cover=''">Quitar imagen</button>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input class="form-control" v-model="form.name" />
            </div>
            <div class="mb-3">
              <label class="form-label">Categoría</label>
              <select class="form-select" v-model="form.category_id">
                <option value="">Seleccionar categoría</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea class="form-control" v-model="form.description" rows="4"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="close" :disabled="formLoading">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="save" :disabled="formLoading">
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
import adminSubcategoriesService from '@/services/admin/subcategories.admin.service'
import apiClient from '@/services/api'
export default {
  name: 'AdminSubcategories',
  data() {
    return {
      loading: true,
      subcategories: [],
      categories: [],
      error: '',
      show: false,
      editing: false,
      formLoading: false,
      search: '',
      pagination: {
        limit: 20,
        offset: 0,
      },
      form: { id: 0, name: '', description: '', category_id: '', cover: '', coverFile: null }
    }
  },
  computed: {
    filtered() {
      if (!this.search) return this.subcategories
      const s = this.search.toLowerCase()
      return this.subcategories.filter(subcat =>
        (subcat.name && subcat.name.toLowerCase().includes(s)) ||
        (subcat.description && subcat.description.toLowerCase().includes(s)) ||
        (subcat.category && subcat.category.name && subcat.category.name.toLowerCase().includes(s))
      )
    },
    totalPages() {
      return Math.max(1, Math.ceil(this.filtered.length / this.pagination.limit))
    },
    currentPage() {
      return Math.floor(this.pagination.offset / this.pagination.limit) + 1
    },
    pages() {
      const total = this.totalPages, current = this.currentPage
      const start = Math.max(1, current - 2), end = Math.min(total, current + 2)
      const arr = []
      for (let i = start; i <= end; i++) arr.push(i)
      return arr
    },
    paginated() {
      return this.filtered.slice(this.pagination.offset, this.pagination.offset + this.pagination.limit)
    }
  },
  mounted() {
    this.loadCategories()
    this.reload()
  },
  methods: {
    async loadCategories() {
      try {
        const res = await apiClient.get('/api/categories')
        this.categories = res.data || []
      } catch (e) {
        console.error('Error al cargar categorías:', e)
      }
    },
    async reload() {
      this.loading = true
      try {
        const res = await adminSubcategoriesService.list()
        this.subcategories = res.data || []
      } catch (e) {
        this.error = e?.response?.data?.message || e?.message || 'Error al cargar subcategorías'
        this.subcategories = []
        console.error('Error al cargar subcategorías:', e)
      } finally {
        this.loading = false
      }
    },
    setPage(page) {
      const p = Math.min(Math.max(1, page), this.totalPages)
      this.pagination.offset = (p - 1) * this.pagination.limit
    },
    openForm(s) {
      if (s) {
        this.editing = true
        this.form = { 
          id: s.id, 
          name: s.name, 
          description: s.description, 
          category_id: s.category_id || '',
          cover: this.$buildImg ? this.$buildImg(s.image_url) : s.image_url, 
          coverFile: null 
        }
      } else {
        this.editing = false
        this.form = { id: 0, name: '', description: '', category_id: '', cover: '', coverFile: null }
      }
      this.show = true
    },
    close() { 
      if (!this.formLoading) {
        this.show = false 
      }
    },
    onCover(e) {
      const f = e.target.files?.[0]
      if (f) {
        this.form.coverFile = f
        this.form.cover = URL.createObjectURL(f)
      }
    },
    async save() {
      if (!this.form.name || !this.form.name.trim()) { alert('El nombre es requerido'); return }
      if (!this.form.category_id) { alert('La categoría es requerida'); return }
      this.formLoading = true
      try {
        const fd = new FormData()
        fd.append('name', this.form.name)
        fd.append('description', this.form.description || '')
        fd.append('category_id', this.form.category_id)
        if (this.form.coverFile) {
          fd.append('image', this.form.coverFile)
        }
        // Si es edición (tiene ID), usar update (POST), si no, usar create (POST)
        if (this.editing && this.form.id > 0) {
          await adminSubcategoriesService.update(this.form.id, fd)
        } else {
          await adminSubcategoriesService.create(fd)
        }
        // Cerrar modal antes de recargar
        this.show = false
        await this.reload()
      } catch (e) {
        const action = this.editing ? 'actualizar' : 'crear'
        alert(`No se pudo ${action} la subcategoría: ` + (e?.response?.data?.message || e?.message))
      } finally {
        this.formLoading = false
      }
    },
    async remove(id) {
      if (!confirm('¿Eliminar esta subcategoría?')) return
      try {
        await adminSubcategoriesService.remove(id)
        await this.reload()
      } catch (e) {
        alert('No se pudo eliminar la subcategoría: ' + (e?.response?.data?.message || e?.message))
      }
    }
  }
}
</script>
<style scoped>
.card { border-radius: 12px; }
</style>

