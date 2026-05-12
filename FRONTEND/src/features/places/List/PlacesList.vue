<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="row mb-5">
      <div class="col-12 text-center">
        <h1 class="display-5 fw-bold mb-3">
          <i class="fas fa-map-marker-alt text-primary me-3"></i>
          Lugares Turísticos
        </h1>
        <p class="lead text-muted">Explora todos los destinos increíbles de Bolivia</p>
      </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
      <div class="col-12">
        <PlaceFilters
          :query="query"
          :region="region"
          :regiones="regionesDisponibles"
          @update:query="onUpdateQuery"
          @update:region="onUpdateRegion"
          @apply="() => {}"
        />
      </div>
    </div>

    <!-- Mapa -->
    <div class="row mb-4">
      <div class="col-12">
        <PlaceMap :lugares="lugaresFiltrados" :center="[-16.5, -68.15]" :zoom="5" />
      </div>
    </div>

    <!-- Resultados y Controles -->
    <div class="row">
      <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="d-flex align-items-center mb-2">
            <p class="mb-0 text-muted me-3">
              Mostrando {{ lugaresMostrados.length }} de {{ total }} lugares
            </p>
            <span class="badge bg-primary">
              Página {{ paginaActual }} de {{ totalPaginas }}
            </span>
          </div>
          <div class="d-flex align-items-center flex-wrap">
            <select class="form-select me-3 mb-2" v-model="lugaresPorPagina" style="width: 120px;">
              <option value="12">12 por página</option>
              <option value="24">24 por página</option>
              <option value="48">48 por página</option>
              <option value="96">96 por página</option>
            </select>
            <select class="form-select me-3 mb-2" v-model="orden" style="width: 180px;">
              <option value="nombre">Ordenar por nombre</option>
              <option value="region">Ordenar por región</option>
              <option value="destacado">Solo destacados</option>
              <option value="popularidad">Más populares</option>
              <option value="reciente">Más recientes</option>
            </select>
            <div class="btn-group mb-2" role="group">
              <button 
                type="button" 
                class="btn btn-outline-primary"
                :class="{ active: vista === 'grid' }"
                @click="vista = 'grid'"
              >
                <i class="fas fa-th"></i>
              </button>
              <button 
                type="button" 
                class="btn btn-outline-primary"
                :class="{ active: vista === 'list' }"
                @click="vista = 'list'"
              >
                <i class="fas fa-list"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Grid View -->
    <div v-if="vista === 'grid'" class="row">
      <PlaceCard 
        v-for="lugar in lugaresMostrados" 
        :key="lugar.id" 
        :lugar="lugar"
      />
    </div>

    <!-- List View -->
    <div v-else class="row">
      <div class="col-12">
        <div class="list-group">
          <div 
            v-for="lugar in lugaresMostrados" 
            :key="lugar.id"
            class="list-group-item list-group-item-action"
          >
            <div class="row align-items-center">
              <div class="col-md-2">
                <img 
                  :src="lugar.imagenes[0]" 
                  :alt="lugar.nombre"
                  class="img-fluid rounded"
                  style="height: 80px; object-fit: cover;"
                >
              </div>
              <div class="col-md-6">
                <div class="d-flex align-items-center mb-2">
                  <h5 class="mb-0 me-3">{{ lugar.nombre }}</h5>
                  <span v-if="lugar.destacado" class="badge bg-warning text-dark">
                    <i class="fas fa-star me-1"></i>
                    Destacado
                  </span>
                </div>
                <p class="text-muted mb-2">{{ lugar.descripcion.substring(0, 150) }}...</p>
                <div>
                  <span class="badge bg-primary me-2">{{ lugar.region }}</span>
                  <span class="badge bg-info">{{ lugar.informacionPractica.clima }}</span>
                </div>
              </div>
              <div class="col-md-4 text-end">
                <router-link :to="`/lugar/${lugar.id}`" class="btn btn-outline-primary">
                  <i class="fas fa-info-circle me-1"></i>
                  Ver Detalles
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sin resultados -->
    <div v-if="lugaresFiltrados.length === 0" class="text-center py-5">
      <i class="fas fa-search text-muted display-1 mb-3"></i>
      <h4 class="text-muted">No se encontraron lugares</h4>
      <p class="text-muted">Intenta con otros filtros de búsqueda</p>
      <button @click="limpiarFiltros" class="btn btn-primary">
        <i class="fas fa-times me-2"></i>
        Limpiar Filtros
      </button>
    </div>

    <!-- Paginación -->
    <div v-if="totalPaginas > 1" class="row mt-5">
      <div class="col-12">
        <nav aria-label="Navegación de páginas">
          <ul class="pagination justify-content-center">
            <!-- Botón Anterior -->
            <li class="page-item" :class="{ disabled: paginaActual === 1 }">
              <button 
                class="page-link" 
                @click="cambiarPagina(paginaActual - 1)"
                :disabled="paginaActual === 1"
              >
                <i class="fas fa-chevron-left"></i>
                Anterior
              </button>
            </li>

            <!-- Primera página -->
            <li v-if="paginaActual > 3" class="page-item">
              <button class="page-link" @click="cambiarPagina(1)">1</button>
            </li>

            <!-- Elipsis -->
            <li v-if="paginaActual > 4" class="page-item disabled">
              <span class="page-link">...</span>
            </li>

            <!-- Páginas alrededor de la actual -->
            <li 
              v-for="pagina in paginasAMostrar" 
              :key="pagina"
              class="page-item"
              :class="{ active: pagina === paginaActual }"
            >
              <button class="page-link" @click="cambiarPagina(pagina)">
                {{ pagina }}
              </button>
            </li>

            <!-- Elipsis -->
            <li v-if="paginaActual < totalPaginas - 3" class="page-item disabled">
              <span class="page-link">...</span>
            </li>

            <!-- Última página -->
            <li v-if="paginaActual < totalPaginas - 2" class="page-item">
              <button class="page-link" @click="cambiarPagina(totalPaginas)">
                {{ totalPaginas }}
              </button>
            </li>

            <!-- Botón Siguiente -->
            <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
              <button 
                class="page-link" 
                @click="cambiarPagina(paginaActual + 1)"
                :disabled="paginaActual === totalPaginas"
              >
                Siguiente
                <i class="fas fa-chevron-right"></i>
              </button>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Información de paginación -->
    <div v-if="totalPaginas > 1" class="row mt-3">
      <div class="col-12 text-center">
        <small class="text-muted">
          Mostrando lugares {{ (paginaActual - 1) * lugaresPorPagina + 1 }} - 
          {{ Math.min(paginaActual * lugaresPorPagina, lugaresFiltrados.length) }} 
          de {{ lugaresFiltrados.length }} resultados
        </small>
      </div>
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import { defineComponent, ref, computed, watch } from 'vue'
import PlaceCard from '@/components/place/PlaceCard.vue'
import PlaceFilters from '@/components/place/PlaceFilters.vue'
import PlaceMap from '@/components/place/PlaceMap.vue'
import { usePlaces } from '@/composables/usePlaces'

export default defineComponent({
  name: 'PlacesList',
  components: {
    PlaceCard,
    PlaceFilters,
    PlaceMap
  },
  setup() {
    const { query: q, region: r, resultados, all, regionesDisponibles, resetFilters } = usePlaces()

    const query = ref(q.value)
    const region = ref(r.value)
    const vista = ref('grid')
    const orden = ref('nombre')
    const paginaActual = ref(1)
    const lugaresPorPagina = ref(9)

    // Sync from local refs to composable
    const sync = () => {
      q.value = query.value
      r.value = region.value
    }

    function onUpdateQuery(val) {
      query.value = val
      paginaActual.value = 1 // Reset a primera página al buscar
    }

    function onUpdateRegion(val) {
      region.value = val
      paginaActual.value = 1 // Reset a primera página al filtrar
    }

    const lugaresFiltrados = computed(() => {
      sync()
      let arr = [...resultados.value]
      switch (orden.value) {
        case 'nombre':
          arr.sort((a, b) => a.nombre.localeCompare(b.nombre))
          break
        case 'region':
          arr.sort((a, b) => a.region.localeCompare(b.region))
          break
        case 'destacado':
          arr = arr.filter(l => !!l.destacado)
          break
        case 'popularidad':
          // Simular orden por popularidad (en un caso real vendría del backend)
          arr.sort((a, b) => (b.destacado ? 1 : 0) - (a.destacado ? 1 : 0))
          break
        case 'reciente':
          // Simular orden por fecha de creación (en un caso real vendría del backend)
          arr.sort((a, b) => b.id - a.id)
          break
      }
      return arr
    })

    const totalPaginas = computed(() => {
      return Math.ceil(lugaresFiltrados.value.length / lugaresPorPagina.value)
    })

    const lugaresMostrados = computed(() => {
      const inicio = (paginaActual.value - 1) * lugaresPorPagina.value
      const fin = inicio + lugaresPorPagina.value
      return lugaresFiltrados.value.slice(inicio, fin)
    })

    const paginasAMostrar = computed(() => {
      const paginas = []
      const inicio = Math.max(1, paginaActual.value - 2)
      const fin = Math.min(totalPaginas.value, paginaActual.value + 2)
      
      for (let i = inicio; i <= fin; i++) {
        paginas.push(i)
      }
      return paginas
    })

    function cambiarPagina(pagina) {
      if (pagina >= 1 && pagina <= totalPaginas.value) {
        paginaActual.value = pagina
        // Scroll suave hacia arriba
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }
    }

    function limpiarFiltros() {
      resetFilters()
      query.value = ''
      region.value = ''
      orden.value = 'nombre'
      paginaActual.value = 1
    }

    // Watch para resetear página cuando cambian los filtros
    watch([query, region, orden], () => {
      paginaActual.value = 1
    })

    const total = computed(() => all.value.length)

    return {
      // state
      query,
      region,
      vista,
      orden,
      paginaActual,
      lugaresPorPagina,
      // data
      lugaresFiltrados,
      lugaresMostrados,
      regionesDisponibles,
      total,
      totalPaginas,
      paginasAMostrar,
      // methods
      limpiarFiltros,
      onUpdateQuery,
      onUpdateRegion,
      cambiarPagina
    }
  }
})
</script>

<style scoped>
.list-group-item {
  border: none;
  border-bottom: 1px solid #dee2e6;
  transition: background-color 0.3s ease;
}

.list-group-item:hover {
  background-color: #f8f9fa;
}

.btn-group .btn.active {
  background-color: #0d6efd;
  color: white;
}

.pagination .page-link {
  color: #0d6efd;
  border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: white;
}

.pagination .page-item.disabled .page-link {
  color: #6c757d;
  background-color: #fff;
  border-color: #dee2e6;
}

@media (max-width: 768px) {
  .pagination {
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 0.375rem 0.5rem;
    font-size: 0.875rem;
  }
}
</style> 