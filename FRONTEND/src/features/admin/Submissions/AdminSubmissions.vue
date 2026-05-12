<template>
  <div class="container-fluid p-4">
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h3 mb-1">
            <i class="fas fa-inbox text-primary me-2"></i>
            Envíos de usuarios
          </h1>
          <p class="text-muted">Revisa y aprueba contenido enviado por usuarios
          </p>
        </div>
        <div class="d-flex gap-2">
          <input type="text" class="form-control" placeholder="Buscar..." v-model="search" style="max-width: 280px;">
          <select class="form-select" v-model="statusFilter">
            <option value="">Todos</option>
            <option value="pending">Pendiente</option>
            <option value="approved">Aprobado</option>
            <option value="rejected">Rechazado</option>
          </select>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Título</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in filtered" :key="item.id">
                <td><span class="badge bg-light text-dark">{{ item.type }}</span></td>
                <td>{{ item.title }}</td>
                <td>{{ item.user }}</td>
                <td>{{ item.date }}</td>
                <td>
                  <span :class="['badge', item.status === 'approved' ? 'bg-success' : item.status === 'rejected' ? 'bg-danger' : 'bg-warning']">
                    {{ item.status }}
                  </span>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-success" @click="approve(item)"><i class="fas fa-check me-1"></i>Aprobar</button>
                    <button class="btn btn-sm btn-outline-danger" @click="reject(item)"><i class="fas fa-times me-1"></i>Rechazar</button>
                    <button class="btn btn-sm btn-outline-secondary" @click="view(item)"><i class="fas fa-eye"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminSubmissions',
  data() {
    return {
      search: '',
      statusFilter: '',
      items: [
        { id: 1, type: 'hotel', title: 'Hotel Titicaca', user: 'los.andes', date: '2025-09-28', status: 'pending' },
        { id: 2, type: 'evento', title: 'Carnaval Oruro', user: 'oruro.eventos', date: '2025-09-27', status: 'approved' },
        { id: 3, type: 'gastronomia', title: 'Café Altura', user: 'sabores.bo', date: '2025-09-26', status: 'rejected' }
      ]
    }
  },
  computed: {
    filtered() {
      return this.items.filter(it =>
        (!this.statusFilter || it.status === this.statusFilter) &&
        (!this.search || it.title.toLowerCase().includes(this.search.toLowerCase()))
      )
    }
  },
  methods: {
    approve(item) { item.status = 'approved' },
    reject(item) { item.status = 'rejected' },
    view(item) { alert(`Vista previa de: ${item.title}`) }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>



