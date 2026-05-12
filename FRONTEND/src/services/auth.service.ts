import apiClient from './api'
import API_ENDPOINTS from './endpoints'

export interface RegisterPayload {
  name: string
  email: string
  password: string
  role_id: number
  status: number
}

export interface GoogleLoginPayload {
  id_token: string
}

export const authService = {
  register(payload: RegisterPayload) {
    return apiClient.post(API_ENDPOINTS.AUTH.REGISTER, payload)
  },

  loginWithGoogle(payload: GoogleLoginPayload) {
    return apiClient.post(API_ENDPOINTS.AUTH.GOOGLE, payload)
  }
}

export default authService


