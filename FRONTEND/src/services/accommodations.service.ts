import { apiClient } from './api'
import API_ENDPOINTS from './endpoints'
import type { 
  AccommodationApi, 
  AccommodationsResponseApi, 
  AccommodationImageApi,
  PlaceApi 
} from '@/types'

// Servicio para acomodaciones
export const accommodationsService = {
  // Obtener todas las acomodaciones
  async getAllAccommodations(): Promise<AccommodationsResponseApi> {
    return apiClient.get<AccommodationsResponseApi>(API_ENDPOINTS.ACCOMMODATIONS.BASE)
  },

  // Obtener acomodación por ID
  async getAccommodationById(id: number): Promise<AccommodationApi> {
    return apiClient.get<AccommodationApi>(API_ENDPOINTS.ACCOMMODATIONS.BY_ID(id))
  },

  // Buscar acomodaciones por filtros
  async searchAccommodations(filters: {
    location?: string
    badge?: string
    min_price?: number
    max_price?: number
    capacity?: number
  } = {}): Promise<AccommodationsResponseApi> {
    return apiClient.get<AccommodationsResponseApi>('/api/accommodations/search', filters)
  }
}

// Función principal para obtener acomodaciones (con fallback a datos locales)
export async function fetchAccommodations(): Promise<AccommodationApi[]> {
  try {
    const response = await accommodationsService.getAllAccommodations()
    return response.data
  } catch (error) {
    console.error('Error fetching accommodations from API:', error)
    // Fallback: intentar desde locations/by-type/accommodation
    try {
      const res = await apiClient.get<{ success: boolean; data: any[] }>('/api/locations/by-type/accommodation')
      if (res && res.success === true && Array.isArray(res.data)) {
        return res.data.map(mapLocationToAccommodation)
      }
    } catch (e) {
      console.error('Error fetching accommodations from locations endpoint:', e)
    }
    // Último recurso: datos locales
    return getAllAccommodationsLocal()
  }
}

// Datos locales como fallback
function getAllAccommodationsLocal(): AccommodationApi[] {
  return [
    {
      id: 1,
      place_id: 1,
      title: "Cabañas ecológicas en los Andes",
      description: "Disfruta de la serenidad de cabañas ecológicas en los Andes bolivianos. Construidas con materiales sostenibles y vistas espectaculares.",
      location: "La Paz, Altiplano",
      room_type: "Cabaña doble",
      price_per_night: "120.00",
      capacity: 2,
      latitude: "-16.50000000",
      longitude: "-68.15000000",
      badge: "Ecológico",
      link: "/alojamiento/cabañas",
      facebook: "",
      instagram: "",
      whatsapp: "",
      place: {
        id: 1,
        users_id: 1,
        name: "La Paz",
        description: "Capital administrativa de Bolivia",
        latitude: "-16.50000000",
        longitude: "-68.15000000",
        address: "La Paz, Bolivia",
        opening_hours: "Todo el año",
        contact_info: "+591 2 1234567",
        created_at: "2025-01-01T00:00:00.000000Z"
      },
      images: []
    }
  ]
}

// Helper para mapear una location (type: accommodation) a AccommodationApi
function mapLocationToAccommodation(loc: any): AccommodationApi {
  const images = Array.isArray(loc.images) ? loc.images : []
  const locationText = loc.address || (loc.department && loc.department.name) || 'Bolivia'
  return {
    id: Number(loc.id),
    place_id: 0,
    title: String(loc.name || ''),
    description: String(loc.description || ''),
    location: String(locationText),
    room_type: String(loc.room_type || ''),
    price_per_night: String(loc.price_per_night || '0'),
    capacity: (loc.capacity != null ? Number(loc.capacity) : undefined) as any,
    latitude: String(loc.latitude || ''),
    longitude: String(loc.longitude || ''),
    badge: loc.badge || '',
    link: loc.link || '',
    facebook: loc.facebook || '',
    instagram: loc.instagram || '',
    whatsapp: loc.whatsapp || '',
    place: {
      id: 0,
      users_id: 0,
      name: locationText,
      description: locationText,
      latitude: String(loc.latitude || ''),
      longitude: String(loc.longitude || ''),
      address: String(loc.address || ''),
      opening_hours: '',
      contact_info: '',
      created_at: String(loc.created_at || new Date().toISOString())
    } as any,
    images: images as unknown as AccommodationImageApi[]
  } as AccommodationApi
}

export default accommodationsService
