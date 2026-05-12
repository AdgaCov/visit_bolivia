<template>
  <div id="app">
    <!-- Barra de progreso de navegación -->
    <NavigationProgress />
    
    <!-- Solo mostrar header y footer en rutas públicas (no admin) -->
    <template v-if="!isAdminRoute">
      <AppHeader />
      <main>
        <router-view :key="$route.fullPath" />
      </main>
      <AppFooter />
      <ScrollToTop />
    </template>
    
    <!-- Para rutas admin, solo mostrar el router-view (AdminLayout se encarga del resto) -->
    <template v-else>
      <router-view :key="$route.fullPath" />
    </template>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import AppFooter from '@/components/AppFooter.vue'
import ScrollToTop from '@/components/ScrollToTop.vue'
import NavigationProgress from '@/components/NavigationProgress.vue'

export default {
  name: 'App',
  components: {
    AppHeader,
    AppFooter,
    ScrollToTop,
    NavigationProgress
  },
  setup() {
    const route = useRoute()
    
    // Detectar si estamos en una ruta administrativa
    const isAdminRoute = computed(() => {
      return route.path.startsWith('/admin')
    })
    
    return {
      isAdminRoute
    }
  }
}
</script>

<style>
@import '@/assets/styles/colors.css';
@import '@/assets/styles/fonts.css';

#app {
  font-family: var(--font-family-base);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: var(--text-primary);
}

body {
  margin: 0;
  padding: 0;
  background-color: var(--bg-primary);
}

.btn {
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
}

.card {
  border-radius: 12px;
  overflow: hidden;
  background-color: var(--card-bg);
  box-shadow: 0 2px 10px var(--shadow-color);
}

.badge {
  border-radius: 6px;
}

/* Estilos globales usando variables CSS */
.text-primary { color: var(--primary-color) !important; }
.text-secondary { color: var(--secondary-color) !important; }
.text-success { color: var(--success-color) !important; }
.text-info { color: var(--info-color) !important; }
.text-warning { color: var(--warning-color) !important; }
.text-danger { color: var(--danger-color) !important; }

.bg-primary { background-color: var(--primary-color) !important; }
.bg-secondary { background-color: var(--secondary-color) !important; }
.bg-success { background-color: var(--success-color) !important; }
.bg-info { background-color: var(--info-color) !important; }
.bg-warning { background-color: var(--warning-color) !important; }
.bg-danger { background-color: var(--danger-color) !important; }
</style>
