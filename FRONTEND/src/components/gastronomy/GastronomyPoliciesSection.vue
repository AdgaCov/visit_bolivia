<template>
  <section class="policies-section my-4">
    <div class="card p-4">
      <h3 class="section-title">
        <i class="fas fa-file-alt icon"></i>Políticas
      </h3>

      <!-- Loading state -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p class="text-muted">Cargando políticas...</p>
      </div>

      <!-- Content -->
      <div v-else-if="policyData" class="policies-grid">
        <!-- Reservas -->
        <div class="policy-card">
          <div class="policy-header">
            <i class="fas fa-calendar-check icon"></i>
            <h4 class="policy-title">Reservas</h4>
          </div>
          <div class="policy-content">
            <p v-if="reservationText" class="policy-text">{{ reservationText }}</p>
            <p v-else class="policy-text text-muted">Consultar condiciones de reserva</p>
            <a 
              v-if="policyData.reservation_link" 
              :href="policyData.reservation_link" 
              target="_blank" 
              rel="noopener"
              class="policy-link"
            >
              <i class="fas fa-external-link-alt"></i>
              Reservar ahora
            </a>
          </div>
        </div>

        <!-- Cancelación -->
        <div class="policy-card">
          <div class="policy-header">
            <i class="fas fa-times-circle icon"></i>
            <h4 class="policy-title">Cancelación</h4>
          </div>
          <div class="policy-content">
            <p v-if="cancellationText" class="policy-text">{{ cancellationText }}</p>
            <p v-else class="policy-text text-muted">Consultar condiciones de cancelación</p>
          </div>
        </div>

        <!-- Horarios -->
        <div class="policy-card">
          <div class="policy-header">
            <i class="fas fa-clock icon"></i>
            <h4 class="policy-title">Horarios</h4>
          </div>
          <div class="policy-content">
            <p v-if="hoursText" class="policy-text">{{ hoursText }}</p>
            <p v-else class="policy-text text-muted">Consultar horarios</p>
          </div>
        </div>

        <!-- Formas de pago -->
        <div class="policy-card">
          <div class="policy-header">
            <i class="fas fa-credit-card icon"></i>
            <h4 class="policy-title">Formas de pago</h4>
          </div>
          <div class="policy-content">
            <div v-if="paymentMethods.length > 0" class="payment-methods">
              <span 
                v-for="(method, index) in paymentMethods" 
                :key="index"
                class="payment-badge"
              >
                <i :class="getPaymentIcon(method)"></i>
                {{ method }}
              </span>
            </div>
            <p v-else class="policy-text text-muted">Consultar métodos disponibles</p>
            <p v-if="policyData.payment_notes" class="payment-notes">
              <i class="fas fa-info-circle"></i>
              {{ policyData.payment_notes }}
            </p>
          </div>
        </div>
      </div>

      <!-- No data state -->
      <div v-else class="no-data-state">
        <i class="fas fa-info-circle"></i>
        <p class="text-muted">No hay información de políticas disponible</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, watch, defineExpose } from 'vue'
import { getLocationPolicies, type LocationPolicy } from '@/services/location-policies.service'

interface PoliciesInfo { 
  reservations?: string
  cancellation?: string
  hours?: string
}

const props = defineProps<{
  locationId?: string | number | null
  policies?: PoliciesInfo | null
  payments?: string[] | null
}>()

const policyData = ref<LocationPolicy | null>(null)
const loading = ref(false)
const hasPolicies = ref(false)

const emit = defineEmits<{
  policiesLoaded: [hasPolicies: boolean]
}>()

// Exponer hasPolicies para que el componente padre pueda verificar si hay políticas
defineExpose({
  hasPolicies
})

// Computed properties para formatear los datos
const reservationText = computed(() => {
  if (policyData.value?.reservation_notes) {
    return policyData.value.reservation_notes
  }
  return props.policies?.reservations || null
})

const cancellationText = computed(() => {
  if (policyData.value?.cancellation_policy) {
    let text = policyData.value.cancellation_policy
    if (policyData.value.cancellation_deadline_hours) {
      const hours = policyData.value.cancellation_deadline_hours
      const days = Math.floor(hours / 24)
      const remainingHours = hours % 24
      let deadlineText = ''
      if (days > 0) {
        deadlineText = `${days} día${days > 1 ? 's' : ''}`
        if (remainingHours > 0) {
          deadlineText += ` y ${remainingHours} hora${remainingHours > 1 ? 's' : ''}`
        }
      } else {
        deadlineText = `${hours} hora${hours > 1 ? 's' : ''}`
      }
      text = `Cancelación con ${deadlineText} de anticipación. ${text}`
    }
    return text
  }
  return props.policies?.cancellation || null
})

const hoursText = computed(() => {
  if (policyData.value?.opening_hours) {
    let text = policyData.value.opening_hours
    if (policyData.value.days_closed && policyData.value.days_closed !== 'Ninguno') {
      text += `. Días cerrado: ${policyData.value.days_closed}`
    }
    return text
  }
  return props.policies?.hours || null
})

const paymentMethods = computed(() => {
  if (policyData.value) {
    const methods: string[] = []
    if (policyData.value.accepts_cash) methods.push('Efectivo')
    if (policyData.value.accepts_credit_card) methods.push('Tarjeta de crédito')
    if (policyData.value.accepts_debit_card) methods.push('Tarjeta de débito')
    if (policyData.value.accepts_bank_transfer) methods.push('Transferencia bancaria')
    if (policyData.value.accepts_digital_wallet) methods.push('Billetera digital')
    return methods
  }
  return props.payments || []
})

const getPaymentIcon = (method: string): string => {
  const icons: Record<string, string> = {
    'Efectivo': 'fas fa-money-bill-wave',
    'Tarjeta de crédito': 'fas fa-credit-card',
    'Tarjeta de débito': 'fas fa-credit-card',
    'Transferencia bancaria': 'fas fa-university',
    'Billetera digital': 'fas fa-wallet'
  }
  return icons[method] || 'fas fa-money-bill'
}

const loadPolicies = async () => {
  if (!props.locationId) {
    // Si no hay locationId pero hay props legacy, considerar que hay políticas
    const hasLegacyPolicies = !!(props.policies || props.payments)
    policyData.value = null
    hasPolicies.value = hasLegacyPolicies
    emit('policiesLoaded', hasLegacyPolicies)
    return
  }

  loading.value = true
  try {
    const data = await getLocationPolicies(props.locationId)
    policyData.value = data
    hasPolicies.value = !!data
    emit('policiesLoaded', hasPolicies.value)
  } catch (error) {
    console.error('Error loading location policies:', error)
    policyData.value = null
    hasPolicies.value = false
    emit('policiesLoaded', false)
  } finally {
    loading.value = false
  }
}

// Cargar políticas cuando cambie el locationId
watch(() => props.locationId, () => {
  loadPolicies()
}, { immediate: true })

onMounted(() => {
  if (props.locationId) {
    loadPolicies()
  }
})
</script>

<style scoped>
.policies-section .card {
  background: var(--bg-secondary);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  padding: 2rem !important;
}

.section-title {
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  font-size: 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.section-title .icon {
  color: var(--primary-blue);
  font-size: 1.125rem;
}

/* Loading state */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  gap: 1rem;
}

.loading-state .spinner {
  width: 2.5rem;
  height: 2.5rem;
  border: 3px solid var(--border-light);
  border-top-color: var(--primary-blue);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* No data state */
.no-data-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  gap: 0.75rem;
  color: var(--text-muted);
}

.no-data-state i {
  font-size: 2rem;
  opacity: 0.5;
}

/* Policies grid */
.policies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.25rem;
}

/* Policy card */
.policy-card {
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  padding: 1.5rem;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
}

.policy-card:hover {
  border-color: var(--border-color);
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.policy-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--border-light);
}

.policy-header .icon {
  color: var(--primary-blue);
  font-size: 1.125rem;
  width: 1.5rem;
  text-align: center;
}

.policy-title {
  font-size: 1rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin: 0;
}

.policy-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.policy-text {
  font-size: 0.9375rem;
  line-height: 1.6;
  color: var(--text-primary);
  margin: 0;
}

.policy-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--primary-blue);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: var(--font-weight-medium);
  margin-top: 0.5rem;
  transition: all 0.2s ease;
}

.policy-link:hover {
  color: var(--primary-blue-dark);
  gap: 0.75rem;
}

.policy-link i {
  font-size: 0.75rem;
}

/* Payment methods */
.payment-methods {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.payment-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 0.75rem;
  background: var(--light-gray);
  border: 1px solid var(--border-light);
  border-radius: 6px;
  font-size: 0.8125rem;
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
  transition: all 0.2s ease;
}

.payment-badge:hover {
  background: var(--primary-blue);
  color: var(--white);
  border-color: var(--primary-blue);
  transform: translateY(-1px);
}

.payment-badge i {
  font-size: 0.75rem;
}

.payment-notes {
  margin-top: 0.75rem;
  padding-top: 0.75rem;
  border-top: 1px solid var(--border-light);
  font-size: 0.8125rem;
  color: var(--text-secondary);
  line-height: 1.5;
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
}

.payment-notes i {
  color: var(--primary-blue);
  margin-top: 0.125rem;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .policies-section .card {
    padding: 1.5rem !important;
  }

  .policies-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .policy-card {
    padding: 1.25rem;
  }
}
</style>




