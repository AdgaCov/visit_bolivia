<template>
	<section class="rooms-rates-section my-4">
		<div class="card p-4">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h3 class="h5 m-0"><i class="fas fa-door-open me-2"></i>Habitaciones y tarifas</h3>
				<div class="d-flex gap-2">
					<input type="date" v-model="checkIn" class="form-control form-control-sm" aria-label="Fecha de llegada" />
					<input type="date" v-model="checkOut" class="form-control form-control-sm" aria-label="Fecha de salida" />
				</div>
			</div>

			<div v-if="rooms && rooms.length" class="rooms-grid">
				<article v-for="(room, index) in visibleRooms" :key="room.id" class="room-card">
					<div class="room-media" v-if="room.photos && room.photos.length">
						<img :src="getRoomImage(room, index)" :alt="room.name" />
					</div>
					<div class="room-content">
						<h4 class="h6 mb-1">{{ room.name }}</h4>
						<p class="text-muted mb-2">Capacidad: {{ room.capacity }} • Camas: {{ room.beds }}</p>
						<ul class="amenities list-inline mb-2" v-if="room.amenities && room.amenities.length">
							<li v-for="(a, i) in room.amenities" :key="i" class="list-inline-item badge rounded-pill bg-light text-dark border">{{ a }}</li>
						</ul>
						<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
							<strong class="price">{{ formatPrice(room.pricePerNight) }}</strong>
							<div class="d-flex gap-2">
								<a :href="whatsAppUrl(room)" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success btn-cta">
									<i class="fab fa-whatsapp me-1 icon-small"></i>Consultar
								</a>
								<button class="btn btn-sm btn-primary btn-cta">Reservar</button>
							</div>
						</div>
					</div>
				</article>
			</div>
			<div v-if="canShowMore" class="text-center mt-3">
				<button class="btn btn-outline-primary btn-sm" @click="showMore">Ver más</button>
			</div>
			<div v-else class="text-muted">No hay tipos de habitación disponibles.</div>
		</div>
	</section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface RoomInfo {
	id: string | number
	name: string
	capacity: number
	beds: string
	amenities: string[]
	photos: string[]
	pricePerNight: string | number
	refundableUntil?: string | null
	availableDates?: string[]
}

const props = defineProps<{
	rooms: RoomInfo[] | null | undefined
	hotelName?: string
	phoneOrWhatsapp?: string | null
	pageSize?: number
}>()

const checkIn = ref<string>('')
const checkOut = ref<string>('')

const pageSize = computed(() => Math.max(1, props.pageSize ?? 6))
const visibleCount = ref<number>(pageSize.value)
const visibleRooms = computed(() => (props.rooms || []).slice(0, visibleCount.value))
const canShowMore = computed(() => (props.rooms?.length || 0) > visibleCount.value)
const showMore = () => { visibleCount.value = Math.min((props.rooms?.length || 0), visibleCount.value + pageSize.value) }

const formatPrice = (price: string | number) => {
	const n = Math.abs(typeof price === 'string' ? parseFloat(price) : price)
	if (Number.isNaN(n)) return 'Consultar precio'
	return `$${n.toFixed(0)}/noche`
}

const getRoomImage = (room: RoomInfo, roomIndex: number) => {
	// Array de imágenes de habitaciones de hoteles de internet
	const hotelImages = [
		'https://d1vp8nomjxwyf1.cloudfront.net/wp-content/uploads/sites/51/2016/11/22164807/arrizul_congress_gallery_doble_deluxe_021.jpg',
		'https://sc04.alicdn.com/kf/H2c670b87340242e7ba2126d826dfed98C.jpg',
		'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=400&h=300&fit=crop'
	]
	
	// Usar el índice de la habitación en el array para garantizar imágenes diferentes
	const imageIndex = roomIndex % hotelImages.length
	
	// Debug: mostrar qué imagen se está seleccionando
	console.log(`Room Index: ${roomIndex}, Image Index: ${imageIndex}, Image URL: ${hotelImages[imageIndex]}`)
	
	return hotelImages[imageIndex]
}

const whatsAppUrl = (room: RoomInfo) => {
	const phone = (props.phoneOrWhatsapp || '').replace(/[^\d+]/g, '') || ''
	const msg = encodeURIComponent(`Hola, quisiera consultar disponibilidad para la habitación "${room.name}" en ${props.hotelName || 'su hotel'} del ${checkIn.value || 'fecha llegada'} al ${checkOut.value || 'fecha salida'}.`)
	const base = phone ? `https://wa.me/${phone}?text=${msg}` : `https://wa.me/?text=${msg}`
	return base
}
</script>

<style scoped>
.rooms-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 1rem;
}
.room-card { display: grid; grid-template-columns: 1fr; border: 1px solid var(--border-light); border-radius: 12px; overflow: hidden; background: var(--bg-secondary); }
.room-media { height: 160px; overflow: hidden; background: #f4f4f4; }
.room-media img { width: 100%; height: 100%; object-fit: cover; display: block; }
.room-content { padding: 1rem; }
.room-content .d-flex { row-gap: 0.5rem; }
.amenities .badge { font-weight: 500; }
.price { color: var(--primary-blue); }
.btn-cta { padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 600; }
.icon-small { font-size: 0.9rem; }

@media (max-width: 576px) {
	/* Evitar solapamiento: apilar precio y botones */
	.room-content .d-flex { flex-direction: column; align-items: flex-start; }
	.price { font-size: 0.95rem; margin-bottom: 0.25rem; }
}
</style>


