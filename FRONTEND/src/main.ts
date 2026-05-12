import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import pinia from './store'

import installBootstrap from './plugins/bootstrap'
import installLeaflet from './plugins/leaflet'
import installFontAwesome from './plugins/fontawesome'
import ImagesPlugin from './utils/images'

const app = createApp(App)

// Instalar plugins
installBootstrap()
installLeaflet()
installFontAwesome()

// Usar Pinia store
app.use(pinia)

// Usar router
app.use(router)

// Plugin de imágenes (base desde .env y helper $buildImg)
app.use(ImagesPlugin)

// Montar la aplicación
app.mount('#app') 