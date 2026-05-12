<template>
  <div class="sticky-cta" v-if="show">
    <div class="container d-flex justify-content-between align-items-center">
      <strong class="restaurant-name">{{ title }}</strong>
      <div class="d-flex align-items-center gap-2">
        <a :href="whatsAppUrl" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm btn-cta">
          <i class="fab fa-whatsapp me-1 icon-small"></i>Reservar mesa
        </a>
        <button class="btn btn-primary btn-sm btn-cta">Llamar</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ title: string; phoneOrWhatsapp?: string | null; show?: boolean }>()

const whatsAppUrl = computed(() => {
  const phone = (props.phoneOrWhatsapp || '').replace(/[^\d+]/g, '')
  const msg = encodeURIComponent(`Hola, quisiera reservar mesa en ${props.title}.`)
  return phone ? `https://wa.me/${phone}?text=${msg}` : `https://wa.me/?text=${msg}`
})

const show = computed(() => props.show !== false)
</script>

<style scoped>
.sticky-cta { position: sticky; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(6px); border-top: 1px solid var(--border-light); padding: 0.5rem 0; box-shadow: 0 -4px 12px rgba(0,0,0,0.06); }
.restaurant-name { font-weight: 600; }
.btn-cta { padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 600; }
.icon-small { font-size: 0.9rem; }
</style>


