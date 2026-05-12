import { defineStore } from 'pinia'
import adminAuthService, { type AdminUser } from '@/services/admin/auth.admin.service'

interface AuthState {
  user: AdminUser | null
  token: string | null
  isAuthenticated: boolean
  loading: boolean
  error: string | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isAuthenticated: false,
    loading: false,
    error: null
  }),

  getters: {
    // Verificar si el usuario está autenticado
    authenticated: (state: AuthState) => state.isAuthenticated,
    
    // Obtener información del usuario
    currentUser: (state: AuthState) => state.user,
    
    // Verificar si es admin
    isAdmin: (state: AuthState) => {
      const roleValue = state.user?.role
      const role = typeof roleValue === 'string' ? roleValue : roleValue?.name
      const roleId = state.user?.role_id
      return role === 'admin' || role === 'super_admin' || roleId === 1 || roleId === '1'
    }
  },

  actions: {
    // Iniciar sesión
    async login(email: string, password: string): Promise<boolean> {
      this.loading = true
      this.error = null
      
      try {
        const response = await adminAuthService.login({ email, password })
        
        if (response.success && response.data) {
          this.user = response.data.user
          this.token = response.data.token
          this.isAuthenticated = true
          
          // Guardar en localStorage
          console.log('💾 Guardando token en localStorage...')
          console.log('📝 Token recibido:', response.data.token ? response.data.token.substring(0, 20) + '...' : 'NULL/VACÍO')
          localStorage.setItem('adminUser', JSON.stringify(response.data.user))
          localStorage.setItem('adminToken', response.data.token)
          localStorage.setItem('adminAuthenticated', 'true')
          
          // Verificar que se guardó correctamente
          const savedToken = localStorage.getItem('adminToken')
          console.log('✅ Token guardado en localStorage:', savedToken ? savedToken.substring(0, 20) + '...' : 'NO SE GUARDÓ')
          
          return true
        } else {
          this.error = response.message || 'Usuario o contraseña incorrectos'
          return false
        }
      } catch (err: any) {
        const errorMsg = err?.response?.data?.message || err?.message || 'Error al iniciar sesión'
        this.error = errorMsg
        console.error('Login error:', err)
        return false
      } finally {
        this.loading = false
      }
    },

    // Cerrar sesión
    async logout() {
      try {
        await adminAuthService.logout()
      } catch (err) {
        console.error('Logout error:', err)
      } finally {
      this.user = null
        this.token = null
      this.isAuthenticated = false
      this.error = null
      
      // Limpiar localStorage
      localStorage.removeItem('adminUser')
        localStorage.removeItem('adminToken')
      localStorage.removeItem('adminAuthenticated')
      }
    },

    // Verificar autenticación al cargar la app
    checkAuth() {
      const storedUser = localStorage.getItem('adminUser')
      const storedToken = localStorage.getItem('adminToken')
      const isAuth = localStorage.getItem('adminAuthenticated') === 'true'
      
      if (storedUser && storedToken && isAuth) {
        try {
          this.user = JSON.parse(storedUser)
          this.token = storedToken
          this.isAuthenticated = true
          // TODO: Verificar token con el backend si es necesario
        } catch (err) {
          console.error('Error parsing stored user:', err)
          this.logout()
        }
      }
    },

    // Limpiar error
    clearError() {
      this.error = null
    }
  }
})
