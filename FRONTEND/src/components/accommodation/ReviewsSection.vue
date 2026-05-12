<template>
	<section class="reviews-section my-4">
		<div class="card p-4">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h3 class="h5 m-0"><i class="fas fa-star me-2 text-warning"></i>Opiniones de huéspedes</h3>
				<div v-if="summary" class="rating-summary">
					<span class="avg">{{ summary.average.toFixed(1) }}</span>
					<span class="muted">/ 5</span>
					<span class="count text-muted">({{ summary.count }} reseñas)</span>
				</div>
			</div>

			<div v-if="reviews && reviews.length" class="reviews-list">
				<article v-for="r in reviews" :key="r.id" class="review-item">
					<div class="review-header">
						<strong>{{ r.author }}</strong>
						<span class="stars" :title="`${r.rating}/5`">
							<i v-for="n in 5" :key="n" :class="n <= r.rating ? 'fas fa-star text-warning' : 'far fa-star text-warning'" />
						</span>
						<span class="date text-muted">{{ formatDate(r.date) }}</span>
					</div>
					<p class="mb-0 text-secondary">{{ r.text }}</p>
				</article>
			</div>
			<div v-else class="text-muted">Aún no hay reseñas.</div>
		</div>
	</section>
</template>

<script setup lang="ts">
interface ReviewItem { id: string | number; author: string; rating: number; date: string; text: string }
interface ReviewSummary { average: number; count: number }

const props = defineProps<{ reviews: ReviewItem[] | null | undefined; summary?: ReviewSummary | null }>()

const formatDate = (iso: string) => {
	const d = new Date(iso)
	return d.toLocaleDateString()
}
</script>

<style scoped>
.rating-summary .avg { font-size: 1.25rem; font-weight: 700; margin-right: 0.25rem; }
.rating-summary .muted { color: var(--text-secondary); margin-right: 0.5rem; }
.reviews-list { display: grid; gap: 1rem; }
.review-item { border: 1px solid var(--border-light); border-radius: 12px; background: var(--bg-secondary); padding: 1rem; }
.review-header { display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; }
.review-header .date { margin-left: auto; }
</style>




