import apiClient from './api'
import API_ENDPOINTS from './endpoints'

export interface Location {
  id: number
  name: string
  description: string
  latitude: string
  longitude: string
  address?: string
  opening_hours?: string
  contact_info?: string
  type: 'Internacional' | 'Local' | 'Nacional' | 'Regional'
  department_id: number
  badge?: string
  room_type?: string
  price_per_night?: string
  start_date?: string
  end_date?: string
  event_type?: string
  capacity?: number
  is_recurring?: boolean
  facebook?: string
  instagram?: string
  whatsapp?: string
  link?: string
  created_at: string
  updated_at: string
  department: {
    id: number
    name: string
    description: string
    image_url: string
    created_at: string
  }
  user: {
    id: number
    name: string
    email: string
    role: string
    status: boolean
    created_at: string
  }
  subcategories: Array<{
    id: number
    category_id: number
    name: string
    description: string
    image_url: string
    created_at: string
    updated_at: string
    pivot: {
      location_id: number
      subcategory_id: number
    }
  }>
  images: Array<{
    id: number
    location_id: number
    image_url: string
    alt_text?: string
    is_main: boolean
    uploaded_at: string
  }>
}

export interface LocationsResponse {
  success: boolean
  data: Location[]
}

export interface NearbyLocation extends Location {
  distance?: number
}

export interface NearbyLocationsResponse {
  success: boolean
  data: NearbyLocation[]
}

export interface LocationFilters {
  type?: string
  department_id?: number
  limit?: number
  offset?: number
}

// Obtener todas las ubicaciones
export const getAllLocations = async (filters?: LocationFilters): Promise<LocationsResponse> => {
  try {
    const params = new URLSearchParams()
    
    if (filters?.type) {
      params.append('type', filters.type)
    }
    if (filters?.department_id) {
      params.append('department_id', filters.department_id.toString())
    }
    if (filters?.limit) {
      params.append('limit', filters.limit.toString())
    }
    if (filters?.offset) {
      params.append('offset', filters.offset.toString())
    }

    const queryString = params.toString()
    const url = queryString ? `${API_ENDPOINTS.LOCATIONS.BASE}?${queryString}` : API_ENDPOINTS.LOCATIONS.BASE
    
    const response = await apiClient.get(url)
    return response as LocationsResponse
  } catch (error) {
    console.error('Error fetching locations:', error)
    throw error
  }
}

// Obtener ubicaciones por tipo
export const getLocationsByType = async (type: string): Promise<LocationsResponse> => {
  return getAllLocations({ type })
}

// Obtener ubicaciones cercanas
export const getNearbyLocations = async (latitude: number, longitude: number, radius?: number): Promise<NearbyLocationsResponse> => {
  try {
    const params = new URLSearchParams()
    params.append('latitude', String(latitude))
    params.append('longitude', String(longitude))
    if (radius != null) params.append('radius', String(radius))

    const url = `${API_ENDPOINTS.LOCATIONS.NEARBY}?${params.toString()}`
    console.log('📍 Locations: obteniendo cercanos', { url })

    const response = await apiClient.get(url)
    return response as NearbyLocationsResponse
  } catch (error) {
    console.error('❌ Error fetching nearby locations:', error)
    throw error
  }
}

// Obtener ubicaciones destacadas (con badge)
export const getFeaturedLocations = async (): Promise<LocationsResponse> => {
  try {
    const response = await apiClient.get(API_ENDPOINTS.LOCATIONS.FEATURED)
    return response as LocationsResponse
  } catch (error) {
    console.error('Error fetching featured locations:', error)
    throw error
  }
}

// Obtener ubicaciones con subcategorías
export const getLocationsWithSubcategories = async (): Promise<LocationsResponse> => {
  try {
    const response = await apiClient.get(API_ENDPOINTS.LOCATIONS.WITH_SUBCATEGORIES)
    return response as LocationsResponse
  } catch (error) {
    console.error('Error fetching locations with subcategories:', error)
    throw error
  }
}

// Obtener ubicación por ID
export const getLocationById = async (id: string | number): Promise<Location> => {
  try {
    console.log('🔍 Locations: Obteniendo ubicación con ID:', id)
    const response = await apiClient.get(API_ENDPOINTS.LOCATIONS.BY_ID(id))
    console.log('✅ Locations: Respuesta completa de ubicación:', response)
    
    // La API podría devolver { success: true, data: location } o directamente location
    if (response && typeof response === 'object') {
      if ('data' in response && response.data) {
        console.log('📦 Locations: Ubicación encontrada en response.data:', response.data)
        console.log('📦 Locations: Items en la ubicación:', (response.data as any).items)
        return response.data as Location
      } else if ('id' in response) {
        console.log('📦 Locations: Ubicación encontrada directamente:', response)
        console.log('📦 Locations: Items en la ubicación:', (response as any).items)
        return response as Location
      }
    }
    
    throw new Error('Formato de respuesta inesperado')
  } catch (error) {
    console.error('❌ Locations: Error obteniendo ubicación por ID:', error)
    throw error
  }
}

// Transformar ubicación para el mapa
export const transformLocationForMap = (location: Location) => {
  return {
    id: location.id,
    nombre: location.name,
    descripcion: location.description,
    coordenadas: [parseFloat(location.latitude), parseFloat(location.longitude)],
    imagenes: location.images?.map(img => img.image_url) || [],
    region: location.department.name,
    destacado: !!location.badge,
    type: location.type,
    department: location.department.name,
    address: location.address,
    contact: location.contact_info,
    opening_hours: location.opening_hours
  }
}

// Filtrar ubicaciones por tipo para las pestañas
export const filterLocationsByTab = (locations: Location[], tab: string) => {
  console.log(`🔍 Locations: Filtrando ${locations.length} ubicaciones para pestaña '${tab}'`)
  
  switch (tab) {
    case 'regions':
      // Para regiones, agrupamos por departamento y mostramos los más representativos
      const departments = new Map()
      locations.forEach(location => {
        const dept = location.department
        if (dept && !departments.has(dept.id)) {
          departments.set(dept.id, {
            id: dept.id,
            nombre: dept.name,
            descripcion: dept.description,
            coordenadas: [parseFloat(location.latitude), parseFloat(location.longitude)],
            imagenes: [dept.image_url],
            region: dept.name,
            destacado: !!location.badge,
            type: 'region',
            department: dept.name,
            locationCount: 1
          })
        } else if (dept) {
          departments.get(dept.id).locationCount++
        }
      })
      const regionsResult = Array.from(departments.values()).sort((a, b) => b.locationCount - a.locationCount)
      console.log('🏔️ Locations: Regiones filtradas:', regionsResult)
      return regionsResult
    
    case 'cities':
      const citiesFiltered = locations
      console.log('🏙️ Locations: Ciudades encontradas:', citiesFiltered.length)
      const citiesResult = citiesFiltered.map(transformLocationForMap)
      console.log('🏙️ Locations: Ciudades transformadas:', citiesResult)
      return citiesResult
    
    case 'nature':
      const natureFiltered = locations.filter(loc =>
        loc.description.toLowerCase().includes('parque') ||
         loc.description.toLowerCase().includes('naturaleza') ||
         loc.description.toLowerCase().includes('selva') ||
         loc.description.toLowerCase().includes('laguna') ||
         loc.description.toLowerCase().includes('montaña') ||
         loc.description.toLowerCase().includes('salar') ||
         loc.description.toLowerCase().includes('lago') ||
         loc.description.toLowerCase().includes('reserva') ||
         loc.description.toLowerCase().includes('nacional'))
      console.log('🌿 Locations: Sitios naturales encontrados:', natureFiltered.length)
      const natureResult = natureFiltered.map(transformLocationForMap)
      console.log('🌿 Locations: Sitios naturales transformados:', natureResult)
      return natureResult
    
    default:
      const defaultResult = locations.map(transformLocationForMap)
      console.log('📍 Locations: Resultado por defecto:', defaultResult)
      return defaultResult
  }
}

// Obtener rutas de navegación para ubicaciones
export const getLocationRoute = (location: Location) => {
  const id = Number(location.id)
  const type = String(location.type || '')
  
  console.log('🔗 Locations: Generando ruta para ubicación:', { id, type, name: location.name })
  
  const route = { name: 'AttractionDetail', params: { id } }
  
  console.log('🔗 Locations: Ruta generada:', route)
  return route
}

// Transformar ubicación a formato compatible con GastronomyPage
export const mapLocationToGastronomyLike = (location: Location) => {
  console.log('🔄 Locations: Mapeando ubicación a formato gastronomía:', location.name)
  console.log('🔄 Locations: Items originales:', (location as any).items)
  
  return {
    id: location.id,
    name: location.name,
    title: location.name,
    description: location.description,
    latitude: parseFloat(location.latitude),
    longitude: parseFloat(location.longitude),
    address: location.address,
    opening_hours: location.opening_hours,
    contact_phone: location.contact_info,
    phone: location.contact_info,
    whatsapp: location.whatsapp,
    website: location.link,
    facebook: location.facebook,
    instagram: location.instagram,
    type: location.type,
    department: location.department,
    images: location.images || [],
    subcategories: location.subcategories || [],
    badge: location.badge,
    room_type: location.room_type,
    price_per_night: location.price_per_night,
    start_date: location.start_date,
    end_date: location.end_date,
    event_type: location.event_type,
    capacity: location.capacity,
    is_recurring: location.is_recurring,
    created_at: location.created_at,
    updated_at: location.updated_at,
    // ✅ AGREGAR LOS ITEMS
    items: (location as any).items || []
  }
}
