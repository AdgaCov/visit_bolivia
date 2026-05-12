import adminApiClient from './api.admin'
import ADMIN_API_ENDPOINTS from './endpoints.admin'

export interface AdminUser {
  id: number
  name: string
  email: string
  role_id?: number
  role?: string // Para compatibilidad con el formulario
  status?: boolean
  active?: boolean // Para compatibilidad con el formulario
  plan_id?:number
  lastLogin?: string
  avatar?: string
  created_at?: string
  updated_at?: string
}

export interface AdminUsersListResponse {
  success: boolean
  data: AdminUser[]
}

export interface AdminUserResponse {
  success: boolean
  data: AdminUser
}

class AdminUsersService {
  async list(): Promise<AdminUsersListResponse> {
    // El endpoint usa GET para obtener la lista
    return adminApiClient.get<AdminUsersListResponse>(ADMIN_API_ENDPOINTS.USERS.BASE)
  }

  async get(id: number | string): Promise<AdminUserResponse> {
    return adminApiClient.get(ADMIN_API_ENDPOINTS.USERS.BY_ID(id))
  }

  async create(payload: Partial<AdminUser>): Promise<AdminUserResponse> {
    return adminApiClient.post(ADMIN_API_ENDPOINTS.USERS.BASE, payload)
  }

  async update(id: number | string, payload: FormData | Partial<AdminUser>): Promise<AdminUserResponse> {
    // El endpoint de actualización usa POST
    return adminApiClient.put<AdminUserResponse>(ADMIN_API_ENDPOINTS.USERS.UPDATE(id), payload)
  }

  async remove(id: number | string): Promise<{ success: boolean }> {
    return adminApiClient.delete(ADMIN_API_ENDPOINTS.USERS.BY_ID(id))
  }

  async toggleStatus(id: number | string): Promise<AdminUserResponse> {
    // El endpoint usa PUT para cambiar el estado
    return adminApiClient.put<AdminUserResponse>(ADMIN_API_ENDPOINTS.USERS.TOGGLE_STATUS(id))
  }
}

export const adminUsersService = new AdminUsersService()
export default adminUsersService


