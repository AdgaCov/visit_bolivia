<template>
  <div class="container-fluid admin-auth">
    <div class="row min-vh-100 align-items-center justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card auth-card">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <i class="fas fa-shield-alt display-4 mb-3 auth-icon"></i>
              <h2 class="fw-bold auth-title">Admin Panel</h2>
              <p class="auth-subtitle">Bolivia Turismo</p>
            </div>

            <form @submit.prevent="handleLogin">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                  </span>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="email"
                    v-model="email"
                    placeholder="Correo"
                    required
                    :disabled="authStore.loading"
                  >
                </div>
              </div>

              <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                  </span>
                  <input 
                    type="password" 
                    class="form-control" 
                    id="password"
                    v-model="password"
                    placeholder="••••••••"
                    required
                    :disabled="authStore.loading"
                  >
                </div>
              </div>

              <div v-if="authStore.error" class="alert alert-danger mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ authStore.error }}
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3 auth-btn" :disabled="authStore.loading">
                <i v-if="authStore.loading" class="fas fa-spinner fa-spin me-2"></i>
                <i v-else class="fas fa-sign-in-alt me-2"></i>
                {{ authStore.loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
              </button>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')

// Verificar autenticación al montar el componente
onMounted(() => {
  authStore.checkAuth()
  // Si ya está autenticado, redirigir
  if (authStore.authenticated) {
    router.push('/admin')
  }
})

const handleLogin = async () => {
  if (await authStore.login(email.value, password.value)) {
    router.push('/admin')
  }
}
</script>

<style scoped>
.admin-auth {
  background: var(--bg-primary);
}

.auth-card {
  background: var(--card-bg);
  border: 1px solid var(--border-light);
  border-radius: 16px;
  box-shadow: var(--shadow-lg);
}

.auth-icon {
  color: var(--primary-blue);
}

.auth-title {
  color: var(--text-primary);
}

.auth-subtitle {
  color: var(--text-secondary);
}

.input-group-text {
  background-color: var(--bg-secondary);
  border-color: var(--border-light);
}

.form-control {
  background: var(--white);
  border-color: var(--border-light);
}

.auth-btn {
  border-radius: 10px;
  font-weight: 600;
}

.display-4 {
  font-size: 3rem;
}
</style>