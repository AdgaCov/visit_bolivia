<template>
  <div class="container-fluid p-4">
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h3 mb-1"><i class="fas fa-bed text-primary me-2"></i>Hoteles</h1>
          <p class="text-muted">Gestión de hoteles y alojamientos</p>
        </div>
        <button class="btn btn-primary" @click="openForm()"><i class="fas fa-plus me-2"></i>Nuevo hotel</button>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4"><input class="form-control" placeholder="Buscar hotel..." v-model="search"></div>
          <div class="col-md-3">
            <select class="form-select" v-model="region">
              <option value="">Todas las regiones</option>
              <option v-for="r in regiones" :key="r" :value="r">{{ r }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model="stars">
              <option value="">Todas las categorías</option>
              <option v-for="n in [5,4,3,2,1]" :key="n" :value="n">{{ n }} estrellas</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Logo</th><th>Nombre</th><th>Región</th><th>Categoría</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
              <tr v-for="h in filtered" :key="h.id">
                <td><img :src="h.logo" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:8px;background:var(--light-gray);"></td>
                <td><strong>{{ h.name }}</strong><div class="text-muted small">{{ h.location }}</div></td>
                <td><span class="badge bg-light text-dark">{{ h.region }}</span></td>
                <td>{{ h.stars }}★</td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(h)"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(h.id)"><i class="fas fa-trash"></i></button>
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
            <h5 class="modal-title">{{ editing ? 'Editar' : 'Nuevo' }} hotel</h5>
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
              <div class="col-md-3">
                <label class="form-label">Categoría (★)</label>
                <input type="number" min="1" max="5" class="form-control" v-model.number="form.stars">
              </div>
              <div class="col-md-3">
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
  name: 'AdminHotels',
  data() {
    return {
      show: false,
      editing: false,
      search: '',
      region: '',
      stars: '',
      regiones: ['Altiplano', 'Valles', 'Llanos', 'Amazonía'],
      hotels: [
        { id: 1, name: 'Hotel Andino', region: 'Altiplano', location: 'La Paz', stars: 4, logo: '/images/10.png', description: 'Vista a la cordillera' },
        { id: 2, name: 'Eco Lodge', region: 'Amazonía', location: 'Rurrenabaque', stars: 5, logo: '/images/12.png', description: 'Naturaleza y confort' },
        { id: 3, name: 'Hotel Valle', region: 'Valles', location: 'Cochabamba', stars: 3, logo: '/images/13.png', description: 'Céntrico y cómodo' }
      ],
      form: { id: 0, name: '', region: 'Altiplano', location: '', stars: 3, logo: '', description: '' }
    }
  },
  computed: {
    filtered() {
      return this.hotels.filter(h =>
        (!this.search || h.name.toLowerCase().includes(this.search.toLowerCase())) &&
        (!this.region || h.region === this.region) &&
        (!this.stars || h.stars === Number(this.stars))
      )
    }
  },
  methods: {
    openForm(h) { this.show = true; this.editing = !!h; this.form = h ? { ...h } : { id: 0, name: '', region: 'Altiplano', location: '', stars: 3, logo: '', description: '' } },
    close() { this.show = false },
    onLogo(e) { const f = e.target.files?.[0]; if (f) this.form.logo = URL.createObjectURL(f) },
    save() {
      if (this.editing) {
        const idx = this.hotels.findIndex(x => x.id === this.form.id)
        this.hotels[idx] = { ...this.form }
      } else {
        this.hotels.unshift({ ...this.form, id: nextId++ })
      }
      this.close()
    },
    remove(id) { this.hotels = this.hotels.filter(h => h.id !== id) }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>



