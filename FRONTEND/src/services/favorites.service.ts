import apiClient from './api'
import API_ENDPOINTS from './endpoints'

export type FavoriteType = 'places' | 'articles' | 'events' | 'gastronomy'

export interface FavoriteLocation {
  id: number
  user_id: number | null
  name: string
  description: string
  latitude: string
  longitude: string
  address?: string
  opening_hours?: string
  contact_info?: string
  type: string
  department_id: number
  badge?: string
  room_type?: string | null
  price_per_night?: string | null
  start_date?: string | null
  end_date?: string | null
  event_type?: string | null
  capacity?: number | null
  is_recurring?: boolean
  facebook?: string | null
  instagram?: string | null
  whatsapp?: string | null
  link?: string | null
  created_at: string
  updated_at: string
  favorites_count?: number
  department: {
    id: number
    name: string
    description: string
    image_url: string
    created_at: string
  }
  images: Array<{
    id: number
    location_id: number
    image_url: string
    alt_text?: string
    is_main: boolean
    uploaded_at: string
  }>
  items?: any[]
}

export interface Favorite {
  id: number
  user_id: number
  location_id: number
  created_at: string
  user?: {
    id: number
    name: string
    email: string
    role_id: number
    status: boolean
    created_at: string
  }
  location?: FavoriteLocation
}

export interface FavoritesResponse {
  success: boolean
  data: Favorite[]
  count?: number
}

export interface FavoriteActionResponse {
  success: boolean
  message?: string
  data?: Favorite
}

export interface FavoriteCard {
  id: number | string
  type: FavoriteType
  title: string
  description: string
  image: string
  location: string
  category: string
  badge?: string
  link: string
  locationId?: number | string
  favoritesCount?: number
  isTemporary?: boolean
}

class FavoritesService {
  /**
   * Obtiene los favoritos del usuario autenticado
   * Requiere token de autenticación
   */
  async getUserFavorites(): Promise<FavoritesResponse> {
    try {
      console.log('🔍 Favorites: Obteniendo favoritos del usuario desde:', API_ENDPOINTS.FAVORITES.BASE)
      const response = await apiClient.get<FavoritesResponse>(API_ENDPOINTS.FAVORITES.BASE)
      console.log('✅ Favorites: Respuesta recibida:', response)
      return response
    } catch (error: any) {
      console.error('❌ Favorites: Error fetching user favorites:', error)
      
      // Si es un 404, la ruta no existe en el backend
      if (error && error.response && error.response.status === 404) {
        const customError: any = new Error('La ruta de favoritos no está disponible en el servidor. Por favor, verifica que el endpoint /api/favorites esté configurado.')
        customError.response = error.response
        throw customError
      }
      
      throw error
    }
  }

  /**
   * Obtiene todos los favoritos populares (con favorites_count)
   * No requiere autenticación
   * La respuesta es Favorite[] con la misma estructura que getUserFavorites
   */
  async getAllFavorites(): Promise<FavoritesResponse> {
    try {
      console.log('🔍 Favorites: Obteniendo todos los favoritos desde:', API_ENDPOINTS.FAVORITES.ALL)
      const response = await apiClient.get<FavoritesResponse>(API_ENDPOINTS.FAVORITES.ALL)
      console.log('✅ Favorites: Respuesta recibida (todos los favoritos):', response)
      return response
    } catch (error: any) {
      console.error('❌ Favorites: Error fetching all favorites:', error)
      
      // Si es un 404, la ruta no existe en el backend
      if (error && error.response && error.response.status === 404) {
        const customError: any = new Error('La ruta de favoritos populares no está disponible en el servidor. Por favor, verifica que el endpoint /api/favorites/all esté configurado.')
        customError.response = error.response
        throw customError
      }
      
      throw error
    }
  }

  async addFavorite(locationId: number | string): Promise<FavoriteActionResponse> {
    return apiClient.post<FavoriteActionResponse, { location_id: number | string }>(
      API_ENDPOINTS.FAVORITES.BASE,
      { location_id: locationId }
    )
  }

  async removeFavorite(locationId: number | string): Promise<FavoriteActionResponse> {
    return apiClient.delete<FavoriteActionResponse>(`${API_ENDPOINTS.FAVORITES.BASE}/${locationId}`)
  }

  /**
   * Transforma un favorito de la API al formato esperado por el componente
   */
  transformFavoriteToCard(favorite: Favorite): FavoriteCard {
    const location = favorite.location
    if (!location) {
      throw new Error('El favorito no tiene una locacion asociada')
    }
    const mainImage = location.images?.find(img => img.is_main) || location.images?.[0]
    const imageUrl = mainImage?.image_url || '/images/placeholder.jpg'
    
    // Construir URL completa si es relativa
    const fullImageUrl = imageUrl.startsWith('http') 
      ? imageUrl 
      : `${process.env.VUE_APP_API_URL || 'http://localhost:8000'}/${imageUrl.replace(/^\//, '')}`

    return {
      id: favorite.id,
      type: this.mapLocationTypeToFavoriteType(location.type),
      title: location.name,
      description: location.description,
      image: fullImageUrl,
      location: location.department?.name || location.address || '',
      category: location.department?.name || '',
      badge: location.badge || '',
      link: this.getLocationRoute(location),
      locationId: location.id,
      favoritesCount: location.favorites_count || 0,
      isTemporary: false
    }
  }

  /**
   * Mapea el tipo de location al tipo de favorito
   */
  private mapLocationTypeToFavoriteType(type: string): FavoriteType {
    switch (type) {
      case 'restaurant':
        return 'gastronomy'
      case 'event':
        return 'events'
      case 'museum':
      case 'attraction':
      case 'city':
      case 'accommodation':
      default:
        return 'places'
    }
  }

  /**
   * Genera la ruta para una location
   */
  private getLocationRoute(location: FavoriteLocation): string {
    const id = location.id
    const type = location.type || 'attraction'
    
    switch (type) {
      case 'museum':
        return `/museo/${id}`
      case 'city':
        return `/ciudad/${id}`
      case 'restaurant':
        return `/location/${id}`
      case 'accommodation':
        return `/hotel/${id}`
      case 'attraction':
        return `/atractivo/${id}`
      default:
        return `/location/${id}`
    }
  }
}

export const favoritesService = new FavoritesService()
export default favoritesService

