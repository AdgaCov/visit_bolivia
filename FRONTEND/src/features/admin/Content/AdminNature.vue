<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-leaf text-primary me-2"></i>Naturaleza</h1>
        <p class="text-muted">Gestión de atractivos naturales</p>
      </div>
      <button class="btn btn-primary" @click="openForm()"><i class="fas fa-plus me-2"></i>Nuevo sitio</button>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4"><input class="form-control" placeholder="Buscar..." v-model="search"></div>
          <div class="col-md-4">
            <select class="form-select" v-model="region">
              <option value="">Todas las regiones</option>
              <option v-for="r in regiones" :key="r" :value="r">{{ r }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Imagen</th><th>Nombre</th><th>Región</th><th>Tipo</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
              <tr v-for="n in filtered" :key="n.id">
                <td><img :src="n.image" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:8px;background:var(--light-gray);"></td>
                <td><strong>{{ n.name }}</strong><div class="text-muted small">{{ n.location }}</div></td>
                <td><span class="badge bg-light text-dark">{{ n.region }}</span></td>
                <td>{{ n.kind }}</td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(n)"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(n.id)"><i class="fas fa-trash"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="show" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editing ? 'Editar' : 'Nuevo' }} sitio</h5>
            <button type="button" class="btn-close" @click="close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input class="form-control" v-model="form.name">
              </div>
              <div class="col-md-6">
                <label class="form-label">Región</label>
                <select class="form-select" v-model="form.region">
                  <option v-for="r in regiones" :key="r" :value="r">{{ r }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Ubicación</label>
                <input class="form-control" v-model="form.location">
              </div>
              <div class="col-md-6">
                <label class="form-label">Tipo</label>
                <select class="form-select" v-model="form.kind">
                  <option>Montaña</option>
                  <option>Lago</option>
                  <option>Río</option>
                  <option>Selva</option>
                  <option>Desierto</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Imagen</label>
                <input type="file" accept="image/*" class="form-control" @change="onImage">
              </div>
              <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" rows="3" v-model="form.description"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="close">Cancelar</button>
            <button class="btn btn-primary" @click="save">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
let nextId = 4
export default {
  name: 'AdminNature',
  data() {
    return {
      show: false,
      editing: false,
      search: '',
      region: '',
      regiones: ['Altiplano', 'Valles', 'Llanos', 'Amazonía'],
      items: [
        { id: 1, name: 'Salar de Uyuni', region: 'Altiplano', location: 'Potosí', kind: 'Desierto', image: '/images/41.png', description: '' },
        { id: 2, name: 'Lago Titicaca', region: 'Altiplano', location: 'La Paz', kind: 'Lago', image: '/images/12.png', description: '' }
      ],
      form: { id: 0, name: '', region: 'Altiplano', location: '', kind: 'Montaña', image: '', description: '' }
    }
  },
  computed: {
    filtered() {
      return this.items.filter(n =>
        (!this.search || n.name.toLowerCase().includes(this.search.toLowerCase())) &&
        (!this.region || n.region === this.region)
      )
    }
  },
  methods: {
    openForm(n) { this.show = true; this.editing = !!n; this.form = n ? { ...n } : { id: 0, name: '', region: 'Altiplano', location: '', kind: 'Montaña', image: '', description: '' } },
    close() { this.show = false },
    onImage(e) { const f = e.target.files?.[0]; if (f) this.form.image = URL.createObjectURL(f) },
    save() { if (this.editing) { const i = this.items.findIndex(x => x.id === this.form.id); this.items[i] = { ...this.form } } else { this.items.unshift({ ...this.form, id: nextId++ }) } this.close() },
    remove(id) { this.items = this.items.filter(x => x.id !== id) }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>



