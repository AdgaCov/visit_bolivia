<template>
  <section class="amenities-section my-4">
    <div class="card p-4">
      <h3 class="h5 mb-3">
        <i class="fas fa-handshake me-2"></i>Servicios
      </h3>
      <div v-if="amenities && amenities.length" class="amenities-grid">
        <div v-for="(group, idx) in amenities" :key="idx" class="amenities-group">
          <h4 class="h6 text-muted mb-2">{{ group.title }}</h4>
          <ul class="list-unstyled m-0 amenities-list">
            <li v-for="(item, i) in group.items" :key="i">
              <i class="fas fa-check text-success me-2"></i>{{ item }}
            </li>
          </ul>
        </div>
      </div>
      <div v-else class="text-muted">Información.</div>
    </div>
  </section>
</template>

<script setup lang="ts">
interface AmenitiesGroup {
  title: string
  items: string[]
}

defineProps<{ amenities: AmenitiesGroup[] | null | undefined }>()
</script>

<style scoped>
.amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
.amenities-group { background: var(--bg-secondary); border: 1px solid var(--border-light); border-radius: 12px; padding: 1rem; }
.amenities-list li { color: var(--text-secondary); margin-bottom: 0.375rem; }
</style>




