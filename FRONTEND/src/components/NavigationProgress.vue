<template>
  <div v-if="isLoading" class="navigation-progress">
    <div class="progress-bar" :style="{ width: progress + '%' }"></div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'NavigationProgress',
  setup() {
    const isLoading = ref(false)
    const progress = ref(0)
    const router = useRouter()
    let progressInterval = null
    let progressTimeout = null

    const startProgress = () => {
      isLoading.value = true
      progress.value = 0
      
      // Simular progreso gradual
      progressInterval = setInterval(() => {
        if (progress.value < 90) {
          progress.value += Math.random() * 15
          if (progress.value > 90) {
            progress.value = 90
          }
        }
      }, 100)
    }

    const finishProgress = () => {
      progress.value = 100
      setTimeout(() => {
        isLoading.value = false
        progress.value = 0
        if (progressInterval) {
          clearInterval(progressInterval)
          progressInterval = null
        }
      }, 200)
    }

    onMounted(() => {
      // Escuchar eventos de navegación del router
      router.beforeEach((to, from, next) => {
        if (from.path !== to.path) {
          startProgress()
        }
        next()
      })

      router.afterEach(() => {
        finishProgress()
      })
    })

    onUnmounted(() => {
      if (progressInterval) {
        clearInterval(progressInterval)
      }
      if (progressTimeout) {
        clearTimeout(progressTimeout)
      }
    })

    return {
      isLoading,
      progress
    }
  }
}
</script>

<style scoped>
.navigation-progress {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  z-index: 9999;
  background: transparent;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, var(--primary-blue), #4A90E2);
  transition: width 0.3s ease;
  box-shadow: 0 0 10px rgba(30, 64, 175, 0.5);
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
  100% {
    opacity: 1;
  }
}
</style>

