me dice que <template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-newspaper text-primary me-2"></i>Artículos</h1>
        <p class="text-muted">Gestión de artículos y contenidos editoriales</p>
      </div>
      <button class="btn btn-primary" @click="openForm()"><i class="fas fa-plus me-2"></i>Nuevo artículo</button>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6"><input class="form-control" placeholder="Buscar..." v-model="search"></div>
          <div class="col-md-3">
            <select class="form-select" v-model="category">
              <option value="">Todas las categorías</option>
              <option>Historia</option>
              <option>Cultura</option>
              <option>Aventura</option>
              <option>Gastronomía</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Portada</th><th>Título</th><th>Categoría</th><th>Autor</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
              <tr v-for="a in filtered" :key="a.id">
                <td><img :src="a.cover" alt="" style="width:64px;height:40px;object-fit:cover;border-radius:6px;background:var(--light-gray);"></td>
                <td><strong>{{ a.title }}</strong><div class="text-muted small">{{ a.excerpt }}</div></td>
                <td><span class="badge bg-light text-dark">{{ a.category }}</span></td>
                <td>{{ a.author }}</td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(a)"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(a.id)"><i class="fas fa-trash"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="loading" class="text-center p-4">
          <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
          <span class="ms-2">Cargando...</span>
        </div>
        <div v-if="!loading && filtered.length === 0" class="text-center p-4 text-muted">
          No se encontraron artículos
        </div>
        <div v-if="!loading && (pagination.count || items.length)" class="card-footer bg-white border-top">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <small class="text-muted">
              Mostrando {{ items.length }} de {{ pagination.count ? pagination.count : (pagination.hasMore ? (items.length + '+') : items.length) }} artículos
            </small>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item" :class="{ disabled: currentPage <= 1 }" @click="handlePageClick('first')">
                  <span class="page-link">Primera</span>
                </li>
                <li class="page-item" :class="{ disabled: currentPage <= 1 }" @click="handlePageClick('prev')">
                  <span class="page-link">«</span>
                </li>
                <li class="page-item" v-for="p in pages" :key="p" :class="{ active: p === currentPage }" @click="handlePageClick(p)">
                  <span class="page-link">{{ p }}</span>
                </li>
                <li class="page-item" :class="{ disabled: currentPage >= totalPages }" @click="handlePageClick('next')">
                  <span class="page-link">»</span>
                </li>
                <li class="page-item" :class="{ disabled: currentPage >= totalPages }" @click="handlePageClick('last')">
                  <span class="page-link">Última</span>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <div v-if="show" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editing ? 'Editar' : 'Nuevo' }} artículo</h5>
            <button type="button" class="btn-close" @click="close" :disabled="saving"></button>
          </div>
          <!-- Progress Bar -->
          <div v-if="saving" class="progress" style="height: 4px; border-radius: 0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                 role="progressbar" 
                 style="width: 100%;">
            </div>
          </div>
          <div class="modal-body">
            <div class="row g-4">
              <div class="col-lg-8">
            <div class="row g-3">
                  <div class="col-12">
                <label class="form-label">Título</label>
                <input class="form-control" v-model="form.title">
              </div>
                  <div class="col-12">
                    <label class="form-label">Subtítulo</label>
                    <input class="form-control" v-model="form.subtitle">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Descripción breve</label>
                    <textarea class="form-control" rows="2" v-model="form.description"></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Autor</label>
                    <input class="form-control" v-model="form.author">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Categoría (etiqueta interna)</label>
                <select class="form-select" v-model="form.category">
                  <option>Historia</option>
                  <option>Cultura</option>
                  <option>Aventura</option>
                  <option>Gastronomía</option>
                </select>
              </div>
                  <div class="col-md-4">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" v-model="form.type">
                      <option value="custom">custom</option>
                      <option value="place">place</option>
                      <option value="event">event</option>
                      <option value="restaurant">restaurant</option>
                      <option value="accommodation">accommodation</option>
                    </select>
              </div>
                  <div class="col-md-4">
                    <label class="form-label">Page section</label>
                    <input class="form-control" v-model="form.page_section" placeholder="home | what_to_do_grid | planning...">
              </div>
                  <div class="col-md-4">
                    <label class="form-label">Template ID</label>
                    <input type="number" class="form-control" v-model.number="form.template_id">
              </div>
              <div class="col-12">
                <label class="form-label">Contenido</label>
                    <textarea class="form-control" rows="8" v-model="form.content" placeholder="Escribe el artículo..."></textarea>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                  <div class="card-body">
                    <label class="form-label">Portada</label>
                    <div class="ratio ratio-16x9 mb-2" style="border-radius: 8px; overflow: hidden; background: var(--bg-secondary);">
                      <img v-if="form.cover" :src="form.cover" alt="" style="width:100%;height:100%;object-fit:cover;">
                      <div v-else class="d-flex align-items-center justify-content-center text-muted">Sin imagen</div>
                    </div>
                    <div class="d-grid gap-2">
                      <input type="file" accept="image/*" class="form-control" @change="onCover">
                      <input class="form-control" placeholder="o pega URL de imagen" v-model="form.cover">
                      <button class="btn btn-outline-secondary" type="button" @click="form.cover=''">Quitar imagen</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="close" :disabled="saving">Cancelar</button>
            <button class="btn btn-primary" @click="save" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <span v-if="saving">{{ editing ? 'Actualizando...' : 'Guardando...' }}</span>
              <span v-else>{{ editing ? 'Actualizar' : 'Guardar' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
let nextId = 4
import adminArticlesService from '@/services/admin/articles.admin.service'
export default {
  name: 'AdminArticles',
  data() {
    return {
      loading: false,
      saving: false,
      error: '',
      show: false,
      editing: false,
      search: '',
      category: '',
      items: [],
      form: { id: 0, title: '', subtitle: '', description: '', category: 'Historia', author: '', cover: '', coverFile: null, excerpt: '', content: '', type: 'custom', page_section: 'home', template_id: 1, id_category: null },
      pagination: {
        limit: 500,
        offset: 0,
        count: 0,
        hasMore: true
      },
      loadingMore: false
    }
  },
  computed: {
    filtered() {
      return this.items.filter(a =>
        (!this.search || a.title.toLowerCase().includes(this.search.toLowerCase())) &&
        (!this.category || a.category === this.category)
      )
    },
    currentPage() {
      return Math.floor(this.pagination.offset / this.pagination.limit) + 1
    },
    totalPages() {
      if (this.pagination && this.pagination.count) {
        return Math.max(1, Math.ceil(this.pagination.count / this.pagination.limit))
      }
      // Sin count: si hay más, al menos existe la siguiente página; si no, quedarse en la actual
      return this.pagination.hasMore ? (this.currentPage + 1) : Math.max(1, this.currentPage)
    },
    pages() {
      const total = this.totalPages
      if (!total) return []
      const current = this.currentPage
      const start = Math.max(1, current - 2)
      const end = Math.min(total, current + 2)
      const arr = []
      for (let i = start; i <= end; i++) arr.push(i)
      return arr
    }
  },
  mounted() {
    this.reload()
  },
  methods: {
    async reload() {
      this.pagination.offset = 0
      await this.load(true)
    },
    async load(reset = true) {
      try {
        if (reset) {
          this.loading = true
          this.items = []
        } else {
          this.loadingMore = true
        }
        this.error = ''
        const params = {
          limit: this.pagination.limit,
          offset: this.pagination.offset
        }
        console.log('[AdminArticles] list params =>', params)
        const res = await adminArticlesService.list(params)
        const data = res?.data || []
        const mapped = data.map(a => ({
          id: a.id,
          title: a.title,
          subtitle: a.subtitle || '',
          description: a.description || '',
          category: a.category?.name || (a.subcategories?.[0]?.name || ''),
          author: a.author || '',
          // ✅ Imagen procesada con $buildImg
          cover: this.$buildImg ? this.$buildImg(a.media_url || (a.images && a.images[0]?.image_url) || '') : (a.media_url || (a.images && a.images[0]?.image_url) || ''),
          excerpt: a.subtitle || a.description || '',
          content: a.content || '',
          type: a.type || 'custom',
          page_section: a.page_section || '',
          template_id: a.template_id || 1,
          id_category: a.id_category || null
        }))
        // const mapped = data.map(a => ({
        //   id: a.id,
        //   title: a.title,
        //   category: a.category?.name || (a.subcategories?.[0]?.name || ''),
        //   author: a.author || '',
        //   cover: a.media_url || (a.images && a.images[0]?.image_url) || '',
        //   excerpt: a.subtitle || a.description || '',
        //   content: a.content || ''
        // }))
        
        if (reset) {
          this.items = mapped
        } else {
          this.items.push(...mapped)
        }
        
        if (res?.pagination) {
          this.pagination.count = res.pagination.count
          this.pagination.hasMore = (this.pagination.offset + data.length) < res.pagination.count
        } else {
          this.pagination.hasMore = data.length === this.pagination.limit
          if (!this.pagination.count) this.pagination.count = this.items.length
        }
      } catch (e) {
        console.error('Error cargando artículos', e)
        this.error = 'No se pudieron cargar los artículos'
        if (reset) this.items = []
      } finally {
        this.loading = false
        this.loadingMore = false
      }
    },
    async setPage(p) {
      const page = Math.min(Math.max(1, p), this.totalPages || 1)
      const newOffset = (page - 1) * this.pagination.limit
      if (newOffset === this.pagination.offset) return
      this.pagination.offset = newOffset
      await this.load(true)
    },
    async prevPage() { await this.setPage(this.currentPage - 1) },
    async nextPage() { await this.setPage(this.currentPage + 1) },
    async handlePageClick(target) {
      // Evitar clicks cuando esté "disabled"
      if (target === 'first') {
        if (this.currentPage <= 1) return
        return this.setPage(1)
      }
      if (target === 'prev') {
        if (this.currentPage <= 1) return
        return this.prevPage()
      }
      if (target === 'next') {
        if (this.currentPage >= this.totalPages) return
        return this.nextPage()
      }
      if (target === 'last') {
        if (this.currentPage >= this.totalPages) return
        return this.setPage(this.totalPages)
      }
      // Número de página
      const p = Number(target)
      if (!Number.isNaN(p)) {
        return this.setPage(p)
      }
    },
    async openForm(a) { 
      this.show = true; 
      this.editing = !!a; 
      if (a) {
        // Cargar artículo completo desde el backend para edición
        try {
          const articleRes = await adminArticlesService.get(a.id)
          const fullArticle = articleRes?.data
          
          if (fullArticle) {
            this.form = {
              id: fullArticle.id,
              title: fullArticle.title || '',
              subtitle: fullArticle.subtitle || '',
              description: fullArticle.description || '',
              category: fullArticle.category?.name || (fullArticle.subcategories?.[0]?.name || 'Historia'),
              author: fullArticle.author || '',
              cover: this.$buildImg ? this.$buildImg(fullArticle.media_url || (fullArticle.images && fullArticle.images[0]?.image_url) || '') : (fullArticle.media_url || (fullArticle.images && fullArticle.images[0]?.image_url) || ''),
              coverFile: null,
              excerpt: fullArticle.subtitle || fullArticle.description || '',
              content: fullArticle.content || '',
              type: fullArticle.type || 'custom',
              page_section: fullArticle.page_section || 'home',
              template_id: fullArticle.template_id || 1,
              id_category: fullArticle.id_category || null
            }
          } else {
            // Fallback a datos del listado si no se puede cargar completo
            this.form = {
              id: a.id,
              title: a.title || '',
              subtitle: a.subtitle || a.excerpt || '',
              description: a.description || a.excerpt || '',
              category: a.category || 'Historia',
              author: a.author || '',
              cover: a.cover || '',
              coverFile: null,
              excerpt: a.excerpt || '',
              content: a.content || '',
              type: a.type || 'custom',
              page_section: a.page_section || 'home',
              template_id: a.template_id || 1,
              id_category: a.id_category || null
            }
          }
        } catch (e) {
          console.error('Error cargando artículo completo:', e)
          // Fallback a datos del listado
          this.form = {
            id: a.id,
            title: a.title || '',
            subtitle: a.subtitle || a.excerpt || '',
            description: a.description || a.excerpt || '',
            category: a.category || 'Historia',
            author: a.author || '',
            cover: a.cover || '',
            coverFile: null,
            excerpt: a.excerpt || '',
            content: a.content || '',
            type: a.type || 'custom',
            page_section: a.page_section || 'home',
            template_id: a.template_id || 1,
            id_category: a.id_category || null
          }
        }
      } else {
        this.form = { 
          id: 0, 
          title: '', 
          subtitle: '', 
          description: '', 
          category: 'Historia', 
          author: '', 
          cover: '', 
          coverFile: null,
          excerpt: '', 
          content: '', 
          type: 'custom', 
          page_section: 'home', 
          template_id: 1, 
          id_category: null 
        }
      }
    },
    close() { 
      if (!this.saving) {
        this.show = false 
      }
    },
    onCover(e) { const f = e.target.files?.[0]; if (f) { this.form.coverFile = f; this.form.cover = URL.createObjectURL(f) } },
    async save() {
      if (!this.form.title || !this.form.title.trim()) {
        alert('El título es requerido')
        return
      }
      
      try {
        this.saving = true
        const fd = new FormData()
        fd.append('title', this.form.title.trim())
        if (this.form.subtitle) fd.append('subtitle', this.form.subtitle.trim())
        if (this.form.description) fd.append('description', this.form.description.trim())
        if (this.form.content) fd.append('content', this.form.content.trim())
        if (this.form.author) fd.append('author', this.form.author.trim())
        fd.append('type', this.form.type || 'custom')
        if (this.form.page_section) fd.append('page_section', this.form.page_section.trim())
        if (this.form.template_id != null && this.form.template_id !== '') {
          fd.append('template_id', String(this.form.template_id))
        }
        if (this.form.id_category != null && this.form.id_category !== '') {
          fd.append('id_category', String(this.form.id_category))
        }
        if (this.form.coverFile) {
          fd.append('image', this.form.coverFile)
        } else if (this.form.cover && this.form.cover.trim()) {
          fd.append('media_url', this.form.cover.trim())
        }

        if (this.editing) {
          // Actualizar artículo existente
          console.log('Actualizando artículo ID:', this.form.id)
          console.log('Enviando FormData:', {
            title: fd.get('title'),
            type: fd.get('type'),
            page_section: fd.get('page_section'),
            template_id: fd.get('template_id'),
            hasImage: !!fd.get('image'),
            hasMediaUrl: !!fd.get('media_url')
          })

          const res = await adminArticlesService.update(this.form.id, fd)
          console.log('Respuesta del servidor (update):', res)
          
          if (res?.success && res?.data) {
            // Cerrar modal antes de recargar
            this.saving = false
            this.show = false
            await this.reload()
          } else {
            throw new Error(res?.message || 'Respuesta inválida del servidor')
          }
        } else {
          // Crear nuevo artículo
          console.log('Creando nuevo artículo')
          console.log('Enviando FormData:', {
            title: fd.get('title'),
            type: fd.get('type'),
            page_section: fd.get('page_section'),
            template_id: fd.get('template_id'),
            hasImage: !!fd.get('image'),
            hasMediaUrl: !!fd.get('media_url')
          })

          const res = await adminArticlesService.create(fd)
          console.log('Respuesta del servidor (create):', res)
          
          if (res?.success && res?.data) {
            const a = res.data
            this.items.unshift({
              id: a.id,
              title: a.title,
              category: a.category?.name || (a.subcategories?.[0]?.name || ''),
              author: a.author || '',
              cover: this.$buildImg ? this.$buildImg(a.media_url || (a.images && a.images[0]?.image_url) || '') : (a.media_url || (a.images && a.images[0]?.image_url) || ''),
              excerpt: a.subtitle || a.description || '',
              content: a.content || ''
            })
            this.pagination.count = (this.pagination.count || 0) + 1
            // Cerrar modal y resetear formulario
            this.saving = false
            this.show = false
            this.form = { id: 0, title: '', subtitle: '', description: '', category: 'Historia', author: '', cover: '', coverFile: null, excerpt: '', content: '', type: 'custom', page_section: 'home', template_id: 1, id_category: null }
          } else {
            throw new Error(res?.message || 'Respuesta inválida del servidor')
          }
        }
      } catch (e) {
        console.error(`Error ${this.editing ? 'actualizando' : 'creando'} artículo:`, e)
        const errorMsg = e?.response?.data?.message || e?.message || 'Error desconocido'
        console.error('Detalles del error:', {
          error: e,
          response: e?.response,
          status: e?.response?.status,
          data: e?.response?.data
        })
        alert(`No se pudo ${this.editing ? 'actualizar' : 'crear'} el artículo: ${errorMsg}`)
      } finally {
        this.saving = false
      }
    },
    async remove(id) {
      if (confirm('¿Estás seguro de eliminar este artículo?')) {
        try {
          await adminArticlesService.remove(id)
          this.items = this.items.filter(x => x.id !== id)
          this.pagination.count = Math.max(0, this.pagination.count - 1)
        } catch (e) {
          console.error('Error eliminando artículo', e)
          alert('No se pudo eliminar el artículo')
        }
      }
    }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>
