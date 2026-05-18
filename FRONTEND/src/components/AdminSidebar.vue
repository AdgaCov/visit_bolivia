<template>
  <aside class="admin-sidebar">
    <!-- Overlay para móvil -->
    <div class="sidebar-overlay d-lg-none" @click="closeSidebar"></div>
    
    <!-- Contenido del sidebar -->
    <div class="sidebar-content">
      <!-- Logo/Brand -->
      <div class="sidebar-brand">
        <div class="brand-content">
          <img src="/icons/icono_b.svg" alt="Bolivia Turismo" class="brand-logo">
          <div class="brand-text">
            <h5 class="brand-title">Bolivia Turismo</h5>
            <small class="brand-subtitle">Panel Admin</small>
          </div>
        </div>
      </div>
      
      <!-- Navegación principal -->
      <nav class="sidebar-nav">
        <!-- Sección de contenidos -->
        <div class="nav-section" v-if="canSeeAll || isEditor">
          <div class="section-title">
            <i class="fas fa-folder-open me-2"></i>
            Contenidos
          </div>

          <router-link 
            to="/admin/content/locations" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/locations' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll || isEditor"
          >
            <i class="fas fa-bed nav-icon"></i>
            <span class="nav-text">Locaciones</span>
          </router-link>

          <router-link 
            to="/admin/content/destinations" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/destinations' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll || isEditor"
          >
            <i class="fas fa-route nav-icon"></i>
            <span class="nav-text">Destinos</span>
          </router-link>


          <router-link 
            to="/admin/content/articles" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/articles' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll"
          >
            <i class="fas fa-newspaper nav-icon"></i>
            <span class="nav-text">Artículos</span>
          </router-link>
          
          <router-link 
            to="/admin/content/article-relations" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/article-relations' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll"
          >
            <i class="fas fa-project-diagram nav-icon"></i>
            <span class="nav-text">Relaciones Artículos</span>
          </router-link>
          
          <router-link 
            to="/admin/content/categories" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/categories' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll"
          >
            <i class="fas fa-tags nav-icon"></i>
            <span class="nav-text">Categorías</span>
          </router-link>

          <router-link 
            to="/admin/content/subcategories" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/subcategories' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll"
          >
            <i class="fas fa-layer-group nav-icon"></i>
            <span class="nav-text">Subcategorías</span>
          </router-link>

          <router-link 
            to="/admin/content/location-images" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/location-images' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll || isEditor"
          >
            <i class="fas fa-image nav-icon"></i>
            <span class="nav-text">Imágenes Locaciones</span>
          </router-link>
          <router-link 
            to="/admin/content/location-items" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/location-items' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll || isEditor"
          >
            <i class="fas fa-image nav-icon"></i>
            <span class="nav-text">Items Locaciones</span>
          </router-link>
          
          <router-link 
            to="/admin/content/location-subcategories" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/location-subcategories' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll"
          >
            <i class="fas fa-sitemap nav-icon"></i>
            <span class="nav-text">Locaciones & Subcategorías</span>
          </router-link>
          
          <router-link 
            to="/admin/content/location-reviews" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/location-reviews' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll"
          >
            <i class="fas fa-star nav-icon"></i>
            <span class="nav-text">Reviews Locaciones</span>
          </router-link>
          
          <router-link 
            to="/admin/content/location-policies" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/content/location-policies' }"
            @click="closeSidebarOnMobile"
            v-if="canSeeAll || isEditor"
          >
            <i class="fas fa-file-alt nav-icon"></i>
            <span class="nav-text">Políticas</span>
          </router-link>
        </div>

        <!-- Sección de administración -->
        <div class="nav-section" v-if="isSuperAdmin">
          <div class="section-title">
            <i class="fas fa-cog me-2"></i>
            Administración
          </div>

          <router-link 
            to="/admin/users" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/users' }"
            @click="closeSidebarOnMobile"
          >
            <i class="fas fa-users nav-icon"></i>
            <span class="nav-text">Usuarios</span>
          </router-link>

          <router-link 
            to="/admin/plans" 
            class="nav-item"
            :class="{ 'active': $route.path === '/admin/plans' }"
            @click="closeSidebarOnMobile"
          >
            <i class="fas fa-tags nav-icon"></i>
            <span class="nav-text">Planes</span>
          </router-link>
        </div>
      
      </nav>
      
      <!-- Footer del sidebar -->
      <div class="sidebar-footer">
        <div class="footer-content">
          <small class="text-muted">© 2025 Bolivia Turismo</small>
          <a href="/" target="_blank" class="view-site-link" title="Ver sitio web">
            <i class="fas fa-external-link-alt"></i>
            <span>Ver sitio</span>
          </a>
        </div>
      </div>
    </div>
  </aside>
</template>

<script>
import { computed } from 'vue'
import { useAuthStore } from '@/store/auth'

export default {
  name: 'AdminSidebar',
  setup() {
    const authStore = useAuthStore()

    const currentUser = computed(() => authStore.currentUser)
    
    // role puede venir como string ("super_admin") o como objeto { id, name, ... }
    const roleName = computed(() => {
      const user = currentUser.value || {}
      const rawRole = user.role
      if (!rawRole) return ''
      if (typeof rawRole === 'string') return rawRole
      return rawRole.name || ''
    })

    // role_id viene como string ("1", "3"), lo normalizamos a number
    const roleId = computed(() => {
      const user = currentUser.value || {}
      const raw = user.role_id
      if (raw === undefined || raw === null) return null
      const n = Number(raw)
      return Number.isNaN(n) ? null : n
    })

    // Soporta tanto name de rol (super_admin, admin, editor, user)
    // como role_id numérico (1=super_admin, 2=admin, 3=editor, 4=user)
    const isSuperAdmin = computed(
      () => roleName.value === 'super_admin' || roleId.value === 1
    )
    const isAdmin = computed(
      () => roleName.value === 'admin' || roleId.value === 2
    )
    const isEditor = computed(
      () => roleName.value === 'editor' || roleId.value === 3
    )

    // Super admin y admin ven todo el menú
    const canSeeAll = computed(() => isSuperAdmin.value || isAdmin.value)

    const closeSidebar = () => {
      const sidebar = document.querySelector('.admin-sidebar')
      if (sidebar) {
        sidebar.classList.remove('show-mobile')
      }
    }

    const closeSidebarOnMobile = () => {
      // Solo cerrar en móvil
      if (window.innerWidth < 992) {
        closeSidebar()
      }
    }

    return {
      closeSidebar,
      closeSidebarOnMobile,
      isSuperAdmin,
      isAdmin,
      isEditor,
      canSeeAll
    }
  }
}
</script>

<style scoped>
.admin-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 250px;
  height: 100vh;
  background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
  z-index: 1000;
  transition: transform 0.3s ease;
}

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.admin-sidebar.show-mobile .sidebar-overlay {
  opacity: 1;
  visibility: visible;
}

.sidebar-content {
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 0;
  overflow-y: auto;
}

/* Brand */
.sidebar-brand {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.brand-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.brand-logo {
  width: 40px;
  height: 40px;
  object-fit: contain;
}

.brand-text {
  flex: 1;
}

.brand-title {
  color: white;
  font-size: 1rem;
  font-weight: 600;
  margin: 0;
  line-height: 1.2;
}

.brand-subtitle {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.75rem;
}

/* Navegación */
.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
}

.nav-section {
  margin-bottom: 1.5rem;
}

.section-title {
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 0 1.5rem 0.5rem;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
  margin-bottom: 0.25rem;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: white;
  border-left-color: var(--primary-blue);
}

.nav-item.active {
  background: rgba(59, 130, 246, 0.1);
  color: white;
  border-left-color: var(--primary-blue);
}

.nav-icon {
  width: 18px;
  text-align: center;
  font-size: 0.9rem;
}

.nav-text {
  font-size: 0.9rem;
  font-weight: 500;
}

/* Footer del sidebar */
.sidebar-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  margin-top: auto;
}

.footer-content {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.view-site-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  font-size: 0.8rem;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.view-site-link:hover {
  background: rgba(255, 255, 255, 0.05);
  color: white;
}

/* Responsive */
@media (max-width: 991.98px) {
  .admin-sidebar {
    transform: translateX(-100%);
  }
  
  .admin-sidebar.show-mobile {
    transform: translateX(0);
  }
}

@media (max-width: 576px) {
  .admin-sidebar {
    width: 280px;
  }
  
  .brand-content {
    gap: 0.5rem;
  }
  
  .brand-logo {
    width: 32px;
    height: 32px;
  }
  
  .brand-title {
    font-size: 0.9rem;
  }
  
  .nav-item {
    padding: 0.65rem 1rem;
  }
}
</style>
