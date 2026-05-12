<template>
  <div v-if="lugar" class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link to="/">Inicio</router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link to="/lugares">Lugares</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ lugar.nombre }}</li>
      </ol>
    </nav>

    <!-- Header del lugar -->
    <div class="row mb-5">
      <div class="col-lg-8">
        <div class="d-flex align-items-center mb-3">
          <h1 class="display-4 fw-bold mb-0">{{ lugar.nombre }}</h1>
          <span v-if="lugar.destacado" class="badge bg-warning text-dark ms-3">
            <i class="fas fa-star me-1"></i>
            Destacado
          </span>
        </div>
        <p class="lead text-muted">{{ lugar.descripcion }}</p>
        <div class="mb-4">
          <span class="badge bg-primary me-2">
            <i class="fas fa-map-marker-alt me-1"></i>
            {{ lugar.region }}
          </span>
          <span class="badge bg-info me-2">
            <i class="fas fa-thermometer-half me-1"></i>
            {{ lugar.informacionPractica.clima }}
          </span>
          <span class="badge bg-success">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ lugar.informacionPractica.mejorEpoca }}
          </span>
        </div>
      </div>
      <div class="col-lg-4 text-end">
        <router-link to="/lugares" class="btn btn-outline-primary">
          <i class="fas fa-arrow-left me-2"></i>
          Volver a Lugares
        </router-link>
      </div>
    </div>

    <!-- Galería de imágenes -->
    <div class="row mb-5">
      <div class="col-12">
        <h3 class="mb-4">
          <i class="fas fa-images text-primary me-2"></i>
          Galería
        </h3>
        <PlaceGallery :images="lugar.imagenes" />
      </div>
    </div>

    <!-- Información práctica y mapa -->
    <div class="row mb-5">
      <div class="col-lg-8">
        <h3 class="mb-4">
          <i class="fas fa-info-circle text-primary me-2"></i>
          Información Práctica
        </h3>
        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body text-center">
                <i class="fas fa-calendar-alt text-primary display-4 mb-3"></i>
                <h5 class="card-title">Mejor Época</h5>
                <p class="card-text">{{ lugar.informacionPractica.mejorEpoca }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body text-center">
                <i class="fas fa-thermometer-half text-info display-4 mb-3"></i>
                <h5 class="card-title">Clima</h5>
                <p class="card-text">{{ lugar.informacionPractica.clima }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body text-center">
                <i class="fas fa-ticket-alt text-success display-4 mb-3"></i>
                <h5 class="card-title">Precio de Entrada</h5>
                <p class="card-text">{{ lugar.informacionPractica.precioEntrada }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
              <div class="card-body text-center">
                <i class="fas fa-plane text-warning display-4 mb-3"></i>
                <h5 class="card-title">Cómo Llegar</h5>
                <p class="card-text">{{ lugar.informacionPractica.comoLlegar }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">
              <i class="fas fa-map-marker-alt text-primary me-2"></i>
              Ubicación
            </h5>
            <p class="card-text">
              Coordenadas: {{ lugar.coordenadas[0] }}, {{ lugar.coordenadas[1] }}
            </p>
            <div class="bg-light p-3 rounded">
              <p class="mb-2"><strong>Región:</strong> {{ lugar.region }}</p>
              <p class="mb-0"><strong>Clima:</strong> {{ lugar.informacionPractica.clima }}</p>
            </div>
            <!-- Mapa interactivo -->
            <div class="mt-3">
              <PlaceMap 
                :lugar-activo="lugar"
                :lugares="lugaresRelacionados"
                :center="lugar.coordenadas"
                :zoom="10"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lugares relacionados -->
    <div class="row">
      <div class="col-12">
        <h3 class="mb-4">
          <i class="fas fa-compass text-primary me-2"></i>
          Otros Lugares en {{ lugar.region }}
        </h3>
        <div class="row">
          <div 
            v-for="lugarRelacionado in lugaresRelacionados" 
            :key="lugarRelacionado.id"
            class="col-lg-4 col-md-6 mb-4"
          >
            <div class="card h-100 shadow-sm">
              <img 
                :src="lugarRelacionado.imagenes[0]" 
                :alt="lugarRelacionado.nombre"
                class="card-img-top"
                style="height: 200px; object-fit: cover;"
              >
              <div class="card-body">
                <h5 class="card-title">{{ lugarRelacionado.nombre }}</h5>
                <p class="card-text text-muted">
                  {{ lugarRelacionado.descripcion.substring(0, 100) }}...
                </p>
                <router-link 
                  :to="`/lugar/${lugarRelacionado.id}`" 
                  class="btn btn-outline-primary btn-sm"
                >
                  Ver Detalles
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading o error -->
  <div v-else class="container py-5 text-center">
    <div v-if="loading" class="py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <p class="mt-3">Cargando información del lugar...</p>
    </div>
    <div v-else class="py-5">
      <i class="fas fa-exclamation-triangle text-warning display-1 mb-3"></i>
      <h4>Lugar no encontrado</h4>
      <p class="text-muted">El lugar que buscas no existe o ha sido removido.</p>
      <router-link to="/lugares" class="btn btn-primary">
        <i class="fas fa-arrow-left me-2"></i>
        Volver a Lugares
      </router-link>
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import { defineComponent, ref, computed, onMounted } from 'vue'
import PlaceGallery from '@/components/place/PlaceGallery.vue'
import PlaceMap from '@/components/place/PlaceMap.vue'
import { getAllPlaces, getPlaceById } from '@/services/places.service'

export default defineComponent({
  name: 'DetalleLugar',
  components: {
    PlaceGallery,
    PlaceMap
  },
  props: {
    id: {
      type: [String, Number],
      required: true
    }
  },
  setup(props) {
    const loading = ref(true)
    const lugar = computed(() => getPlaceById(props.id))
    const lugaresRelacionados = computed(() => {
      if (!lugar.value) return []
      return getAllPlaces()
        .filter(l => l.region === lugar.value.region && l.id !== lugar.value.id)
        .slice(0, 3)
    })

    onMounted(() => {
      setTimeout(() => {
        loading.value = false
      }, 300)
    })

    return {
      loading,
      lugar,
      lugaresRelacionados
    }
  }
})
</script>

<style scoped>
.card {
  transition: transform 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
}

.breadcrumb a {
  text-decoration: none;
  color: #0d6efd;
}

.breadcrumb a:hover {
  color: #0a58ca;
}
</style> 