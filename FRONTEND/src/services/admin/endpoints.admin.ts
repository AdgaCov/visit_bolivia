/**
 * Endpoints exclusivos para el panel administrativo
 */

export const ADMIN_API_ENDPOINTS = {
  ARTICLES: {
    BASE: '/api/articles',
    BY_ID: (id: string | number) => `/api/articles/${id}`,
    UPDATE: (id: string | number) => `/api/articles/${id}/update`,
    BY_PARENT: (parentId: string | number) => `/api/articles/by-parent/${parentId}`
  },
  LOCATIONS: {
    BASE: '/api/locations',
    BY_ID: (id: string | number) => `/api/locations/${id}`,
    BY_USER: (userId: string | number) => `/api/locations/by-user/${userId}`,
    UPDATE: (id: string | number) => `/api/locations/${id}/update`
  },
  LOCATION_IMAGES: {
    BASE: '/api/location-images',
    BY_ID: (id: string | number) => `/api/location-images/${id}`,
    SET_MAIN: (id: string | number) => `/api/location-images/${id}/set-main`,
    BY_USER: (userId: string | number) => `/api/location-images/by-user/${userId}`,
  },
  LOCATION_ITEMS: {
    BASE: '/api/location-items',
    BY_ID: (id: string | number) => `/api/location-items/${id}`,
    BY_USER: (userId: string | number) => `/api/location-items/by-user/${userId}`,
  },
  LOCATION_SUBCATEGORIES: {
    BASE: '/api/location-subcategories',
    BY_LOCATION: (locationId: string | number) => `/api/location-subcategories/${locationId}`,
    BY_LOCATION_AND_SUBCATEGORY: (locationId: string | number, subcategoryId: string | number) =>
      `/api/location-subcategories/${locationId}/${subcategoryId}`,
  },
  LOCATION_REVIEWS: {
    BASE: '/api/location-reviews',
    BY_ID: (id: string | number) => `/api/location-reviews/${id}`,
    BY_LOCATION: (locationId: string | number) => `/api/location-reviews/by-location/${locationId}`
  },
  LOCATION_POLICIES: {
    BASE: '/api/location-policies',
    BY_ID: (id: string | number) => `/api/location-policies/${id}`,
    BY_LOCATION: (locationId: string | number) => `/api/location-policies/by-location/${locationId}`,
    BY_USER: (userId: string | number) => `/api/location-policies/by-user/${userId}`,
    UPDATE_OR_CREATE: (locationId: string | number) => `/api/location-policies/by-location/${locationId}/update-or-create`
  },
  CATEGORIES: {
    BASE: '/api/categories',
    BY_ID: (id: string | number) => `/api/categories/${id}`,
    UPDATE: (id: string | number) => `/api/categories/${id}/update`
  },
  SUBCATEGORIES: {
    BASE: '/api/subcategories',
    BY_ID: (id: string | number) => `/api/subcategories/${id}`,
    UPDATE: (id: string | number) => `/api/subcategories/${id}/update`
  },
  USERS: {
    BASE: '/api/users',
    BY_ID: (id: string | number) => `/api/users/${id}`,
    UPDATE: (id: string | number) => `/api/users/${id}`,
    TOGGLE_STATUS: (id: string | number) => `/api/users/${id}/toggle-status`
  },
  PLANS: {
    BASE: '/api/plans',
    BY_ID: (id: string | number) => `/api/plans/${id}`,
    ACTIVE: '/api/plans/active',
    TOGGLE_ACTIVE: (id: string | number) => `/api/plans/${id}/toggle-active`
  }
} as const

export default ADMIN_API_ENDPOINTS


