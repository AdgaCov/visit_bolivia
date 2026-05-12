import { defineStore } from 'pinia'
import type { Place } from '@/types'
import { lugaresTuristicos } from '@/assets/data/lugares.js'

interface PlacesState {
  places: Place[]
  loading: boolean
  error: string | null
  filters: {
    region: string
    search: string
    destacados: boolean
    categoria: string
    tipo: string
  }
}

export const usePlacesStore = defineStore('places', {
  state: (): PlacesState => ({
    places: [],
    loading: false,
    error: null,
    filters: {
      region: '',
      search: '',
      destacados: false,
      categoria: '',
      tipo: ''
    }
  }),

  getters: {
    // Obtener todos los lugares
    allPlaces: (state: PlacesState) => state.places,
    
    // Obtener lugares destacados
    destacados: (state: PlacesState) => state.places.filter((place: Place) => place.destacado),
    
    // Obtener regiones únicas
    regiones: (state: PlacesState) => [...new Set(state.places.map((place: Place) => place.region))],
    
    // Obtener lugares filtrados
    lugaresFiltrados: (state: PlacesState) => {
      let filtered = state.places
      
      if (state.filters.region) {
        filtered = filtered.filter((place: Place) => place.region === state.filters.region)
      }
      
      if (state.filters.search) {
        const search = state.filters.search.toLowerCase()
        filtered = filtered.filter((place: Place) =>
          place.nombre.toLowerCase().includes(search) ||
          place.descripcion.toLowerCase().includes(search)
        )
      }
      
      if (state.filters.destacados) {
        filtered = filtered.filter((place: Place) => place.destacado)
      }
      
      return filtered
    },
    
    // Estadísticas
    estadisticas: (state: PlacesState) => ({
      total: state.places.length,
      destacados: state.places.filter((p: Place) => p.destacado).length,
      regiones: [...new Set(state.places.map((p: Place) => p.region))].length
    })
  },

  actions: {
    // Cargar lugares
    async fetchPlaces() {
      this.loading = true
      this.error = null
      
      try {
        // Simular delay de API
        await new Promise(resolve => setTimeout(resolve, 500))
        this.places = [...lugaresTuristicos] as Place[]
        
        // Cache en localStorage para mejor performance
        localStorage.setItem('places_cache', JSON.stringify(this.places))
        localStorage.setItem('places_cache_timestamp', Date.now().toString())
      } catch (err) {
        this.error = 'Error al cargar los lugares'
        console.error('Error fetching places:', err)
      } finally {
        this.loading = false
      }
    },

    // Cargar desde cache si está disponible
    loadFromCache() {
      const cached = localStorage.getItem('places_cache')
      const timestamp = localStorage.getItem('places_cache_timestamp')
      
      if (cached && timestamp) {
        const cacheAge = Date.now() - parseInt(timestamp)
        const maxAge = 5 * 60 * 1000 // 5 minutos
        
        if (cacheAge < maxAge) {
          this.places = JSON.parse(cached)
          return true
        }
      }
      return false
    },

    // Obtener lugar por ID
    getPlaceById(id: string | number): Place | null {
      return this.places.find((place: Place) => String(place.id) === String(id)) || null
    },

    // Actualizar filtros
    updateFilters(newFilters: Partial<PlacesState['filters']>) {
      this.filters = { ...this.filters, ...newFilters }
    },

    // Limpiar filtros
    clearFilters() {
      this.filters = {
        region: '',
        search: '',
        destacados: false,
        categoria: '',
        tipo: ''
      }
    },

    // Buscar lugares
    searchPlaces(query: string) {
      this.filters.search = query
    },

    // Filtrar por región
    filterByRegion(region: string) {
      this.filters.region = region
    },

    // Toggle destacados
    toggleDestacados() {
      this.filters.destacados = !this.filters.destacados
    }
  }
})
