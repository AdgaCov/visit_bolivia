/**
 * Logger condicional que solo muestra logs en desarrollo
 * En producción, los logs se eliminan automáticamente por el bundler
 */

const isDevelopment = process.env.NODE_ENV === 'development'

export const logger = {
  /**
   * Log de información (solo en desarrollo)
   */
  log: (...args: any[]) => {
    if (isDevelopment) {
      console.log(...args)
    }
  },

  /**
   * Log de advertencias (siempre visible, pero menos verboso en producción)
   */
  warn: (...args: any[]) => {
    if (isDevelopment) {
      console.warn(...args)
    } else {
      // En producción, solo loguear errores críticos
      console.warn('[WARN]', ...args)
    }
  },

  /**
   * Log de errores (siempre visible)
   */
  error: (...args: any[]) => {
    console.error(...args)
  },

  /**
   * Log de información de debug (solo en desarrollo)
   */
  debug: (...args: any[]) => {
    if (isDevelopment) {
      console.debug(...args)
    }
  },

  /**
   * Log de información de API (solo en desarrollo)
   */
  api: (message: string, data?: any) => {
    if (isDevelopment) {
      console.log(`[API] ${message}`, data || '')
    }
  },

  /**
   * Log de información de autenticación (solo en desarrollo)
   */
  auth: (message: string, data?: any) => {
    if (isDevelopment) {
      console.log(`[AUTH] ${message}`, data || '')
    }
  }
}

export default logger

