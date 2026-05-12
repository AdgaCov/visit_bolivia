<template>
  <div class="admin-layout">
    <AdminSidebar />
    <div class="admin-content">
      <!-- Header administrativo -->
      <header class="admin-header">
        <div class="admin-header-content">
          <div class="admin-header-left">
            <button class="mobile-menu-toggle d-lg-none" @click="toggleSidebar">
              <i class="fas fa-bars"></i>
            </button>
            <div class="admin-breadcrumb">
              <i class="fas fa-shield-alt text-primary me-2"></i>
              <span class="breadcrumb-text">Panel Administrativo</span>
              <i class="fas fa-chevron-right mx-2 text-muted"></i>
              <span class="current-page">{{ currentPageTitle }}</span>
            </div>
          </div>
          <div class="admin-header-right">
            <div class="admin-user-info">
              <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
              </div>
              <div class="user-details">
                <span class="user-name">Administrador</span>
                <small class="user-role">Super Admin</small>
              </div>
              <div class="user-actions">
                <button class="btn-logout" @click="logout" title="Cerrar Sesión">
                  <i class="fas fa-sign-out-alt"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Contenido principal -->
      <main class="admin-main">
        <router-view />
      </main>
    </div>
    <ScrollToTop />
  </div>
</template>

<script>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import AdminSidebar from '@/components/AdminSidebar.vue'
import ScrollToTop from '@/components/ScrollToTop.vue'

export default {
  name: 'AdminLayout',
  components: { AdminSidebar, ScrollToTop },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const authStore = useAuthStore()
    
    const currentPageTitle = computed(() => {
      const routeNames = {
        'AdminDashboard': 'Dashboard',
        'AdminPlaces': 'Lugares Turísticos',
        'AdminBusiness': 'Empresas',
        'AdminBusinessNewHotel': 'Nuevo Hotel',
        'AdminBusinessNewRestaurant': 'Nuevo Restaurante'
      }
      return routeNames[route.name] || 'Panel'
    })
    
    const toggleSidebar = () => {
      // Implementar toggle del sidebar en móvil
      const sidebar = document.querySelector('.admin-sidebar')
      if (sidebar) {
        sidebar.classList.toggle('show-mobile')
      }
    }
    
    const logout = () => {
      authStore.logout()
      router.push('/admin/login')
    }
    
    return {
      currentPageTitle,
      toggleSidebar,
      logout
    }
  }
}
</script>

<style scoped>
.admin-layout { 
  min-height: 100vh; 
  background: var(--bg-primary); 
  display: flex; 
}

.admin-content { 
  flex: 1; 
  padding-left: 250px; 
  display: flex;
  flex-direction: column;
}

/* Header administrativo */
.admin-header {
  background: var(--white);
  border-bottom: 1px solid var(--border-light);
  padding: 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.admin-header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  height: 70px;
}

.admin-header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.mobile-menu-toggle {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: var(--text-primary);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.mobile-menu-toggle:hover {
  background: var(--bg-secondary);
}

.admin-breadcrumb {
  display: flex;
  align-items: center;
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.breadcrumb-text {
  font-weight: 500;
}

.current-page {
  color: var(--text-primary);
  font-weight: 600;
}

.admin-header-right {
  display: flex;
  align-items: center;
}

.admin-user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.admin-user-info:hover {
  background: var(--bg-secondary);
}

.user-avatar {
  font-size: 2rem;
  color: var(--primary-blue);
}

.user-details {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.user-name {
  font-weight: 600;
  color: var(--text-primary);
  font-size: 0.9rem;
}

.user-role {
  color: var(--text-secondary);
  font-size: 0.75rem;
}

.btn-logout {
  background: none;
  border: none;
  color: var(--text-secondary);
  font-size: 1rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.btn-logout:hover {
  color: var(--danger-red);
  background: rgba(220, 53, 69, 0.1);
}

/* Contenido principal */
.admin-main {
  flex: 1;
  overflow-y: auto;
}

/* Responsive */
@media (max-width: 991.98px) {
  .admin-content { 
    padding-left: 0; 
  }
  
  .user-details {
    display: none;
  }
  
  .admin-breadcrumb .breadcrumb-text {
    display: none;
  }
}

@media (max-width: 576px) {
  .admin-header-content {
    padding: 0.75rem 1rem;
    height: 60px;
  }
  
  .admin-breadcrumb {
    font-size: 0.8rem;
  }
  
  .user-avatar {
    font-size: 1.5rem;
  }
}
</style>


