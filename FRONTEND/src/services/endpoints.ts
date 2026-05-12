/**
 * Centralized API endpoints configuration
 * All API URLs should be defined here to avoid hardcoding throughout the application
 */

export const API_ENDPOINTS = {
  // Base URLs
  BASE: '',
  
  // Locations
  LOCATIONS: {
    BASE: '/api/locations',
    BY_ID: (id: string | number) => `/api/locations/${id}`,
    BY_TYPE: (type: string) => `/api/locations/by-type/${type}`,
    BY_TYPE_WITH_REVIEWS: (type: string) => `/api/locations/by-type-with-reviews/${type}`,
    FEATURED: '/api/locations/featured',
    WITH_SUBCATEGORIES: '/api/locations/with-subcategories',
    ARTICLES: (id: string | number) => `/api/locations/${id}/articles`,
    NEARBY: '/api/locations/nearby'
  },

  // Categories
  CATEGORIES: {
    BASE: '/api/categories',
    BY_ID: (id: string | number) => `/api/categories/${id}`,
    ARTICLES: (id: string | number) => `/api/categories/${id}/articles`,
    LOCATIONS: (id: string | number) => `/api/categories/${id}/locations`,
    ALL: '/api/categories'
  },

  // Articles
  ARTICLES: {
    BASE: '/api/articles',
    BY_PAGE_SECTION: (section: string) => `/api/articles/by-page-section/${section}`,
    BY_CATEGORY: (id: string | number) => `/api/articles/by-category/${id}`,
    BY_PARENT: (id: string | number) => `/api/articles/by-parent/${id}`
  },

  // Subcategories
  SUBCATEGORIES: {
    ARTICLES: (id: string | number) => `/api/subcategories/${id}/articles`
  },

  // Departments
  DEPARTMENTS: {
    BASE: '/api/departments'
  },

  // Cities
  CITIES: {
    BASE: '/api/cities'
  },

  // Events
  EVENTS: {
    BASE: '/api/events',
    LOCATION_EVENTS: '/api/locations/events'
  },

  // Gastronomy
  GASTRONOMY: {
    BASE: '/api/gastronomy',
    BY_ID: (id: string | number) => `/api/gastronomy/${id}`
  },

  // Accommodations
  ACCOMMODATIONS: {
    BASE: '/api/accommodations',
    BY_ID: (id: string | number) => `/api/accommodations/${id}`
  },

  // Search
  SEARCH: {
    SUGGESTIONS: '/api/search/suggestions'
  },

  // Maps
  MAPS: {
    BASE: '/api/maps'
  },

  // Favorites
  FAVORITES: {
    BASE: '/api/favorites',
    ALL: '/api/favorites/all'
  },

  // Location Reviews
  LOCATION_REVIEWS: {
    BASE: '/api/location-reviews',
    BY_ID: (id: string | number) => `/api/location-reviews/${id}`,
    BY_LOCATION: (locationId: string | number) => `/api/location-reviews/by-location/${locationId}`
  },

  // Location Policies
  LOCATION_POLICIES: {
    BY_LOCATION: (locationId: string | number) => `/api/location-policies/by-location/${locationId}`
  },

  // Auth
  AUTH: {
    REGISTER: '/api/auth/register',
    GOOGLE: '/api/auth/google'
  }
} as const

export default API_ENDPOINTS

