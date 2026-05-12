import adminApiClient from './api.admin'

export interface LoginRequest {
  email: string
  password: string
}

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
}

export interface AdminUser {
  id: number
  name: string
  email: string
  role: string | { id: number; name: string; description?: string }
  role_id?: string | number
  plan_id?: string | number
  plan?: Plan
  status: boolean
  created_at: string
}

export interface LoginResponse {
  success: boolean
  message: string
  data: {
    user: AdminUser
    token: string
  }
}

class AdminAuthService {
  async login(credentials: LoginRequest): Promise<LoginResponse> {
    return adminApiClient.post<LoginResponse>( '/api/auth/login', credentials)
  }

  async logout(): Promise<{ success: boolean }> {
    // Si el backend tiene endpoint de logout, usarlo aquí
    return Promise.resolve({ success: true })
  }

  async checkToken(token: string): Promise<{ valid: boolean; user?: AdminUser }> {
    // Si el backend tiene endpoint para verificar token, usarlo aquí
    return Promise.resolve({ valid: true })
  }
}

export const adminAuthService = new AdminAuthService()
export default adminAuthService


