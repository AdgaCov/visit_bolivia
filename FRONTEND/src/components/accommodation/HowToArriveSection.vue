<template>
	<section class="how-to-arrive my-4">
		<div class="card p-4">
			<h3 class="h5 mb-3"><i class="fas fa-route me-2"></i>Cómo llegar</h3>
			<div class="row g-3">
				<div class="col-12 col-md-6">
					<ul class="list-unstyled m-0">
						<li class="mb-2" v-for="(item, i) in transport" :key="i">
							<i class="fas fa-bus me-2 text-primary"></i>{{ item }}
						</li>
					</ul>
				</div>
				<div class="col-12 col-md-6">
					<ul class="list-unstyled m-0">
						<li class="mb-2" v-for="(p, i) in pointsOfInterest" :key="i">
							<i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ p.name }} — {{ p.distanceKm }} km
						</li>
					</ul>
					<a :href="mapsUrl" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mt-2">
						Abrir en Google Maps
					</a>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface POI { name: string; distanceKm: number }

const props = defineProps<{ latitude?: string | number | null; longitude?: string | number | null; address?: string | null; transport?: string[]; pointsOfInterest?: POI[] }>()

const lat = computed(() => props.latitude ? parseFloat(String(props.latitude)) : null)
const lng = computed(() => props.longitude ? parseFloat(String(props.longitude)) : null)

const mapsUrl = computed(() => {
	if (lat.value != null && lng.value != null) {
		return `https://www.google.com/maps?q=${lat.value},${lng.value}`
	}
	if (props.address) {
		return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(props.address)}`
	}
	return 'https://www.google.com/maps'
})

const transport = computed(() => props.transport || ['Taxi desde el aeropuerto', 'Bus urbano cercano', 'Transporte privado disponible'])
const pointsOfInterest = computed<POI[]>(() => props.pointsOfInterest || [])
</script>

<style scoped>
.how-to-arrive .card { background: var(--bg-secondary); border: 1px solid var(--border-light); border-radius: 12px; }
</style>




