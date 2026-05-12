import adminApiClient from './api.admin'
import ADMIN_API_ENDPOINTS from './endpoints.admin'

export interface Plan {
  id: number
  name: string
  description: string
  price: string
  price_currency: string
  max_locations: number
  max_location_images: number
  max_location_items: number
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface PlansListResponse {
  success: boolean
  data: Plan[]
}

export interface PlanResponse {
  success: boolean
  data: Plan
}

class AdminPlansService {
  async list(): Promise<PlansListResponse> {
    return adminApiClient.get<PlansListResponse>(ADMIN_API_ENDPOINTS.PLANS.BASE)
  }

  async getActive(): Promise<PlansListResponse> {
    return adminApiClient.get<PlansListResponse>(ADMIN_API_ENDPOINTS.PLANS.ACTIVE)
  }

  async get(id: number | string): Promise<PlanResponse> {
    return adminApiClient.get<PlanResponse>(ADMIN_API_ENDPOINTS.PLANS.BY_ID(id))
  }

  async create(payload: Partial<Plan>): Promise<PlanResponse> {
    return adminApiClient.post<PlanResponse>(ADMIN_API_ENDPOINTS.PLANS.BASE, payload)
  }

  async update(id: number | string, payload: Partial<Plan>): Promise<PlanResponse> {
    return adminApiClient.put<PlanResponse>(ADMIN_API_ENDPOINTS.PLANS.BY_ID(id), payload)
  }

  async remove(id: number | string): Promise<{ success: boolean }> {
    return adminApiClient.delete(ADMIN_API_ENDPOINTS.PLANS.BY_ID(id))
  }

  async toggleActive(id: number | string): Promise<PlanResponse> {
    return adminApiClient.put<PlanResponse>(ADMIN_API_ENDPOINTS.PLANS.TOGGLE_ACTIVE(id))
  }
}

export const adminPlansService = new AdminPlansService()
export default adminPlansService

