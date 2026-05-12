import apiClient from './api'
import API_ENDPOINTS from './endpoints'

export interface LocationPolicy {
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

export interface LocationPolicyResponse {
  success: boolean
  message?: string
  data: LocationPolicy | null
}

// Obtener políticas de una ubicación
export const getLocationPolicies = async (locationId: string | number): Promise<LocationPolicy | null> => {
  try {
    const response = await apiClient.get<LocationPolicyResponse>(
      API_ENDPOINTS.LOCATION_POLICIES.BY_LOCATION(locationId)
    )
    
    // Si success es false o data es null, no hay políticas
    if (!response || !response.success || !response.data) {
      return null
    }
    
    return response.data
  } catch (error) {
    console.error('Error fetching location policies:', error)
    return null
  }
}

