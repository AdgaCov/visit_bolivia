// Tipos para los datos de la aplicación
export interface Place {
  id: number
  nombre: string
  descripcion: string
  region: string
  coordenadas: [number, number]
  imagenes: string[]
  informacionPractica: InformacionPractica
  destacado?: boolean
}

export interface Region {
  id: number
  nombre: string
  descripcion: string
  clima: string
  altitud: string
  lugares: number[]
}

export interface InformacionPractica {
  mejorEpoca: string
  clima: string
  precioEntrada: string
  comoLlegar: string
}

export interface City {
  id: number
  nombre: string
  descripcion: string
  region: string
  coordenadas: [number, number]
  imagen: string
  destacado?: boolean
}

export interface NatureSite {
  id: number
  nombre: string
  descripcion: string
  region: string
  coordenadas: [number, number]
  imagen: string
  destacado?: boolean
}

