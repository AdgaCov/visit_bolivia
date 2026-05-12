export interface AppError {
  message: string
  code?: string
  status?: number
  timestamp: Date
}

export class ErrorHandler {
  private static instance: ErrorHandler
  private errors: AppError[] = []

  static getInstance(): ErrorHandler {
    if (!ErrorHandler.instance) {
      ErrorHandler.instance = new ErrorHandler()
    }
    return ErrorHandler.instance
  }

  handle(error: any, context?: string): AppError {
    const appError: AppError = {
      message: this.getErrorMessage(error),
      code: error.code || 'UNKNOWN_ERROR',
      status: error.status || 500,
      timestamp: new Date()
    }

    // Log para desarrollo
    if (process.env.NODE_ENV === 'development') {
      console.error(`[${context || 'Global'}] Error:`, appError, error)
    }

    // Agregar a la lista de errores
    this.errors.push(appError)

    // Mantener solo los últimos 50 errores
    if (this.errors.length > 50) {
      this.errors = this.errors.slice(-50)
    }

    return appError
  }

  private getErrorMessage(error: any): string {
    if (typeof error === 'string') return error
    if (error?.message) return error.message
    if (error?.response?.data?.message) return error.response.data.message
    return 'Ha ocurrido un error inesperado'
  }

  getErrors(): AppError[] {
    return [...this.errors]
  }

  clearErrors(): void {
    this.errors = []
  }

  getLastError(): AppError | null {
    return this.errors.length > 0 ? this.errors[this.errors.length - 1] : null
  }
}

// Instancia global
export const errorHandler = ErrorHandler.getInstance()

// Función helper para usar en componentes
export function handleError(error: any, context?: string): AppError {
  return errorHandler.handle(error, context)
}
