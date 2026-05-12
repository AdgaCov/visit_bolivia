<template>
  <div class="main-layout">
    <AppHeader />
    
    <main class="main-content">
      <router-view />
    </main>
    
    <AppFooter />
    <ScrollToTop />
  </div>
</template>

<script>
import AppHeader from '@/components/AppHeader.vue'
import AppFooter from '@/components/AppFooter.vue'
import ScrollToTop from '@/components/ScrollToTop.vue'

export default {
  name: 'MainLayout',
  components: {
    AppHeader,
    AppFooter,
    ScrollToTop
  }
}
</script>

<style scoped>
.main-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--bg-primary);
}

.main-content {
  flex: 1;
  padding-top: 120px; /* Espacio para el navbar fijo */
}

/* Responsive */
@media (max-width: 768px) {
  .main-content {
    padding-top: 100px;
  }
}
</style> 