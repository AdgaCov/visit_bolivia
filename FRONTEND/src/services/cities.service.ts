import { apiClient } from './api'
import API_ENDPOINTS from './endpoints'
import type { CitiesResponseApi, CityApi } from '@/types'

export async function fetchCities(): Promise<CityApi[]> {
  const res = await apiClient.get<CitiesResponseApi>(API_ENDPOINTS.CITIES.BASE)
  if (!res || res.success !== true || !Array.isArray(res.data)) return []
  return res.data
}

export default { fetchCities }




