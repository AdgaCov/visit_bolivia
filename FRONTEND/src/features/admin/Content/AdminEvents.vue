<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-calendar-alt text-primary me-2"></i>Eventos</h1>
        <p class="text-muted">Gestión de eventos y festividades</p>
      </div>
      <button class="btn btn-primary" @click="openForm()"><i class="fas fa-plus me-2"></i>Nuevo evento</button>
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
            <thead><tr><th>Imagen</th><th>Título</th><th>Región</th><th>Fecha</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
              <tr v-for="e in filtered" :key="e.id">
                <td><img :src="e.image" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:8px;background:var(--light-gray);"></td>
                <td><strong>{{ e.title }}</strong><div class="text-muted small">{{ e.location }}</div></td>
                <td><span class="badge bg-light text-dark">{{ e.region }}</span></td>
                <td>{{ e.date }}</td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(e)"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(e.id)"><i class="fas fa-trash"></i></button>
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
            <h5 class="modal-title">{{ editing ? 'Editar' : 'Nuevo' }} evento</h5>
            <button type="button" class="btn-close" @click="close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Título</label>
                <input class="form-control" v-model="form.title">
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
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" v-model="form.date">
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
  name: 'AdminEvents',
  data() {
    return {
      show: false,
      editing: false,
      search: '',
      region: '',
      regiones: ['Altiplano', 'Valles', 'Llanos', 'Amazonía'],
      events: [
        { id: 1, title: 'Carnaval de Oruro', region: 'Altiplano', location: 'Oruro', date: '2026-02-15', image: '/images/feriado.png', description: '' },
        { id: 2, title: 'Festival de Música', region: 'Valles', location: 'Sucre', date: '2025-11-20', image: '/images/41.png', description: '' }
      ],
      form: { id: 0, title: '', region: 'Altiplano', location: '', date: '', image: '', description: '' }
    }
  },
  computed: {
    filtered() {
      return this.events.filter(e =>
        (!this.search || e.title.toLowerCase().includes(this.search.toLowerCase())) &&
        (!this.region || e.region === this.region)
      )
    }
  },
  methods: {
    openForm(e) { this.show = true; this.editing = !!e; this.form = e ? { ...e } : { id: 0, title: '', region: 'Altiplano', location: '', date: '', image: '', description: '' } },
    close() { this.show = false },
    onImage(ev) { const f = ev.target.files?.[0]; if (f) this.form.image = URL.createObjectURL(f) },
    save() { if (this.editing) { const i = this.events.findIndex(x => x.id === this.form.id); this.events[i] = { ...this.form } } else { this.events.unshift({ ...this.form, id: nextId++ }) } this.close() },
    remove(id) { this.events = this.events.filter(x => x.id !== id) }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>



