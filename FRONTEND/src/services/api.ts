export interface RequestOptions extends RequestInit {
  headers?: Record<string, string>;
}

const API_BASE_URL: string = (process as any).env?.VUE_APP_API_URL

function getAuthToken(): string | null {
  const token = localStorage.getItem('adminToken')
  // Debug más detallado
  if (!token) {
    console.warn('⚠️ No se encontró adminToken en localStorage')
    console.log('📋 Contenido de localStorage:', {
      adminToken: localStorage.getItem('adminToken'),
      adminUser: localStorage.getItem('adminUser') ? 'Existe' : 'No existe',
      adminAuthenticated: localStorage.getItem('adminAuthenticated')
    })
  } else {
    console.log('✅ Token encontrado:', token.substring(0, 20) + '...')
    
    // Intentar verificar si el token está expirado (JWT)
    try {
      const parts = token.split('.')
      if (parts.length === 3) {
        const payload = JSON.parse(atob(parts[1]))
        const exp = payload.exp
        const now = Math.floor(Date.now() / 1000)
        
        if (exp && exp < now) {
          console.error('❌ Token EXPIRADO. Expiró:', new Date(exp * 1000).toLocaleString())
          console.error('⏰ Hora actual:', new Date().toLocaleString())
          console.error('⏱️ Tiempo restante:', Math.floor((exp - now) / 60), 'minutos')
          // Limpiar token expirado
          localStorage.removeItem('adminToken')
          localStorage.removeItem('adminUser')
          localStorage.removeItem('adminAuthenticated')
          return null
        } else if (exp) {
          const timeLeft = Math.floor((exp - now) / 60)
          console.log('⏰ Token válido. Expira en:', timeLeft, 'minutos')
        }
      }
    } catch (e) {
      // No es un JWT válido o no se puede parsear
      console.log('⚠️ No se pudo verificar expiración del token (puede no ser JWT)')
    }
  }
  return token
}

async function request<T>(endpoint: string, options: RequestOptions = {}): Promise<T> {
  // Permitir pasar URLs absolutas sin anteponer la base
  const isAbsolute = /^https?:\/\//i.test(endpoint)
  const url = isAbsolute ? endpoint : `${API_BASE_URL}${endpoint}`

  const isFormData = (options as any)?.body instanceof FormData
  const token = getAuthToken()
  
  // Debug: Log del token
  console.log('🔑 Token en localStorage:', token ? '✅ Existe' : '❌ No existe')
  console.log('🌐 URL de petición:', url)
  console.log('📦 Tipo de body:', isFormData ? 'FormData' : 'JSON')
  console.log('🔧 Método:', options.method || 'GET')
  
  // Construir headers base
  const baseHeaders: Record<string, string> = {}
  
  // Para FormData, NO establecer Content-Type (el navegador lo hará)
  // Para JSON, establecer Content-Type
  if (!isFormData) {
    baseHeaders['Content-Type'] = 'application/json'
  }
  
  // Agregar headers personalizados si existen
  if (options.headers) {
    Object.assign(baseHeaders, options.headers)
  }
  
  // SIEMPRE agregar el token al final para asegurar que no se sobrescriba
  if (token) {
    baseHeaders['Authorization'] = `Bearer ${token}`
    console.log('✅ Token agregado al header Authorization')
  } else {
    console.log('⚠️ No hay token para agregar al header')
  }

  // Construir config asegurando que los headers base (con token) no se sobrescriban
  // IMPORTANTE: Para FormData, debemos ser más cuidadosos con los headers
  const config: RequestInit = {
    method: options.method || 'GET',
    ...options,
    // Asegurar que nuestros headers (con token) tengan prioridad
    // Para FormData, NO incluimos Content-Type (el navegador lo hace automáticamente)
    headers: isFormData
      ? {
          // Para FormData, solo agregar headers que no sean Content-Type
          ...(Object.fromEntries(
            Object.entries(options.headers || {}).filter(([key]) => 
              key.toLowerCase() !== 'content-type'
            )
          )),
          ...baseHeaders, // Esto incluye el Authorization
        }
      : {
          ...(options.headers || {}),
          ...baseHeaders, // Para JSON, incluimos todo
        }
  }
  
  // Para FormData, eliminar explícitamente Content-Type si existe
  // El navegador debe establecerlo automáticamente con el boundary correcto
  if (isFormData && config.headers) {
    const headersObj = config.headers as Record<string, string>
    delete headersObj['Content-Type']
    delete headersObj['content-type']
  }
  
  // Verificación final: el token DEBE estar presente
  if (token && config.headers) {
    const finalHeaders = config.headers as Record<string, string>
    if (!finalHeaders['Authorization']) {
      console.error('❌ ERROR CRÍTICO: El token no está en los headers finales después de merge!')
      finalHeaders['Authorization'] = `Bearer ${token}`
    } else {
      console.log('✅ Verificación final: Token está presente en headers')
    }
  }

  // Debug detallado de headers ANTES de enviar
  const headersObj = config.headers as Record<string, string> | undefined
  console.log('📤 Headers que se enviarán:', headersObj ? Object.keys(headersObj) : [])
  console.log('📋 Detalle de headers:', {
    Authorization: headersObj?.['Authorization'] ? headersObj['Authorization'].substring(0, 50) + '...' : '❌ NO EXISTE',
    ContentType: headersObj?.['Content-Type'] || (isFormData ? 'Automático (FormData)' : 'No definido'),
    Todos: headersObj ? Object.keys(headersObj) : []
  })
  
  // Verificar que el token esté presente ANTES de enviar
  if (token && headersObj && !headersObj['Authorization']) {
    console.error('❌ ERROR CRÍTICO: El token existe pero NO está en los headers!')
    headersObj['Authorization'] = `Bearer ${token}`
    config.headers = headersObj
  }

  console.log('🚀 Enviando petición a:', url)
  console.log('🔍 Configuración final:', {
    method: config.method,
    hasBody: !!config.body,
    bodyType: config.body?.constructor?.name,
    headersCount: config.headers ? Object.keys(config.headers as Record<string, string>).length : 0,
    authorizationHeader: (config.headers as Record<string, string>)?.['Authorization'] ? '✅ Presente' : '❌ Faltante',
    headersList: config.headers ? Object.keys(config.headers as Record<string, string>) : []
  })
  
  // IMPORTANTE: Para CORS con FormData, el navegador puede hacer preflight (OPTIONS)
  // Asegurarnos de que el servidor reciba el token en la petición real
  
  try {
    const response = await fetch(url, config)
    
    console.log('📥 Respuesta recibida:', response.status, response.statusText)
    
    // Si es 401, podría ser un problema de CORS preflight
    if (response.status === 401) {
      console.warn('⚠️ 401 recibido. Esto podría ser:')
      console.warn('  1. El servidor no está leyendo el header Authorization con FormData')
      console.warn('  2. El preflight OPTIONS está fallando y el servidor rechaza la petición')
      console.warn('  3. El servidor requiere el token en otro formato o header')
      
      // Intentar ver si hay alguna petición OPTIONS previa
      console.warn('💡 SOLUCIÓN: Verificar en Network tab si hay una petición OPTIONS antes de POST')
      console.warn('💡 El servidor debe permitir Authorization en access-control-allow-headers para OPTIONS')
    }
    
    // Si es 401, verificar headers de respuesta antes de leer el body
    if (response.status === 401) {
      console.error('❌ 401 Unauthorized - El servidor rechazó la petición')
      console.log('🔍 Headers de respuesta:', Array.from(response.headers.entries()))
      
      // Si es 401, el token podría estar expirado o inválido
      // Limpiar el token y redirigir al login
      if (token) {
        console.warn('⚠️ Token posiblemente expirado o inválido. Limpiando sesión...')
        // Limpiar localStorage y recargar para ir al login
        localStorage.removeItem('adminToken')
        localStorage.removeItem('adminUser')
        localStorage.removeItem('adminAuthenticated')
        
        // Si estamos en el admin, redirigir al login
        if (window.location.pathname.startsWith('/admin')) {
          window.location.href = '/admin/login'
        }
      }
    }
    
    if (!response.ok) {
    let errorData: any = null
    try {
      // Clonar la respuesta para poder leerla sin consumirla
      const clonedResponse = response.clone()
      errorData = await clonedResponse.json()
      
      // Log del error para 401
      if (response.status === 401) {
        console.log('📄 Mensaje del servidor:', errorData?.message || errorData)
        console.log('🔑 Token actual:', token ? token.substring(0, 50) + '...' : 'No hay token')
      }
    } catch {
      // Si no se puede parsear JSON, usar el texto
      try {
        const clonedResponse = response.clone()
        const errorText = await clonedResponse.text()
        errorData = { message: errorText }
        
        // Log del error para 401
        if (response.status === 401) {
          console.log('📄 Mensaje del servidor (texto):', errorText)
          console.log('🔑 Token actual:', token ? token.substring(0, 50) + '...' : 'No hay token')
        }
      } catch {
        errorData = { message: 'Error desconocido' }
      }
    }
    const error: any = new Error(errorData?.message || `HTTP error! status: ${response.status}`)
    error.response = {
      status: response.status,
      data: errorData
    }
    throw error
    }
    
    return response.json() as Promise<T>
  } catch (error) {
    console.error('❌ Error en fetch:', error)
    throw error
  }
}

function buildQuery(params: Record<string, unknown> = {}): string {
  const search = new URLSearchParams()
  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null) return
    search.append(key, String(value))
  })
  const qs = search.toString()
  return qs ? `?${qs}` : ''
}

export const apiClient = {
  get<T>(endpoint: string, params?: Record<string, unknown>) {
    return request<T>(`${endpoint}${buildQuery(params)}`, { method: 'GET' })
  },
  post<T, B = unknown>(endpoint: string, body?: B) {
    const isForm = (body as any) instanceof FormData
    return request<T>(endpoint, { method: 'POST', body: isForm ? (body as any) : (body ? JSON.stringify(body) : undefined) })
  },
  put<T, B = unknown>(endpoint: string, body?: B) {
    const isForm = (body as any) instanceof FormData
    return request<T>(endpoint, { method: 'PUT', body: isForm ? (body as any) : (body ? JSON.stringify(body) : undefined) })
  },
  delete<T>(endpoint: string) {
    return request<T>(endpoint, { method: 'DELETE' })
  }
}

export default apiClient

