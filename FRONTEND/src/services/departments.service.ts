import { apiClient } from './api'
import API_ENDPOINTS from './endpoints'
import type { DepartmentsResponseApi, DepartmentApi } from '@/types'

export async function fetchDepartments(): Promise<DepartmentApi[]> {
  const res = await apiClient.get<DepartmentsResponseApi>(API_ENDPOINTS.DEPARTMENTS.BASE)
  if (!res || res.success !== true || !Array.isArray(res.data)) return []
  return res.data
}

export default { fetchDepartments }



