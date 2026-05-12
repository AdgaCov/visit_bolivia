import adminApiClient from '@/services/admin/api.admin'
import ADMIN_API_ENDPOINTS from '@/services/admin/endpoints.admin'

export interface LocationSubcategoryLink {
  id?: number
  location_id: number
  location_name: string
  subcategory_id: number
  subcategory_name: string
  created_at?: string
  updated_at?: string
}

async function list() {
  return adminApiClient.get<{
    success: boolean
    data: LocationSubcategoryLink[]
    count?: number
  }>(ADMIN_API_ENDPOINTS.LOCATION_SUBCATEGORIES.BASE)
}

async function create(body: { location_id: number | string; subcategory_id: number | string }) {
  return adminApiClient.post<{
    success: boolean
    data: LocationSubcategoryLink
    message?: string
  }>(ADMIN_API_ENDPOINTS.LOCATION_SUBCATEGORIES.BASE, body)
}

async function remove(locationId: number | string, subcategoryId: number | string) {
  return adminApiClient.delete<{ success: boolean; message?: string }>(
    ADMIN_API_ENDPOINTS.LOCATION_SUBCATEGORIES.BY_LOCATION_AND_SUBCATEGORY(locationId, subcategoryId)
  )
}

export default {
  list,
  create,
  remove
}


