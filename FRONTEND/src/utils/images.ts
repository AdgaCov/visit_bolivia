// // src/plugins/images.ts
// import { App } from 'vue'

// // ✅ Usa import.meta.env (no process.env) y el nuevo nombre VITE_API_IMG_BASE
// const RAW_BASE = import.meta.env.VITE_API_IMG_BASE || ''

// const API_IMG_BASE = String(RAW_BASE).replace(/\/$/, '')

// function buildImg(url?: string): string {
//   if (!url) return ''
//   if (/^https?:\/\//i.test(url)) return url
//   const path = String(url).replace(/^\//, '')
//   return API_IMG_BASE ? `${API_IMG_BASE}/${path}` : `/${path}`
// }

// export default {
//   install(app: App) {
//     app.config.globalProperties.$apiImgBase = API_IMG_BASE
//     app.config.globalProperties.$buildImg = buildImg
//   }
// }
import { App } from 'vue'

const RAW_BASE = (process as any)?.env?.VUE_APP_API_URL_IMG || ''
// VUE_APP_API_URL_IMG=http://localhost/api_conoce_bolivia/public
console.log("=========",RAW_BASE)

const API_IMG_BASE = String(RAW_BASE).replace(/\/$/, '')

function buildImg(url?: string) {
  if (!url) return ''
  if (/^https?:\/\//i.test(url)) return url
  const path = String(url).replace(/^\//, '')
  return API_IMG_BASE ? `${API_IMG_BASE}/${path}` : `/${path}`
}



export default {
  install(app: App) {
    app.config.globalProperties.$apiImgBase = API_IMG_BASE
    app.config.globalProperties.$buildImg = buildImg
  }
}