<template>
  <!-- Top Banner - Estilo Visit Estonia -->
  <div class="top-banner">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <div class="brand-small">
          <img src="/icons/Logo_cobol.png" alt="Bolivia Turismo" class="brand-logo">
          <a href="#" class="top-nav-link active">Visitante</a>
          <!-- <a href="#" class="top-nav-link">Profesional</a>
          <a href="#" class="top-nav-link">Medios</a> -->
        </div>
        
        <div class="top-nav">
          
          <!-- <router-link to="/admin/login" class="top-nav-link">Iniciar sesión</router-link>
          <button type="button" class="top-nav-link lang-btn" @click="toggleLang" :aria-label="`Cambiar idioma a ${currentLang === 'ES' ? 'EN' : 'ES'}`" title="Cambiar idioma">
            <i class="fas fa-globe"></i>
            <span class="nav-icon-text">{{ currentLang }}</span>
          </button> -->
        </div>
      </div>
    </div>
  </div>

  <!-- Main Navigation - Estilo moderno -->
  <nav class="navbar navbar-expand-lg navbar-modern" role="navigation" aria-label="Navegación principal">
    <div class="container">
      <router-link class="navbar-brand-modern" to="/" aria-label="Bolivia Turismo - Ir al inicio">
        <img src="/icons/tucan_n.svg" alt="Bolivia Turismo" class="brand-logo">
       
      </router-link>
      
      <button class="navbar-toggler modern" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <button type="button" class="nav-link nav-link-button" @click="handleStaticClick">
              Inicio
            </button>
          </li>
          
          <li class="nav-item">
            <button type="button" class="nav-link nav-link-button" @click="handleStaticClick">
              Qué hacer
            </button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link nav-link-button" @click="handleStaticClick">
              Dónde ir
            </button>
          </li>
          
          <li class="nav-item">
            <button type="button" class="nav-link nav-link-button" @click="handleStaticClick">
              Planifica tu viaje
            </button>
          </li>
          
          <!-- <li class="nav-item">
            <router-link class="nav-link" to="/como-llegar" @click="handleLinkClick">
              Cómo Llegar
            </router-link>
          </li> -->
        </ul>
        
        <!-- Right side icons - Estilo Visit Estonia -->
        <div class="navbar-nav-right">
          
          <router-link to="/favoritos" class="nav-icon-link" @click="handleLinkClick">
            <i class="fas fa-heart"></i>
            <span class="nav-icon-text">Favoritos</span>
          </router-link>
          
          <router-link to="/mapa" class="nav-icon-link" @click="handleLinkClick">
            <i class="fas fa-map-marked-alt"></i>
            <span class="nav-icon-text">Mapa</span>
          </router-link>

          <button type="button" class="nav-icon-link nav-icon-button" @click="handleStaticClick">
            <i class="fas fa-search"></i>
            <span class="nav-icon-text">Buscar</span>
          </button>
          <button type="button" class="nav-icon-link nav-icon-button" @click="handleStaticClick">
            <i class="fas fa-chart-line"></i>
            <span class="nav-icon-text">Estadísticas</span>
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { ref, onMounted, getCurrentInstance } from 'vue'

export default {
  name: 'AppHeader',
  setup() {
    const isMenuOpen = ref(false)
    const instance = getCurrentInstance()
    const currentLang = ref('ES')

    const closeMenu = () => {
      isMenuOpen.value = false
      // Cerrar el menú de Bootstrap manipulando las clases directamente
      const navbarCollapse = document.getElementById('navbarNav')
      if (navbarCollapse && navbarCollapse.classList.contains('show')) {
        navbarCollapse.classList.remove('show')
        // También remover la clase del botón toggler
        const toggler = document.querySelector('.navbar-toggler')
        if (toggler) {
          toggler.setAttribute('aria-expanded', 'false')
          toggler.classList.add('collapsed')
        }
      }
    }

    const handleLinkClick = () => {
      closeMenu()
    }

    const handleStaticClick = () => {
      closeMenu()
    }

    const toggleLang = () => {
      currentLang.value = currentLang.value === 'ES' ? 'EN' : 'ES'
      // Placeholder: aquí luego podemos integrar i18n o recarga de contenido
      console.log('Idioma actual:', currentLang.value)
    }

    onMounted(() => {
      // Escuchar cambios de ruta para cerrar el menú
      const router = instance?.appContext.config.globalProperties.$router
      if (router) {
        router.afterEach(() => {
          closeMenu()
        })
      }
    })

    return {
      isMenuOpen,
      closeMenu,
      handleLinkClick,
      handleStaticClick,
      currentLang,
      toggleLang
    }
  }
}
</script>

<style scoped>
/* Top Banner - Estilo Visit  */
.top-banner {
  background: var(--bg-secondary);
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--border-light);
}

.brand-small {
  font-size: 1.25rem;
  font-weight: 300;
  color: var(--text-primary);
  /* text-transform: lowercase; */
  letter-spacing: 0.05em;
  display: flex;
  align-items: center;
  gap: 1.25rem;
  flex-wrap: wrap;
}

.brand-logo {
  height: 50px;
  width: auto;
  object-fit: contain;
}

/* Permitir que el contenedor flex de la franja superior haga wrap si falta espacio */
.top-banner .d-flex {
  flex-wrap: wrap;
  row-gap: 0.5rem;
}

.top-nav {
  display: flex;
  align-items: center;
  gap: 1.25rem;
}

.top-nav-link {
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  padding: 0.25rem 0;
  transition: all 0.2s ease;
  position: relative;
}

/* Soporte para botón como enlace en top-nav (idioma) */
button.top-nav-link {
  background: transparent;
  border: none;
  padding: 0.25rem 0;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  cursor: pointer;
}

.top-nav-link.lang-btn .fa-globe { font-size: 0.95rem; }
.top-nav-link.lang-btn .nav-icon-text { font-size: 0.8rem; margin-left: 0; }

.top-nav-link:hover {
  color: var(--text-primary);
}

.top-nav-link.active {
  color: var(--primary-blue);
}

.top-nav-link.active::after {
  content: '';
  position: absolute;
  bottom: -0.25rem;
  left: 0;
  width: 100%;
  height: 2px;
  background: var(--primary-blue);
}

/* Main Navigation */
.navbar-modern {
  background: var(--navbar-bg);
  padding: 1rem 0;
  border-bottom: 1px solid var(--navbar-border);
  box-shadow: var(--navbar-shadow);
}

.navbar-brand-modern {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--navbar-text);
  text-decoration: none;
  transition: all 0.2s ease;
  /* filter: invert(41%) sepia(4%) saturate(342%) hue-rotate(190deg) */
}

.navbar-brand-modern:hover {
  color: var(--navbar-text-hover);
}

.navbar-nav .nav-link {
  color: var(--navbar-text);
  font-weight: 500;
  padding: 1rem 1.5rem;
  transition: all 0.2s ease;
  position: relative;
  border-radius: 8px;
  margin: 0 0.25rem;
}

.nav-link-button {
  background: transparent;
  border: 0;
  width: 100%;
  text-align: left;
}

.navbar-nav .nav-link:hover {
  color: var(--navbar-text-hover);
  background: var(--light-gray);
}

.navbar-nav .nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 0;
  height: 2px;
  background: var(--navbar-text-hover);
  transition: all 0.3s ease;
  transform: translateX(-50%);
}

.navbar-nav .nav-link:hover::after {
  width: 100%;
}

/* Estado activo automático de Vue Router */
.navbar-nav .router-link-exact-active,
.navbar-nav .router-link-active {
  color: var(--navbar-text-hover);
  background: var(--light-gray);
}

.navbar-nav .router-link-exact-active::after,
.navbar-nav .router-link-active::after {
  width: 100%;
  background: var(--navbar-text-hover);
}

/* Dropdowns modernos */
.dropdown-menu-modern {
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
  padding: 0.5rem 0;
  margin-top: 0.5rem;
  min-width: 220px;
  animation: slideDown 0.3s ease-out;
  transform-origin: top;
}

.dropdown-item-modern {
  padding: 0.75rem 1.5rem;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.2s ease;
  border-radius: 8px;
  margin: 0 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.dropdown-item-modern:hover {
  background: var(--light-gray);
  color: var(--navbar-text-hover);
  transform: translateX(4px);
}

/* Activo en items del dropdown */
.dropdown-item-modern.router-link-exact-active,
.dropdown-item-modern.router-link-active {
  background: var(--light-gray);
  color: var(--navbar-text-hover);
}

.dropdown-item-modern i {
  width: 16px;
  text-align: center;
  color: var(--text-secondary);
}

.dropdown-header {
  color: var(--text-secondary);
  font-weight: 600;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 0.5rem 1.5rem;
  margin: 0 0.5rem;
}

.dropdown-divider {
  margin: 0.5rem 1rem;
  border-color: var(--border-light);
}

/* Right side icons */
.navbar-nav-right {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.nav-icon-link {
  /* display: flex; */
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
  color: var(--navbar-text);
  /* color: var(--text-secondary); */
  text-decoration: none;
  transition: all 0.2s ease;
  padding: 0.5rem;
  border-radius: 8px;
  margin: 5px;
}

.nav-icon-button {
  background: transparent;
  border: 0;
}

.nav-icon-link:hover {
  color: var(--navbar-text-hover);
  background: var(--light-gray);
  transform: translateY(-2px);
}

/* Estado activo para iconos del lado derecho */
.nav-icon-link.router-link-exact-active,
.nav-icon-link.router-link-active {
  color: var(--navbar-text-hover);
  background: var(--light-gray);
}

.nav-icon-link.router-link-exact-active i,
.nav-icon-link.router-link-active i {
  color: var(--primary-blue);
}

.nav-icon-link i {
  font-size: 1.25rem;
}

.nav-icon-text {
  font-size: 0.75rem;
  font-weight: 500;
  margin-left: 10px;
  font-size: 18px;
}

/* Navbar toggler moderno */
.navbar-toggler.modern {
  border: 2px solid var(--border-color);
  border-radius: 8px;
  padding: 0.5rem;
}

.navbar-toggler.modern:focus {
  box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
}

/* Animaciones */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: scaleY(0);
  }
  to {
    opacity: 1;
    transform: scaleY(1);
  }
}

/* Responsive */
@media (max-width: 991.98px) {
  .navbar-nav {
    padding: 1rem 0;
  }
  
  .nav-link {
    margin: 0.25rem 0;
    padding: 0.75rem 1rem !important;
  }
  
  .dropdown-menu-modern {
    border: none;
    box-shadow: none;
    background: var(--light-gray);
    margin: 0;
    padding-left: 1rem;
  }
  
  .dropdown-item-modern {
    color: var(--text-primary);
    margin: 0;
    width: 100%;
  }
  
  .navbar-nav-right {
    margin-top: 1rem;
    justify-content: center;
    width: 100%;
  }
}

@media (max-width: 768px) {
  .top-nav {
    gap: 1rem;
  }
  
  .top-nav-link {
    font-size: 0.75rem;
  }
  
  .navbar-brand-modern {
    font-size: 1.25rem;
  }
}

/* Ajustes para evitar superposición en pantallas medianas/grandes */
@media (max-width: 1399.98px) {
  .navbar-nav .nav-link {
    padding: 0.75rem 1rem;
    margin: 0 0.2rem;
  }
  .navbar-nav-right {
    gap: 1rem;
  }
}

@media (max-width: 1199.98px) {
  .brand-logo {
    height: 40px;
  }
  .navbar-nav .nav-link {
    padding: 0.5rem 0.75rem;
    margin: 0 0.15rem;
  }
  .navbar-nav-right {
    gap: 0.5rem;
  }
  /* Ocultar textos de los iconos para ahorrar espacio */
  .nav-icon-text {
    display: none;
  }
  .nav-icon-link {
    padding: 0.4rem;
  }
}
</style> 
