import apiClient from './api'
import API_ENDPOINTS from './endpoints'
import type { GastronomyItem, PaginatedResponse, GastronomyFilters, ApiResponse, RestaurantCardData } from '@/types/gastronomy'

export interface SearchResponse extends PaginatedResponse<GastronomyItem> {}

export const gastronomyService = {
  getAllRestaurants(params: Partial<GastronomyFilters> = {}) {
    // Usar el endpoint de locations con tipo restaurant ya que es el que funciona
    return apiClient.get<ApiResponse<GastronomyItem[]>>(API_ENDPOINTS.LOCATIONS.BY_TYPE('restaurant'), params)
  },

  getRestaurantById(id: string | number) {
    return apiClient.get<ApiResponse<GastronomyItem>>(API_ENDPOINTS.GASTRONOMY.BY_ID(id))
  },

  getRestaurantsByRegion(region: string, params: Partial<GastronomyFilters> = {}) {
    return apiClient.get<ApiResponse<GastronomyItem[]>>(`/api/restaurants/region/${encodeURIComponent(region)}`, params)
  },

  getRestaurantsByCuisine(cuisine: string, params: Partial<GastronomyFilters> = {}) {
    return apiClient.get<ApiResponse<GastronomyItem[]>>(`/api/restaurants/cuisine/${encodeURIComponent(cuisine)}`, params)
  },

  searchRestaurants(query: string, filters: Partial<GastronomyFilters> = {}) {
    return apiClient.get<ApiResponse<GastronomyItem[]>>('/api/restaurants/search', { q: query, ...filters })
  },

  getFeaturedRestaurants() {
    return apiClient.get<ApiResponse<GastronomyItem[]>>('/api/restaurants/featured')
  },

  // Función helper para transformar GastronomyItem a RestaurantCardData
  transformToRestaurantCard(restaurant: GastronomyItem): RestaurantCardData {
    const mainImage = restaurant.images.find(img => img.is_main) || restaurant.images[0]
    
    return {
      id: restaurant.id,
      title: restaurant.name,
      cuisine: restaurant.cuisine_type,
      location: restaurant.place.name,
      image: mainImage?.image_url || '/images/default-restaurant.jpg',
      description: restaurant.description
    }
  }
}

export default gastronomyService


