<template>
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card h-100 shadow-sm">
      <img :src="lugar.imagenes[0]" class="card-img-top" :alt="lugar.nombre" style="height: 200px; object-fit: cover;">
      <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h5 class="card-title mb-0">{{ lugar.nombre }}</h5>
          <span v-if="lugar.destacado" class="badge bg-warning text-dark">
            <i class="fas fa-star me-1"></i>
            Destacado
          </span>
        </div>
        <p class="card-text text-muted mb-3">{{ lugar.descripcion.substring(0, 100) }}...</p>
        <div class="mb-3">
          <span class="badge bg-primary me-2">
            <i class="fas fa-map-marker-alt me-1"></i>
            {{ lugar.region }}
          </span>
          <span class="badge bg-info">
            <i class="fas fa-thermometer-half me-1"></i>
            {{ lugar.informacionPractica.clima }}
          </span>
        </div>
        <div class="mt-auto">
          <router-link :to="`/lugar/${lugar.id}`" class="btn btn-outline-primary w-100">
            <i class="fas fa-info-circle me-1"></i>
            Ver Detalles
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PlaceCard',
  props: {
    lugar: {
      type: Object,
      required: true
    }
  }
}
</script>

<style scoped>
.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: none;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}
.card-img-top {
  transition: transform 0.3s ease;
}
.card:hover .card-img-top {
  transform: scale(1.05);
}
.badge {
  font-size: 0.8rem;
}
</style> 