import { ref, computed } from 'vue'
import { getAllPlaces, searchPlaces } from '@/services/places.service'
import type { Place } from '@/types'

export function usePlaces(initial: Partial<{ query: string; region: string; categoria: string; ciudad: string; tipo: string }> = {}) {
  const all = ref<Place[]>(getAllPlaces())
  const query = ref(initial.query || '')
  const region = ref(initial.region || '')
  const categoria = ref(initial.categoria || '')
  const ciudad = ref(initial.ciudad || '')
  const tipo = ref(initial.tipo || '')

  const resultados = computed<Place[]>(() => {
    return searchPlaces({
      query: query.value,
      region: region.value,
      categoria: categoria.value,
      ciudad: ciudad.value,
      tipo: tipo.value
    })
  })

  const regionesDisponibles = computed<string[]>(() => {
    const set = new Set(all.value.map(p => p.region))
    return Array.from(set)
  })

  function resetFilters() {
    query.value = ''
    region.value = ''
    categoria.value = ''
    ciudad.value = ''
    tipo.value = ''
  }

  return {
    all,
    query,
    region,
    categoria,
    ciudad,
    tipo,
    resultados,
    regionesDisponibles,
    resetFilters
  }
} 