declare module '@/assets/data/lugares.js' {
  import type { Place, Region } from '@/types'
  export const lugaresTuristicos: Place[]
  export const regiones: Region[]
}

declare module './router' {
  import type { Router } from 'vue-router'
  const router: Router
  export default router
} 