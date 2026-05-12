import apiClient from './api'
import API_ENDPOINTS from './endpoints'

export interface Article {
  id: number
  title: string
  subtitle: string
  description: string
  content: string | null
  type: string
  author: string | null
  media_url: string | null
  template_id: number | string  // La API puede devolver como string o número
  page_section: string
  link: string | null
  created_at: string
  updated_at: string
  images: any[]
  subcategories: any[]
}

export interface ArticlesResponse {
  success: boolean
  data: Article[]
}

class ArticlesService {
  /**
   * Obtiene todos los artículos de una sección específica de página
   * @param pageSection - Sección de la página (home, whattodo, wherego, etc.)
   * @returns Promise con la respuesta de artículos
   */
  async getArticlesByPageSection(pageSection: string): Promise<ArticlesResponse> {
    try {
      const response = await apiClient.get(API_ENDPOINTS.ARTICLES.BY_PAGE_SECTION(pageSection))
      console.log('API Response:', response)
      return response as ArticlesResponse
    } catch (error) {
      console.error(`Error fetching articles for page section ${pageSection}:`, error)
      throw error
    }
  }

  /**
   * Obtiene artículos de la página home
   * @returns Promise con los artículos de home
   */
  async getHomeArticles(): Promise<ArticlesResponse> {
    return this.getArticlesByPageSection('home')
  }

  /**
   * Obtiene artículos de la página whattodo
   * @returns Promise con los artículos de whattodo
   */
  async getWhatToDoArticles(): Promise<ArticlesResponse> {
    return this.getArticlesByPageSection('whattodo')
  }

  /**
   * Obtiene artículos de la página wherego
   * @returns Promise con los artículos de wherego
   */
  async getWhereToGoArticles(): Promise<ArticlesResponse> {
    return this.getArticlesByPageSection('where_to_go')
  }

  /**
   * Obtiene artículos de la página planning
   * @returns Promise con los artículos de planning
   */
  async getPlanningArticles(): Promise<ArticlesResponse> {
    return this.getArticlesByPageSection('planning')
  }

  /**
   * Obtiene artículos de inspiración para la página home
   * @returns Promise con los artículos de home_inspiration
   */
  async getHomeInspirationArticles(): Promise<ArticlesResponse> {
    return this.getArticlesByPageSection('home_inspiration')
  }

  /**
   * Transforma un artículo al formato de InterestCard
   * @param article - Artículo a transformar
   * @returns Objeto compatible con InterestCard
   */
  transformArticleToInterestCard(article: Article) {
    return {
      title: article.title,
      subtitle: article.subtitle,
      description: article.description,
      cover: article.media_url || '/images/placeholder.jpg',
      badge: this.getTemplateLabel(article.template_id),
      to: article.link ? { path: article.link } : null,
      icon: this.getTemplateIcon(article.template_id)
    }
  }

  /**
   * Obtiene la etiqueta para el template_id
   * @param templateId - ID del template (puede ser string o number)
   * @returns Etiqueta descriptiva
   */
  getTemplateLabel(templateId: number | string): string {
    // Convertir a número si es string
    const id = typeof templateId === 'string' ? parseInt(templateId, 10) : templateId
    const templateLabels: { [key: number]: string } = {
      1: 'Historia',
      2: 'Festival',
      3: 'Gastronomía',
      4: 'Naturaleza',
      5: 'Gastronomía',
      6: 'Cultura',
      7: 'Subcultura',
      20: 'Hero'
    }
    return templateLabels[id] || 'Artículo'
  }

  /**
   * Obtiene el icono para el template_id
   * @param templateId - ID del template (puede ser string o number)
   * @returns Clase de icono FontAwesome
   */
  getTemplateIcon(templateId: number | string): string {
    // Convertir a número si es string
    const id = typeof templateId === 'string' ? parseInt(templateId, 10) : templateId
    const templateIcons: { [key: number]: string } = {
      1: 'fas fa-landmark',
      2: 'fas fa-music',
      3: 'fas fa-utensils',
      4: 'fas fa-mountain',
      5: 'fas fa-utensils',
      6: 'fas fa-palette',
      7: 'fas fa-users',
      20: 'fas fa-home'
    }
    return templateIcons[id] || 'fas fa-file-alt'
  }

  /**
   * Filtra artículos por template_id
   * @param articles - Array de artículos
   * @param templateId - ID del template a filtrar
   * @returns Array de artículos filtrados
   */
  filterByTemplate(articles: Article[], templateId: number): Article[] {
    // La API puede devolver template_id como string o número, así que comparamos ambos
    return articles.filter(article => {
      const articleTemplateId = typeof article.template_id === 'string' 
        ? parseInt(article.template_id, 10) 
        : article.template_id
      return articleTemplateId === templateId
    })
  }

  /**
   * Obtiene el primer artículo de un template específico
   * @param articles - Array de artículos
   * @param templateId - ID del template
   * @returns Primer artículo encontrado o null
   */
  getFirstByTemplate(articles: Article[], templateId: number): Article | null {
    const filtered = this.filterByTemplate(articles, templateId)
    return filtered.length > 0 ? filtered[0] : null
  }
}

export const articlesService = new ArticlesService()
export default articlesService
