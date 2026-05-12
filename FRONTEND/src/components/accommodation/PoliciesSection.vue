<template>
	<section class="policies-section my-4">
		<div class="card p-4">
			<h3 class="h5 mb-3"><i class="fas fa-file-alt me-2"></i>Políticas y reglas</h3>
			<div class="row g-3">
				<div class="col-12 col-md-6">
					<div class="policy-box">
						<h4 class="h6">Check-in / Check-out</h4>
						<p class="mb-1"><strong>Check-in:</strong> {{ policies?.checkIn || 'Consultar' }}</p>
						<p class="mb-0"><strong>Check-out:</strong> {{ policies?.checkOut || 'Consultar' }}</p>
					</div>
				</div>
				<div class="col-12 col-md-6">
					<div class="policy-box">
						<h4 class="h6">Cancelación</h4>
						<p class="mb-0">{{ policies?.cancellation || 'Consultar condiciones de cancelación' }}</p>
					</div>
				</div>
				<div class="col-12 col-md-6">
					<div class="policy-box">
						<h4 class="h6">Niños y camas extra</h4>
						<p class="mb-0">{{ policies?.children || 'Consultar' }}</p>
					</div>
				</div>
				<div class="col-12 col-md-6">
					<div class="policy-box">
						<h4 class="h6">Mascotas</h4>
						<p class="mb-0">{{ policies?.pets || 'No permitido' }}</p>
					</div>
				</div>
				<div class="col-12">
					<div class="policy-box">
						<h4 class="h6">Formas de pago</h4>
						<p class="mb-0">{{ paymentsText }}</p>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface PoliciesInfo {
	checkIn?: string
	checkOut?: string
	cancellation?: string
	children?: string
	pets?: string
}

const props = defineProps<{ policies?: PoliciesInfo | null; payments?: string[] | null }>()

const paymentsText = computed(() => {
	const p = props.payments || []
	return p.length ? p.join(', ') : 'Consultar métodos disponibles'
})
</script>

<style scoped>
.policy-box {
	background: var(--bg-secondary);
	border: 1px solid var(--border-light);
	border-radius: 12px;
	padding: 1rem;
}
</style>


