// Admin API client wrapper (separado del cliente público)
// Si en el futuro el admin usa otra base URL o headers, modifícalo aquí.

import apiClient from '@/services/api'

export const adminApiClient = apiClient

export default adminApiClient


