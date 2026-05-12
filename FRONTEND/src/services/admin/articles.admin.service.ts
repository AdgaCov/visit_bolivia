import adminApiClient from './api.admin'
import ADMIN_API_ENDPOINTS from './endpoints.admin'

export interface AdminArticle {
  id: number
  id_category: number | null
  parent_id?: number | null
  title: string
  subtitle: string | null
  description: string | null
  content: string | null
  type: string | null
  author: string | null
  media_url: string | null
  template_id: number | null
  page_section: string | null
  link: string | null
  created_at?: string
  updated_at?: string
  images?: Array<{ image_url: string }>
  category?: { id: number, name: string } | null
  subcategories?: Array<{ id: number; name: string }>
}

export interface AdminArticlesListResponse {
  success: boolean
  data: AdminArticle[]
  pagination?: { limit: number; offset: number; count: number }
}

export interface AdminArticleResponse {
  success: boolean
  data: AdminArticle
}

class AdminArticlesService {
  async list(params?: Record<string, unknown>): Promise<AdminArticlesListResponse> {
    return adminApiClient.get(ADMIN_API_ENDPOINTS.ARTICLES.BASE, params)
  }

  async listByParent(parentId: number | string) {
    return adminApiClient.get<{ success: boolean; data: AdminArticle[] }>(
      ADMIN_API_ENDPOINTS.ARTICLES.BY_PARENT(parentId)
    )
  }

  async get(id: number | string): Promise<AdminArticleResponse> {
    return adminApiClient.get(ADMIN_API_ENDPOINTS.ARTICLES.BY_ID(id))
  }

  async create(payload: Partial<AdminArticle>): Promise<AdminArticleResponse> {
    return adminApiClient.post(ADMIN_API_ENDPOINTS.ARTICLES.BASE, payload)
  }

  async update(id: number | string, payload: FormData | Partial<AdminArticle>): Promise<AdminArticleResponse> {
    // El endpoint de actualización usa POST, no PUT
    return adminApiClient.post<AdminArticleResponse>(ADMIN_API_ENDPOINTS.ARTICLES.UPDATE(id), payload)
  }

  async remove(id: number | string): Promise<{ success: boolean }> {
    return adminApiClient.delete(ADMIN_API_ENDPOINTS.ARTICLES.BY_ID(id))
  }
}

export const adminArticlesService = new AdminArticlesService()
export default adminArticlesService


