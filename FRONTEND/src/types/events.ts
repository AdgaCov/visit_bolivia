export interface EventImage {
  id: number;
  event_id: number;
  image_url: string;
  alt_text: string;
  is_main: boolean;
  uploaded_at: string;
}

export interface EventPlace {
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

export interface EventItem {
  id: number;
  place_id: number;
  name: string;
  description: string;
  start_date: string;
  end_date: string;
  event_type: string;
  price: string;
  is_recurring: boolean;
  latitude: string;
  longitude: string;
  address?: string | null;
  department?: LocationDepartment;
  opening_hours?: string | null;
  contact_info?: string | null;
  created_at: string;
  updated_at: string;
  place: EventPlace;
  images: EventImage[];
}

// Tipos compatibles con la nueva API de locations/by-type/event
export interface LocationDepartment {
  id: number;
  name: string;
  description?: string;
  created_at?: string;
}

export interface LocationEventImage {
  id: number;
  location_id: number;
  image_url: string;
  alt_text: string;
  is_main: boolean;
  uploaded_at: string;
}

export interface LocationEventItem {
  id: number;
  user_id: number;
  name: string;
  description: string;
  latitude: string;
  longitude: string;
  address: string | null;
  opening_hours: string | null;
  contact_info: string | null;
  type: string;
  department_id: number | null;
  badge: string | null;
  room_type: string | null;
  price_per_night: string | null;
  start_date: string;
  end_date: string;
  event_type: string;
  capacity: number | null;
  is_recurring: boolean;
  facebook: string | null;
  instagram: string | null;
  whatsapp: string | null;
  link: string | null;
  created_at: string;
  updated_at: string;
  department?: LocationDepartment;
  user?: unknown;
  subcategories?: unknown[];
  images: LocationEventImage[];
}

// Interfaz para compatibilidad con el componente existente
export interface EventCardData {
  id: number;
  title: string;
  date: string;
  location: string;
  image: string;
  description: string;
  to?: {
    name: string;
    params: {
      id: number;
    };
  };
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

export interface EventFilters {
  q?: string;
  region?: string;
  fromDate?: string; // ISO date
  toDate?: string;   // ISO date
  limit?: number;
  page?: number;
}
