<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-utensils text-primary me-2"></i>Gastronomía</h1>
        <p class="text-muted">Gestión de restaurantes y experiencias gastronómicas</p>
      </div>
      <button class="btn btn-primary" @click="openForm()"><i class="fas fa-plus me-2"></i>Nuevo restaurante</button>
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
            <thead><tr><th>Logo</th><th>Nombre</th><th>Región</th><th>Especialidad</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
              <tr v-for="r in filtered" :key="r.id">
                <td><img :src="r.logo" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:8px;background:var(--light-gray);"></td>
                <td><strong>{{ r.name }}</strong><div class="text-muted small">{{ r.location }}</div></td>
                <td><span class="badge bg-light text-dark">{{ r.region }}</span></td>
                <td>{{ r.specialty }}</td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(r)"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(r.id)"><i class="fas fa-trash"></i></button>
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
            <h5 class="modal-title">{{ editing ? 'Editar' : 'Nuevo' }} restaurante</h5>
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
                <label class="form-label">Especialidad</label>
                <input class="form-control" v-model="form.specialty">
              </div>
              <div class="col-md-6">
                <label class="form-label">Logo</label>
                <input type="file" accept="image/*" class="form-control" @change="onLogo">
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
  name: 'AdminGastronomy',
  data() {
    return {
      show: false,
      editing: false,
      search: '',
      region: '',
      regiones: ['Altiplano', 'Valles', 'Llanos', 'Amazonía'],
      restaurants: [
        { id: 1, name: 'Sabores Andinos', region: 'Altiplano', location: 'La Paz', specialty: 'Comida típica', logo: '/images/5.png', description: '' },
        { id: 2, name: 'Amazon Taste', region: 'Amazonía', location: 'Rurrenabaque', specialty: 'Fusión', logo: '/images/12.png', description: '' }
      ],
      form: { id: 0, name: '', region: 'Altiplano', location: '', specialty: '', logo: '', description: '' }
    }
  },
  computed: {
    filtered() {
      return this.restaurants.filter(r =>
        (!this.search || r.name.toLowerCase().includes(this.search.toLowerCase())) &&
        (!this.region || r.region === this.region)
      )
    }
  },
  methods: {
    openForm(r) { this.show = true; this.editing = !!r; this.form = r ? { ...r } : { id: 0, name: '', region: 'Altiplano', location: '', specialty: '', logo: '', description: '' } },
    close() { this.show = false },
    onLogo(e) { const f = e.target.files?.[0]; if (f) this.form.logo = URL.createObjectURL(f) },
    save() {
      if (this.editing) { const i = this.restaurants.findIndex(x => x.id === this.form.id); this.restaurants[i] = { ...this.form } }
      else { this.restaurants.unshift({ ...this.form, id: nextId++ }) }
      this.close()
    },
    remove(id) { this.restaurants = this.restaurants.filter(x => x.id !== id) }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>



