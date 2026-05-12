import adminApiClient from '@/services/admin/api.admin'
import ADMIN_API_ENDPOINTS from '@/services/admin/endpoints.admin'

export interface AdminLocationPolicy {
  id: number
  location_id: number
  reservation_recommended: boolean
  reservation_notes: string | null
  reservation_link: string | null
  cancellation_deadline_hours: number | null
  cancellation_policy: string | null
  opening_hours: string | null
  days_closed: string | null
  accepts_cash: boolean
  accepts_credit_card: boolean
  accepts_debit_card: boolean
  accepts_bank_transfer: boolean
  accepts_digital_wallet: boolean
  payment_notes: string | null
  event_duration_hours: number | null
  minimum_age: number | null
  dress_code: string | null
  created_at: string
  updated_at: string
  location?: {
    id: number
    name: string
    type: string
  }
}

export interface AdminLocationPolicyListParams {
  limit?: number
  offset?: number
  location_id?: number
}

async function list(params: AdminLocationPolicyListParams = {}) {
  const query = new URLSearchParams()
  if (params.limit != null) query.set('limit', String(params.limit))
  if (params.offset != null) query.set('offset', String(params.offset))
  if (params.location_id != null) query.set('location_id', String(params.location_id))
  const url = `${ADMIN_API_ENDPOINTS.LOCATION_POLICIES.BASE}${query.toString() ? `?${query.toString()}` : ''}`
  return adminApiClient.get<{ success: boolean; data: AdminLocationPolicy[]; pagination?: { limit: number; offset: number; count: number } }>(url)
}

async function listByUser(userId: string | number, params: AdminLocationPolicyListParams = {}) {
  const query = new URLSearchParams()
  if (params.limit != null) query.set('limit', String(params.limit))
  if (params.offset != null) query.set('offset', String(params.offset))
  if (params.location_id != null) query.set('location_id', String(params.location_id))
  const url = `${ADMIN_API_ENDPOINTS.LOCATION_POLICIES.BY_USER(userId)}${query.toString() ? `?${query.toString()}` : ''}`
  return adminApiClient.get<{ success: boolean; data: AdminLocationPolicy[]; pagination?: { limit: number; offset: number; count: number } }>(url)
}

async function get(id: number | string) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_POLICIES.BY_ID(id)
  return adminApiClient.get<{ success: boolean; data: AdminLocationPolicy }>(url)
}

async function getByLocation(locationId: number | string) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_POLICIES.BY_LOCATION(locationId)
  return adminApiClient.get<{ success: boolean; data: AdminLocationPolicy | null; message?: string }>(url)
}

async function create(body: Record<string, any>) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_POLICIES.BASE
  console.log('📤 Creando política:', { url, body })
  try {
    const response = await adminApiClient.post<{ success: boolean; data: AdminLocationPolicy; message?: string }>(url, body)
    console.log('✅ Respuesta crear:', response)
    return response
  } catch (error: any) {
    console.error('❌ Error al crear política:', error)
    console.error('Detalles:', {
      message: error?.message,
      response: error?.response,
      status: error?.response?.status,
      data: error?.response?.data
    })
    throw error
  }
}

async function update(id: number | string, body: Record<string, any>) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_POLICIES.BY_ID(id)
  console.log('📤 Actualizando política:', { url, id, body })
  try {
    const response = await adminApiClient.put<{ success: boolean; data: AdminLocationPolicy; message?: string }>(url, body)
    console.log('✅ Respuesta actualizar:', response)
    return response
  } catch (error: any) {
    console.error('❌ Error al actualizar política:', error)
    console.error('Detalles:', {
      message: error?.message,
      response: error?.response,
      status: error?.response?.status,
      data: error?.response?.data
    })
    throw error
  }
}

async function updateOrCreate(locationId: number | string, body: Record<string, any>) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_POLICIES.UPDATE_OR_CREATE(locationId)
  console.log('📤 Update or create política:', { url, locationId, body })
  try {
    const response = await adminApiClient.post<{ success: boolean; data: AdminLocationPolicy; message?: string }>(url, body)
    console.log('✅ Respuesta update-or-create:', response)
    return response
  } catch (error: any) {
    console.error('❌ Error al update-or-create política:', error)
    console.error('Detalles:', {
      message: error?.message,
      response: error?.response,
      status: error?.response?.status,
      data: error?.response?.data
    })
    throw error
  }
}

async function remove(id: number | string) {
  const url = ADMIN_API_ENDPOINTS.LOCATION_POLICIES.BY_ID(id)
  console.log('📤 Eliminando política:', { url, id })
  try {
    const response = await adminApiClient.delete<{ success: boolean; message?: string }>(url)
    console.log('✅ Respuesta eliminar:', response)
    return response
  } catch (error: any) {
    console.error('❌ Error al eliminar política:', error)
    console.error('Detalles:', {
      message: error?.message,
      response: error?.response,
      status: error?.response?.status,
      data: error?.response?.data
    })
    throw error
  }
}

export const adminLocationPoliciesService = {
  list,
  listByUser,
  get,
  getByLocation,
  create,
  update,
  updateOrCreate,
  remove
}

export default adminLocationPoliciesService

