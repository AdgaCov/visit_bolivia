<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-tags text-primary me-2"></i>Categorías</h1>
        <p class="text-muted mb-0">Gestión de categorías y subcategorías</p>
      </div>
      <button class="btn btn-primary" @click="openForm()"><i class="fas fa-plus me-2"></i>Nueva categoría</button>
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
                <th>Descripción</th>
                <th>Subcategorías</th>
                <th class="text-end" style="width:110px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cat in paginated" :key="cat.id">
                <td><img :src="$buildImg ? $buildImg(cat.image_url) : cat.image_url" alt="" style="width:64px;height:40px;object-fit:cover;border-radius:6px;background:var(--light-gray);"></td>
                <td><strong>{{ cat.name }}</strong></td>
                <td><div class="text-muted small" style="max-width:300px;white-space:pre-line;">{{ cat.description }}</div></td>
                <td>
                  <div v-if="cat.subcategories?.length > 0">
                    <ul class="small mb-0 ps-3">
                      <li v-for="s in cat.subcategories" :key="s.id">{{ s.name }}</li>
                    </ul>
                  </div>
                  <span class="text-muted" v-else>-</span>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(cat)" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(cat.id)" title="Eliminar"><i class="fas fa-trash"></i></button>
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
              Mostrando {{ filtered.length ? (pagination.offset + 1) : 0 }}–{{ Math.min(pagination.offset + pagination.limit, filtered.length) }} de {{ filtered.length }} categorías
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
            <h4 class="modal-title">{{ editing ? 'Editar' : 'Nueva' }} categoría</h4>
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
                <img v-if="form.cover" :src="form.cover" alt="Imagen categoría" style="width:100%;height:100%;object-fit:cover;">
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
import apiClient from '@/services/api'
export default {
  name: 'AdminCategories',
  data() {
    return {
      loading: true,
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
      form: { id: 0, name: '', description: '', cover: '', coverFile: null }
    }
  },
  computed: {
    filtered() {
      if (!this.search) return this.categories
      const s = this.search.toLowerCase()
      return this.categories.filter(cat =>
        (cat.name && cat.name.toLowerCase().includes(s)) ||
        (cat.description && cat.description.toLowerCase().includes(s))
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
    this.reload()
  },
  methods: {
    async reload() {
      this.loading = true
      try {
        const res = await apiClient.get('/api/categories')
        this.categories = res.data || []
      } catch (e) {
        this.error = e?.response?.data?.message || e?.message || 'Error al cargar categorías'
        this.categories = []
      } finally {
        this.loading = false
      }
    },
    setPage(page) {
      const p = Math.min(Math.max(1, page), this.totalPages)
      this.pagination.offset = (p - 1) * this.pagination.limit
    },
    openForm(c) {
      if (c) {
        this.editing = true
        this.form = { id: c.id, name: c.name, description: c.description, cover: this.$buildImg ? this.$buildImg(c.image_url) : c.image_url, coverFile: null }
      } else {
        this.editing = false
        this.form = { id: 0, name: '', description: '', cover: '', coverFile: null }
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
      this.formLoading = true
      try {
        const fd = new FormData()
        fd.append('name', this.form.name)
        fd.append('description', this.form.description || '')
        if (this.form.coverFile) {
          fd.append('image', this.form.coverFile)
        }
        await apiClient.post(`/api/categories/${this.form.id || 0}/update`, fd)
        // Cerrar modal antes de recargar
        this.show = false
        await this.reload()
      } catch (e) {
        alert('No se pudo actualizar la categoría: ' + (e?.response?.data?.message || e?.message))
      } finally {
        this.formLoading = false
      }
    },
    async remove(id) {
      if (!confirm('¿Eliminar esta categoría?')) return
      try {
        await apiClient.delete(`/api/categories/${id}`)
        await this.reload()
      } catch (e) {
        alert('No se pudo eliminar la categoría: ' + (e?.response?.data?.message || e?.message))
      }
    }
  }
}
</script>
<style scoped>
.card { border-radius: 12px; }
</style>
