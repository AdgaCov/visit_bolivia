<template>
  <transition name="fade">
    <div v-show="isVisible" class="scroll-buttons">
      <button 
        @click="shareCurrentLink"
        class="share-button"
        aria-label="Compartir enlace"
        title="Compartir"
      >
        <i class="fas fa-share-alt"></i>
      </button>
      <button 
        @click="scrollToTop"
        class="scroll-to-top"
        aria-label="Volver arriba"
        title="Volver arriba"
      >
        <i class="fas fa-chevron-up"></i>
      </button>
    </div>
  </transition>
  <transition name="fade">
    <div
      v-if="toast.visible"
      class="share-toast"
      role="status"
      aria-live="polite"
    >
      {{ toast.message }}
    </div>
  </transition>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'ScrollToTop',
  setup() {
    const isVisible = ref(false)

    const toggleVisibility = () => {
      // Mostrar el botón cuando el usuario haya scrolleado más de 300px
      isVisible.value = window.pageYOffset > 300
    }

    const scrollToTop = () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      })
    }

    const shareCurrentLink = async () => {
      try {
        const shareData = {
          title: document.title || 'Bolivia Turismo',
          text: 'Mira este contenido interesante de Bolivia Turismo',
          url: window.location.href
        }
        if (navigator.share) {
          await navigator.share(shareData)
          showToast('Enlace compartido')
          return
        }
        await navigator.clipboard.writeText(window.location.href)
        showToast('Enlace copiado al portapapeles')
      } catch (e) {
        console.warn('No se pudo compartir el enlace', e)
      }
    }

    onMounted(() => {
			// Chequeo inicial por si se entra con scroll desplazado
			toggleVisibility()
			// Listener pasivo para mejor rendimiento en scroll
			window.addEventListener('scroll', toggleVisibility, { passive: true })
    })

    onUnmounted(() => {
      window.removeEventListener('scroll', toggleVisibility)
    })

    const toast = ref({ visible: false, message: '' })

    const showToast = (message) => {
      toast.value.message = message
      toast.value.visible = true
      window.setTimeout(() => {
        toast.value.visible = false
      }, 2000)
    }

    return {
      isVisible,
      scrollToTop,
      shareCurrentLink,
      toast
    }
  }
}
</script>

<style scoped>
.scroll-buttons {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  z-index: 1000;
}

.share-button {
  width: 50px;
  height: 50px;
  background: var(--white);
  color: var(--text-primary);
  border: 1px solid var(--border-light);
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  box-shadow: var(--shadow-lg);
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.share-button:hover {
  background: var(--light-gray);
  transform: translateY(-2px);
}

.scroll-to-top {
  width: 50px;
  height: 50px;
  background: var(--primary-blue);
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  box-shadow: var(--shadow-lg);
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.scroll-to-top:hover {
  background: var(--primary-blue-dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
}

.scroll-to-top:active {
  transform: translateY(0);
}

.share-toast {
  position: fixed;
  bottom: 7.25rem;
  right: 2rem;
  background: rgba(0, 0, 0, 0.8);
  color: #fff;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  font-size: 0.9rem;
  box-shadow: var(--shadow-lg);
  z-index: 1001;
}

/* Animaciones */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Responsive */
@media (max-width: 768px) {
  .scroll-buttons { bottom: 1.5rem; right: 1.5rem; }
  .share-toast { bottom: 6.75rem; right: 1.5rem; }
  .scroll-to-top, .share-button { width: 45px; height: 45px; }
  .scroll-to-top { font-size: 1.1rem; }
}

@media (max-width: 480px) {
  .scroll-buttons { bottom: 1rem; right: 1rem; }
  .share-toast { bottom: 6.25rem; right: 1rem; }
  .scroll-to-top, .share-button { width: 40px; height: 40px; }
  .scroll-to-top { font-size: 1rem; }
}
</style>

