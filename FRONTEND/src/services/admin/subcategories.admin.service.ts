import adminApiClient from './api.admin'
import ADMIN_API_ENDPOINTS from './endpoints.admin'

export interface AdminSubcategory {
  id: number
  category_id: number
  name: string
  description: string
  image_url: string
  created_at?: string
  updated_at?: string
  category?: {
    id: number
    name: string
    description: string
    image_url: string
  }
}

export interface AdminSubcategoriesListResponse {
  success: boolean
  data: AdminSubcategory[]
}

export interface AdminSubcategoryResponse {
  success: boolean
  data: AdminSubcategory
}

class AdminSubcategoriesService {
  async list(): Promise<AdminSubcategoriesListResponse> {
    // El endpoint usa GET para obtener la lista
    return adminApiClient.get<AdminSubcategoriesListResponse>(ADMIN_API_ENDPOINTS.SUBCATEGORIES.BASE)
  }

  async get(id: number | string): Promise<AdminSubcategoryResponse> {
    return adminApiClient.get(ADMIN_API_ENDPOINTS.SUBCATEGORIES.BY_ID(id))
  }

  async create(payload: Partial<AdminSubcategory>): Promise<AdminSubcategoryResponse> {
    return adminApiClient.post(ADMIN_API_ENDPOINTS.SUBCATEGORIES.BASE, payload)
  }

  async update(id: number | string, payload: FormData | Partial<AdminSubcategory>): Promise<AdminSubcategoryResponse> {
    // El endpoint de actualización usa POST
    return adminApiClient.post<AdminSubcategoryResponse>(ADMIN_API_ENDPOINTS.SUBCATEGORIES.UPDATE(id), payload)
  }

  async remove(id: number | string): Promise<{ success: boolean }> {
    return adminApiClient.delete(ADMIN_API_ENDPOINTS.SUBCATEGORIES.BY_ID(id))
  }
}

export const adminSubcategoriesService = new AdminSubcategoriesService()
export default adminSubcategoriesService


