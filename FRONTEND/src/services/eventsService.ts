import apiClient from './api'
import API_ENDPOINTS from './endpoints'
import type { EventItem, PaginatedResponse, EventFilters, ApiResponse, EventCardData, LocationEventItem } from '@/types/events'

export interface SearchResponse extends PaginatedResponse<EventItem> {}

export const eventsService = {
  getAllEvents(params: Partial<EventFilters> = {}) {
    return apiClient.get<ApiResponse<EventItem[]>>(API_ENDPOINTS.EVENTS.BASE, params)
  },

  // Nueva fuente: locations/by-type/event (GET)
  getAllLocationEvents(params: Record<string, unknown> = {}) {
    // Endpoint absoluto según requerimiento
    return apiClient.get<ApiResponse<LocationEventItem[]>>(API_ENDPOINTS.LOCATIONS.BY_TYPE('event'), params)
  },

  getEventById(id: string | number) {
    return apiClient.get<ApiResponse<EventItem>>(`/api/events/${id}`)
  },

  getEventsByRegion(region: string, params: Partial<EventFilters> = {}) {
    return apiClient.get<ApiResponse<EventItem[]>>(`/api/events/region/${encodeURIComponent(region)}`, params)
  },

  getUpcomingEvents(limit = 10) {
    return apiClient.get<ApiResponse<EventItem[]>>('/api/events/upcoming', { limit })
  },

  searchEvents(query: string, filters: Partial<EventFilters> = {}) {
    return apiClient.get<ApiResponse<EventItem[]>>('/api/events/search', { q: query, ...filters })
  },

  getFeaturedEvents() {
    return apiClient.get<ApiResponse<EventItem[]>>('/api/events/featured')
  },

  // Función helper para transformar EventItem a EventCardData
  transformToEventCard(event: EventItem): EventCardData {
    const mainImage = event.images.find(img => img.is_main) || event.images[0]
    
    return {
      id: event.id,
      title: event.name,
      date: new Date(event.start_date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }),
      location: event.place.name,
      image: mainImage?.image_url || '/images/default-event.jpg',
      description: event.description
    }
  }
  ,
  // Transformar LocationEventItem (nueva API) a EventCardData
  transformLocationToEventCard(event: LocationEventItem): EventCardData {
    const mainImage = event.images.find(img => img.is_main) || event.images[0]
    const locationText = event.address || event.department?.name || 'Bolivia'

    return {
      id: event.id,
      title: event.name,
      date: new Date(event.start_date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }),
      location: locationText,
      image: mainImage?.image_url || '/images/default-event.jpg',
      description: event.description,
      to: { name: 'GastronomyDetail', params: { id: event.id } }
    }
  }
}

export default eventsService

