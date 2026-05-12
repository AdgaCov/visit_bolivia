import { computed, ref, watch } from 'vue'
import { useAuthStore } from '@/store/auth'
import adminPlansService from '@/services/admin/plans.admin.service'

/**
 * Composable para manejar límites de planes
 * Valida límites basados en el plan del usuario
 * EXCEPTO para role_id = 1 (super_admin) que tiene acceso ilimitado
 */
export function usePlanLimits() {
  const authStore = useAuthStore()
  
  // Plan cargado (se carga automáticamente si solo viene plan_id)
  const loadedPlan = ref(null)
  const loadingPlan = ref(false)

  // Obtener usuario actual
  const currentUser = computed(() => authStore.currentUser)

  // Verificar si es super_admin (role_id = 1) → acceso ilimitado
  const isSuperAdmin = computed(() => {
    const user = currentUser.value
    if (!user) return false
    
    const roleId = user.role_id
    if (roleId === 1 || roleId === '1') return true
    
    const role = user.role
    if (typeof role === 'object' && role?.id === 1) return true
    if (role === 'super_admin') return true
    
    return false
  })

  // Cargar plan del usuario si solo tiene plan_id
  const loadUserPlan = async () => {
    const user = currentUser.value
    if (!user || !user.plan_id) {
      loadedPlan.value = null
      return null
    }
    
    // Si ya tenemos el plan completo en el usuario, usarlo
    if (user.plan && typeof user.plan === 'object') {
      loadedPlan.value = user.plan
      return user.plan
    }
    
    // Si ya está cargando o ya lo tenemos cargado, no volver a cargar
    if (loadingPlan.value || loadedPlan.value) {
      return loadedPlan.value
    }
    
    // Cargar el plan desde el backend
    loadingPlan.value = true
    try {
      const response = await adminPlansService.get(user.plan_id)
      if (response.success && response.data) {
        loadedPlan.value = response.data
        return response.data
      }
    } catch (error) {
      console.error('Error cargando plan del usuario:', error)
      loadedPlan.value = null
    } finally {
      loadingPlan.value = false
    }
    return null
  }

  // Observar cambios en el usuario para cargar el plan automáticamente
  watch(
    () => currentUser.value?.plan_id,
    async (planId, oldPlanId) => {
      // Si cambió el plan_id o es la primera vez que se carga
      if (planId && planId !== oldPlanId && !isSuperAdmin.value) {
        console.log('🔄 Cargando plan del usuario, plan_id:', planId)
        await loadUserPlan()
        console.log('✅ Plan cargado:', loadedPlan.value)
      } else if (!planId) {
        loadedPlan.value = null
      }
    },
    { immediate: true }
  )
  
  // Cargar plan inmediatamente si el usuario ya está disponible al inicializar
  const initializePlan = async () => {
    const user = currentUser.value
    if (user?.plan_id && !isSuperAdmin.value && !loadedPlan.value && !loadingPlan.value) {
      console.log('🔄 Inicializando plan del usuario, plan_id:', user.plan_id)
      await loadUserPlan()
    }
  }
  
  // Ejecutar inicialización
  initializePlan()

  // Obtener plan del usuario
  const userPlan = computed(() => {
    const user = currentUser.value
    if (!user) return null
    
    // Si el plan viene directamente en el usuario
    if (user.plan && typeof user.plan === 'object') {
      return user.plan
    }
    
    // Si tenemos el plan cargado
    if (loadedPlan.value) {
      return loadedPlan.value
    }
    
    return null
  })

  // Obtener límites del plan
  const planLimits = computed(() => {
    if (isSuperAdmin.value) {
      // Super admin tiene límites ilimitados
      return {
        max_locations: Infinity,
        max_location_images: Infinity,
        max_location_items: Infinity
      }
    }
    
    const plan = userPlan.value
    if (!plan) {
      // Si está cargando el plan, retornar null para evitar mostrar límites incorrectos
      if (loadingPlan.value) {
        return {
          max_locations: null,
          max_location_images: null,
          max_location_items: null
        }
      }
      // Si no hay plan después de intentar cargarlo, retornar límites por defecto muy restrictivos
      console.warn('⚠️ No se pudo cargar el plan del usuario')
      return {
        max_locations: 0,
        max_location_images: 0,
        max_location_items: 0
      }
    }
    
    const limits = {
      max_locations: plan.max_locations || 0,
      max_location_images: plan.max_location_images || 0,
      max_location_items: plan.max_location_items || 0
    }
    
    console.log('📊 Límites del plan:', limits)
    return limits
  })

  /**
   * Verificar si puede crear más locaciones
   * @param {number} currentCount - Cantidad actual de locaciones del usuario
   * @returns {boolean}
   */
  const canCreateLocation = (currentCount) => {
    if (isSuperAdmin.value) return true
    return currentCount < planLimits.value.max_locations
  }

  /**
   * Verificar si puede crear más imágenes para una locación
   * @param {number} currentCount - Cantidad actual de imágenes de la locación
   * @returns {boolean}
   */
  const canCreateLocationImage = (currentCount) => {
    if (isSuperAdmin.value) return true
    return currentCount < planLimits.value.max_location_images
  }

  /**
   * Verificar si puede crear más items para una locación
   * @param {number} currentCount - Cantidad actual de items de la locación
   * @returns {boolean}
   */
  const canCreateLocationItem = (currentCount) => {
    if (isSuperAdmin.value) return true
    return currentCount < planLimits.value.max_location_items
  }

  /**
   * Obtener mensaje de límite alcanzado
   * @param {string} type - 'locations' | 'images' | 'items'
   * @param {number} currentCount - Cantidad actual
   * @returns {string}
   */
  const getLimitMessage = (type, currentCount) => {
    if (isSuperAdmin.value) return ''
    
    const limits = {
      locations: planLimits.value.max_locations,
      images: planLimits.value.max_location_images,
      items: planLimits.value.max_location_items
    }
    
    const labels = {
      locations: 'locaciones',
      images: 'imágenes',
      items: 'items'
    }
    
    const limit = limits[type]
    const label = labels[type]
    
    if (currentCount >= limit) {
      return `Has alcanzado el límite de ${limit} ${label} permitidos en tu plan.`
    }
    
    return `Límite: ${currentCount}/${limit} ${label}`
  }

  return {
    isSuperAdmin,
    userPlan,
    planLimits,
    canCreateLocation,
    canCreateLocationImage,
    canCreateLocationItem,
    getLimitMessage,
    loadUserPlan,
    loadingPlan: computed(() => loadingPlan.value)
  }
}

