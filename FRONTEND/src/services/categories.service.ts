import apiClient from './api'
import API_ENDPOINTS from './endpoints'

export interface Subcategory {
  id: number
  category_id: number
  name: string
}

export interface ArticleImage {
  id: number
  article_id: number
  image_url: string
  alt_text: string
  is_main: number
  uploaded_at: string
}

export interface ArticleSubcategory extends Subcategory {
  pivot?: {
    article_id: number
    subcategory_id: number
  }
}

export interface Article {
  id: number
  parent_id?: number | null
  id_category?: number | null
  title: string
  subtitle?: string
  description?: string
  content?: string
  type?: string
  author?: string
  media_url?: string
  template_id?: number
  link?: string | null
  created_at?: string
  updated_at?: string
  images?: ArticleImage[]
  subcategories?: ArticleSubcategory[]
  category?: {
    id: number
    name: string
    description: string
    image_url: string
  } | null
  parent?: Article | null
  children?: Article[]
}

export interface Category {
  id: number
  name: string
  description: string
  image_url: string
  subcategories: Subcategory[]
}

export interface CategoriesResponse {
  success: boolean
  data: Category[]
}

export const categoriesService = {
  async getAllCategories(): Promise<CategoriesResponse> {
    return apiClient.get<CategoriesResponse>(API_ENDPOINTS.CATEGORIES.ALL)
  },

  async getCategoryById(id: number): Promise<{ success: boolean; data: Category }> {
    return apiClient.get<{ success: boolean; data: Category }>(API_ENDPOINTS.CATEGORIES.BY_ID(id))
  },

  async getCategoryArticles(categoryId: number): Promise<{ success: boolean; data: Article[] }> {
    // Traer artículos por CATEGORÍA (endpoint correcto)
    return apiClient.get<{ success: boolean; data: Article[] }>(API_ENDPOINTS.ARTICLES.BY_CATEGORY(categoryId))
  },

  async getSubcategoryArticles(subcategoryId: number): Promise<{ success: boolean; data: Article[] }> {
    return apiClient.get<{ success: boolean; data: Article[] }>(API_ENDPOINTS.SUBCATEGORIES.ARTICLES(subcategoryId))
  },

  async getSpecificArticle(articleId: number): Promise<{ success: boolean; data: Article }> {
    return apiClient.get<{ success: boolean; data: Article }>(`/api/articles/${articleId}`)
  },

  async getArticlesByParent(parentId: number): Promise<{ success: boolean; data: Article[] }> {
    return apiClient.get<{ success: boolean; data: Article[] }>(API_ENDPOINTS.ARTICLES.BY_PARENT(parentId))
  },

  // Función helper para transformar Category a formato compatible con InterestCard
  transformToInterestCard(category: Category) {
    return {
      id: category.id,
      name: category.name,
      description: category.description,
      image: category.image_url,
      icon: this.getCategoryIcon(category.name),
      color: this.getCategoryColor(category.name),
      subcategories: category.subcategories
    }
  },

  // Mapeo de iconos por categoría
  getCategoryIcon(categoryName: string): string {
    const iconMap: Record<string, string> = {
      'Cultura': 'fas fa-theater-masks',
      'Naturaleza': 'fas fa-leaf',
      'Aventura': 'fas fa-mountain',
      'Gastronomía': 'fas fa-utensils',
      'Historia': 'fas fa-landmark',
      'Religión y Espiritualidad': 'fas fa-pray',
      'Artesanía y Compras': 'fas fa-shopping-bag',
      'Vida Nocturna y Entretenimiento': 'fas fa-moon'
    }
    return iconMap[categoryName] || 'fas fa-star'
  },

  // Mapeo de colores por categoría
  getCategoryColor(categoryName: string): string {
    const colorMap: Record<string, string> = {
      'Cultura': '#8B5CF6',
      'Naturaleza': '#10B981',
      'Aventura': '#F59E0B',
      'Gastronomía': '#EF4444',
      'Historia': '#6B7280',
      'Religión y Espiritualidad': '#3B82F6',
      'Artesanía y Compras': '#EC4899',
      'Vida Nocturna y Entretenimiento': '#6366F1'
    }
    return colorMap[categoryName] || '#6B7280'
  },

  // Función helper para generar slug de categoría
  getCategorySlug(categoryName: string): string {
    return categoryName
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '') // Remover acentos
      .replace(/[^a-z0-9\s-]/g, '') // Solo letras, números, espacios y guiones
      .replace(/\s+/g, '-') // Reemplazar espacios con guiones
      .replace(/-+/g, '-') // Múltiples guiones por uno solo
      .trim()
  }
}

export default categoriesService
