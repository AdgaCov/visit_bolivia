import adminApiClient from '@/services/admin/api.admin'
import ADMIN_API_ENDPOINTS from '@/services/admin/endpoints.admin'

export interface LocationReview {
  id: number
  location_id: number
  user_id?: number | null
  rating: number // 1-5
  comment?: string | null
  created_at: string
  location?: {
    id: number
    name: string
  }
  user?: {
    id: number
    name: string
    email: string
  }
}

export interface CreateLocationReviewPayload {
  location_id: number
  user_id?: number | null
  rating: number // 1-5
  comment?: string | null
}

export interface UpdateLocationReviewPayload {
  rating?: number
  comment?: string | null
}

export interface LocationReviewsListParams {
  limit?: number
  offset?: number
  location_id?: number
  user_id?: number
}

async function list(params: LocationReviewsListParams = {}) {
  // La API devuelve: { success: true, data: [{ location: {...}, reviews: [...], average_rating, review_count }], pagination: {...} }
  interface LocationGroup {
    location: {
      id: number
      name: string
      type: string
      [key: string]: any
    }
    reviews: LocationReview[]
    average_rating: number
    review_count: number
  }
  
  // Convertir params a Record<string, unknown> para compatibilidad con apiClient
  const queryParams: Record<string, unknown> = {}
  if (params.limit != null) queryParams.limit = params.limit
  if (params.offset != null) queryParams.offset = params.offset
  if (params.location_id != null) queryParams.location_id = params.location_id
  if (params.user_id != null) queryParams.user_id = params.user_id
  
  return adminApiClient.get<{ 
    success: boolean
    data: LocationGroup[]
    pagination?: { limit: number; offset: number; count: number }
  }>(ADMIN_API_ENDPOINTS.LOCATION_REVIEWS.BASE, queryParams)
}

async function get(id: number | string) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_REVIEWS.BY_ID(id)
  return adminApiClient.get<{ success: boolean; data: LocationReview }>(url)
}

async function create(body: CreateLocationReviewPayload) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_REVIEWS.BASE
  return adminApiClient.post<{ success: boolean; data: LocationReview; message?: string }>(url, body)
}

async function update(id: number | string, body: UpdateLocationReviewPayload) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_REVIEWS.BY_ID(id)
  return adminApiClient.put<{ success: boolean; data: LocationReview; message?: string }>(url, body)
}

async function remove(id: number | string) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_REVIEWS.BY_ID(id)
  return adminApiClient.delete<{ success: boolean; message?: string }>(url)
}

async function getByLocation(locationId: number | string) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_REVIEWS.BY_LOCATION(locationId)
  return adminApiClient.get<{ success: boolean; data: LocationReview[] }>(url)
}

export default {
  list,
  get,
  create,
  update,
  remove,
  getByLocation
}

