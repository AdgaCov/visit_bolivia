import adminApiClient from '@/services/admin/api.admin'
import ADMIN_API_ENDPOINTS from '@/services/admin/endpoints.admin'

export interface AdminLocationsListParams {
  limit?: number
  offset?: number
  type?: string
}

export interface AdminLocationImage {
  id: number
  image_url: string
  is_main?: boolean
}

export interface AdminLocationItem {
  id: number
  name: string
  description?: string | null
  type: string
  destination_route_type?: string | null
  department?: { id: number; name: string; image_url?: string }
  images?: AdminLocationImage[]
  address?: string | null
  created_at?: string
}

async function list(params: AdminLocationsListParams = {}) {
  const query = new URLSearchParams()
  if (params.limit != null) query.set('limit', String(params.limit))
  if (params.offset != null) query.set('offset', String(params.offset))
  if (params.type) query.set('type', params.type)
  const url = `${ADMIN_API_ENDPOINTS.LOCATIONS.BASE}${query.toString() ? `?${query.toString()}` : ''}`
  return adminApiClient.get<{ success: boolean; data: AdminLocationItem[]; pagination?: { limit: number; offset: number; count: number } }>(url)
}

async function listByUser(userId: string | number, params: AdminLocationsListParams = {}) {
  const query = new URLSearchParams()
  if (params.limit != null) query.set('limit', String(params.limit))
  if (params.offset != null) query.set('offset', String(params.offset))
  if (params.type) query.set('type', params.type)
  const url = `${ADMIN_API_ENDPOINTS.LOCATIONS.BY_USER(userId)}${query.toString() ? `?${query.toString()}` : ''}`
  return adminApiClient.get<{ success: boolean; data: AdminLocationItem[]; pagination?: { limit: number; offset: number; count: number } }>(url)
}

async function remove(id: number | string) {
  const url = ADMIN_API_ENDPOINTS.LOCATIONS.BY_ID(id)
  return adminApiClient.delete<{ success: boolean; message?: string }>(url)
}

async function get(id: number | string) {
  const url = ADMIN_API_ENDPOINTS.LOCATIONS.BY_ID(id)
  return adminApiClient.get<{ success: boolean; data: AdminLocationItem }>(url)
}

async function create(body: FormData | Record<string, any>) {
  const url = ADMIN_API_ENDPOINTS.LOCATIONS.BASE
  if (body instanceof FormData) {
    return adminApiClient.post<{ success: boolean; data: AdminLocationItem }>(url, body)
  }
  // Envia JSON plano si es objeto
  return adminApiClient.post<{ success: boolean; data: AdminLocationItem }>(url, body)
}

async function update(id: number | string, body: FormData | Record<string, any>) {
  const url = ADMIN_API_ENDPOINTS.LOCATIONS.BY_ID(id)
  if (body instanceof FormData) {
    return adminApiClient.post<{ success: boolean; data: AdminLocationItem }>(url, body)
  }
  // Envia JSON plano si es objeto
  return adminApiClient.put<{ success: boolean; data: AdminLocationItem }>(url, body)
}

// Imágenes globales de locaciones
async function listImages() {
  return adminApiClient.get(ADMIN_API_ENDPOINTS.LOCATION_IMAGES.BASE)
}

async function listImagesByUser(userId: string | number) {
  return adminApiClient.get(ADMIN_API_ENDPOINTS.LOCATION_IMAGES.BY_USER(userId))
}

async function deleteImage(id: number | string) {
  return adminApiClient.delete(ADMIN_API_ENDPOINTS.LOCATION_IMAGES.BY_ID(id))
}

async function setMainImage(id: number | string) {
  return adminApiClient.put(ADMIN_API_ENDPOINTS.LOCATION_IMAGES.SET_MAIN(id))
}

async function updateAlt(id: number | string, altText: string) {
  return adminApiClient.put(ADMIN_API_ENDPOINTS.LOCATION_IMAGES.BY_ID(id), { alt_text: altText })
}

async function createImage(formData: FormData) {
  return adminApiClient.post(ADMIN_API_ENDPOINTS.LOCATION_IMAGES.BASE, formData)
}

async function updateImage(id: number | string, formData: FormData) {
  return adminApiClient.post(ADMIN_API_ENDPOINTS.LOCATION_IMAGES.BY_ID(id), formData)
}

// Location Items
async function listItems() {
  return adminApiClient.get(ADMIN_API_ENDPOINTS.LOCATION_ITEMS.BASE)
}

async function listItemsByUser(userId: string | number) {
  return adminApiClient.get(ADMIN_API_ENDPOINTS.LOCATION_ITEMS.BY_USER(userId))
}

async function getItem(id: number | string) {
  return adminApiClient.get(ADMIN_API_ENDPOINTS.LOCATION_ITEMS.BY_ID(id))
}

async function createItem(body: FormData | Record<string, any>) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_ITEMS.BASE
  if (body instanceof FormData) {
    return adminApiClient.post(url, body)
  }
  return adminApiClient.post(url, body)
}

async function updateItem(id: number | string, body: FormData | Record<string, any>) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_ITEMS.BY_ID(id)
  if (body instanceof FormData) {
    return adminApiClient.post(url, body)
  }
  return adminApiClient.post(url, body)
}

async function deleteItem(id: number | string) {
  return adminApiClient.delete(ADMIN_API_ENDPOINTS.LOCATION_ITEMS.BY_ID(id))
}

export default {
  list,
  listByUser,
  get,
  create,
  update,
  remove,
  // Imágenes:
  listImages,
  listImagesByUser,
  deleteImage,
  setMainImage,
  updateAlt,
  createImage,
  updateImage,
  // Items:
  listItems,
  listItemsByUser,
  getItem,
  createItem,
  updateItem,
  deleteItem,
}
