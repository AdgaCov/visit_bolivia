export interface GastronomyImage {
  id: number;
  restaurant_id: number;
  image_url: string;
  alt_text: string;
  is_main: boolean;
  uploaded_at: string;
}

export interface GastronomyPlace {
  id: number;
  users_id: number;
  name: string;
  description: string;
  latitude: string;
  longitude: string;
  address: string;
  opening_hours: string;
  contact_info: string;
  created_at: string;
}

export interface GastronomyItem {
  id: number;
  place_id: number;
  name: string;
  description: string;
  cuisine_type: string;
  average_price: string;
  specialty: string;
  weekdays_hours: string;
  weekend_hours: string;
  latitude: string;
  longitude: string;
  created_at: string;
  updated_at: string;
  place: GastronomyPlace;
  images: GastronomyImage[];
}

// Interfaz para compatibilidad con el componente existente
export interface RestaurantCardData {
  id: number;
  title: string;
  cuisine: string;
  location: string;
  image: string;
  description: string;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
}

export interface PaginatedResponse<T> {
  items: T[];
  total: number;
  page: number;
  pageSize: number;
}

export interface GastronomyFilters {
  q?: string;
  region?: string;
  cuisine_type?: string;
  price_range?: string;
  limit?: number;
  page?: number;
}


