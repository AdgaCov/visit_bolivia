<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="row mb-5">
      <div class="col-12 text-center">
        <h1 class="display-5 fw-bold mb-3">
          <i class="fas fa-globe-americas text-primary me-3"></i>
          Regiones de Bolivia
        </h1>
        <p class="lead text-muted">Descubre la diversidad geográfica y cultural de Bolivia</p>
      </div>
    </div>

    <!-- Regiones -->
    <div class="row">
      <div 
        v-for="region in regiones" 
        :key="region.id"
        class="col-lg-6 mb-4"
      >
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="region-icon me-3" :class="getRegionClass(region.nombre)">
                <i :class="getRegionIcon(region.nombre)"></i>
              </div>
              <div>
                <h3 class="card-title mb-1">{{ region.nombre }}</h3>
                <p class="text-muted mb-0">{{ region.altitud }}</p>
              </div>
            </div>
            
            <p class="card-text mb-4">{{ region.descripcion }}</p>
            
            <div class="row mb-4">
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <i class="fas fa-thermometer-half text-info me-2"></i>
                  <span><strong>Clima:</strong> {{ region.clima }}</span>
                </div>
              </div>
              <div class="col-6">
                <div class="d-flex align-items-center">
                  <i class="fas fa-mountain text-success me-2"></i>
                  <span><strong>Altitud:</strong> {{ region.altitud }}</span>
                </div>
              </div>
            </div>

            <!-- Lugares de la región -->
            <div class="mb-4">
              <h6 class="mb-3">
                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                Lugares en esta región:
              </h6>
              <div class="row">
                <div 
                  v-for="lugarId in region.lugares" 
                  :key="lugarId"
                  class="col-12 mb-2"
                >
                  <router-link 
                    :to="`/lugar/${lugarId}`"
                    class="text-decoration-none"
                  >
                    <div class="d-flex align-items-center p-2 rounded bg-light">
                      <i class="fas fa-map-marker-alt text-primary me-2"></i>
                      <span>{{ getLugarById(lugarId)?.nombre }}</span>
                    </div>
                  </router-link>
                </div>
              </div>
            </div>

            <router-link 
              :to="`/lugares?region=${region.nombre}`"
              class="btn btn-outline-primary w-100"
            >
              <i class="fas fa-search me-2"></i>
              Explorar Lugares de {{ region.nombre }}
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Mapa conceptual -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="card-title mb-4">
              <i class="fas fa-map text-primary me-2"></i>
              Mapa de Regiones
            </h3>
            <div class="row text-center">
              <div class="col-md-3 mb-4">
                <div class="region-map-item" data-region="Altiplano">
                  <div class="region-circle bg-primary">
                    <i class="fas fa-mountain text-white"></i>
                  </div>
                  <h6 class="mt-2">Altiplano</h6>
                  <small class="text-muted">3,600 - 4,100 msnm</small>
                </div>
              </div>
              <div class="col-md-3 mb-4">
                <div class="region-map-item" data-region="Valles">
                  <div class="region-circle bg-success">
                    <i class="fas fa-hills text-white"></i>
                  </div>
                  <h6 class="mt-2">Valles</h6>
                  <small class="text-muted">1,800 - 2,800 msnm</small>
                </div>
              </div>
              <div class="col-md-3 mb-4">
                <div class="region-map-item" data-region="Yungas">
                  <div class="region-circle bg-warning">
                    <i class="fas fa-tree text-white"></i>
                  </div>
                  <h6 class="mt-2">Yungas</h6>
                  <small class="text-muted">1,200 - 2,400 msnm</small>
                </div>
              </div>
              <div class="col-md-3 mb-4">
                <div class="region-map-item" data-region="Llanos">
                  <div class="region-circle bg-info">
                    <i class="fas fa-sun text-white"></i>
                  </div>
                  <h6 class="mt-2">Llanos</h6>
                  <small class="text-muted">200 - 800 msnm</small>
                </div>
              </div>
            </div>
            <div class="row text-center mt-3">
              <div class="col-md-6 offset-md-3">
                <div class="region-map-item" data-region="Amazonía">
                  <div class="region-circle bg-dark">
                    <i class="fas fa-leaf text-white"></i>
                  </div>
                  <h6 class="mt-2">Amazonía</h6>
                  <small class="text-muted">150 - 500 msnm</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { regiones } from '@/assets/data/regiones.js'
import { usePlacesStore } from '@/store/places'

export default {
  name: 'RegionsPage',
  data() {
    return {
      regiones: regiones
    }
  },
  computed: {
    placesStore() {
      return usePlacesStore()
    }
  },
  methods: {
    getRegionClass(nombre) {
      const classes = {
        'Altiplano': 'bg-primary',
        'Valles': 'bg-success',
        'Yungas': 'bg-warning',
        'Llanos': 'bg-info',
        'Amazonía': 'bg-dark'
      }
      return classes[nombre] || 'bg-secondary'
    },
    getRegionIcon(nombre) {
      const icons = {
        'Altiplano': 'fas fa-mountain',
        'Valles': 'fas fa-hills',
        'Yungas': 'fas fa-tree',
        'Llanos': 'fas fa-sun',
        'Amazonía': 'fas fa-leaf'
      }
      return icons[nombre] || 'fas fa-map-marker-alt'
    },
    getLugarById(id) {
      return this.placesStore.getPlaceById(id)
    }
  }
}
</script>

<style scoped>
.region-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.region-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  font-size: 2rem;
}

.region-map-item {
  cursor: pointer;
  transition: transform 0.3s ease;
}

.region-map-item:hover {
  transform: scale(1.05);
}

.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}
</style> 