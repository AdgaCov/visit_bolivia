import type { Place } from '@/types'
import { lugaresTuristicos } from '@/assets/data/lugares.js'

export function getAllPlaces(): Place[] {
  return [...lugaresTuristicos] as Place[]
}

export function getPlaceById(id: string | number): Place | null {
  return (lugaresTuristicos as Place[]).find(p => String(p.id) === String(id)) || null
}

export function searchPlaces({ query = '', categoria = '', ciudad = '', region = '', tipo = '' }: { query?: string; categoria?: string; ciudad?: string; region?: string; tipo?: string } = {}): Place[] {
  const q = query.trim().toLowerCase()

  return (lugaresTuristicos as Place[]).filter(place => {
    const inQuery = !q || [place.nombre, place.descripcion, place.region]
      .filter(Boolean)
      .some(text => String(text).toLowerCase().includes(q))

    const inRegion = !region || String(place.region).toLowerCase() === String(region).toLowerCase()

    const inCategoria = !categoria
    const inCiudad = !ciudad
    const inTipo = !tipo

    return inQuery && inRegion && inCategoria && inCiudad && inTipo
  })
} 