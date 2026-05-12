<template>
  <div class="container-fluid p-4">
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="h3 mb-1">
          <i class="fas fa-chart-line text-primary me-2"></i>
          Analytics
        </h1>
        <p class="text-muted">Métricas clave de contenido y participación</p>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-3" v-for="kpi in kpis" :key="kpi.label">
        <div class="card border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="p-3 rounded me-3" :class="kpi.bg">
              <i :class="kpi.icon" class="fs-4"></i>
            </div>
            <div>
              <h4 class="mb-0">{{ kpi.value }}</h4>
              <small class="text-muted">{{ kpi.label }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent">
        <h5 class="mb-0">
          <i class="fas fa-chart-bar text-primary me-2"></i>
          Actividad reciente
        </h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Recurso</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in activity" :key="item.id">
                <td>{{ item.date }}</td>
                <td>{{ item.user }}</td>
                <td>{{ item.action }}</td>
                <td>{{ item.resource }}</td>
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
  name: 'AdminAnalytics',
  data() {
    return {
      kpis: [
        { label: 'Lugares', value: 128, icon: 'fas fa-map-marker-alt text-primary', bg: 'bg-primary bg-opacity-10' },
        { label: 'Hoteles', value: 42, icon: 'fas fa-bed text-success', bg: 'bg-success bg-opacity-10' },
        { label: 'Eventos', value: 23, icon: 'fas fa-calendar-alt text-warning', bg: 'bg-warning bg-opacity-10' },
        { label: 'Gastronomía', value: 57, icon: 'fas fa-utensils text-info', bg: 'bg-info bg-opacity-10' }
      ],
      activity: [
        { id: 1, date: '2025-10-01', user: 'agencia.andina', action: 'Creó', resource: 'Hotel Andino' },
        { id: 2, date: '2025-10-01', user: 'turismo.lpz', action: 'Actualizó', resource: 'Festival del Sol' },
        { id: 3, date: '2025-09-30', user: 'sabores.bo', action: 'Creó', resource: 'Restaurante Wari' }
      ]
    }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>



