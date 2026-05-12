// Re-exportar tipos desde data.ts
export * from './data'

// Tipos adicionales específicos de la aplicación
export interface AccommodationExperience {
  id: number
  title: string
  description: string
  location: string
  price: string
  image: string
  badge: string
  link: string
}

export interface PopularDestination {
  id: number
  nombre: string
  ubicacion: string
  descripcion: string
  image: string
  tags: string[]
}

export interface Experience {
  id: number
  titulo: string
  descripcion: string
  region: string
  icon: string
} 

// API Departments
export interface DepartmentCityApi {
  id: number
  department_id: number
  name: string
  latitude: string
  longitude: string
  description: string
  created_at: string
}

export interface DepartmentApi {
  id: number
  name: string
  description: string
  created_at: string
  cities: DepartmentCityApi[]
}

export interface DepartmentsResponseApi {
  success: boolean
  data: DepartmentApi[]
}

// API Cities
export interface CityImageApi {
  id: number
  city_id: number
  image_url: string
  uploaded_at: string
}

export interface PlaceApi {
  id: number
  users_id: number
  name: string
  description: string
  latitude: string
  longitude: string
  address: string
  opening_hours: string
  contact_info: string
  created_at: string
  images_places?: PlaceImageApi[]
}

export interface CityApi {
  id: number
  department_id: number
  name: string
  latitude: string
  longitude: string
  description: string
  created_at: string
  department: DepartmentApi
  places: PlaceApi[]
  images_cities: CityImageApi[]
}

export interface CitiesResponseApi {
  success: boolean
  data: CityApi[]
}

export interface PlaceImageApi {
  id: number
  place_id: number
  image_url: string
  uploaded_at: string
}

// API Accommodations
export interface AccommodationImageApi {
  id: number
  accommodation_id: number
  image_url: string
  is_main: boolean
}

export interface AccommodationApi {
  id: number
  place_id: number
  title: string
  description: string
  location: string
  room_type: string
  price_per_night: string
  capacity: number
  latitude: string
  longitude: string
  badge: string
  link: string
  facebook: string
  instagram: string
  whatsapp: string
  website?: string | null
  place: PlaceApi
  images: AccommodationImageApi[]
}

export interface AccommodationsResponseApi {
  success: boolean
  data: AccommodationApi[]
}
