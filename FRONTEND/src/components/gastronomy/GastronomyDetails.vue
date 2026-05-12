<template></template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ restaurant: any }>()

const restaurantName = computed(() =>
  props.restaurant?.name || props.restaurant?.title || ''
)

const restaurantDescription = computed(() =>
  props.restaurant?.description || ''
)

const restaurantType = computed(() => {
  const raw = props.restaurant?.cuisine_type || props.restaurant?.type || 'restaurante'
  const t = String(raw).trim()
  return t ? t.charAt(0).toUpperCase() + t.slice(1) : 'Restaurante'
})

const entityTypeLabel = computed(() => {
  const t = String(props.restaurant?.type || props.restaurant?.cuisine_type || 'restaurante').toLowerCase()
  const map: Record<string, string> = {
    restaurant: 'Restaurante',
    accommodation: 'Hotel',
    hotel: 'Hotel',
    event: 'Evento',
    museum: '',
    city: 'Ciudad',
    attraction: 'Atractivo',
    place: 'Lugar'
  }
  return map[t] || (t ? t.replace(/^./, (c: string) => c.toUpperCase()) : 'Lugar')
})

const restaurantPrice = computed(() => props.restaurant?.price_per_night || 'Consultar')
const restaurantRating = computed(() => {
  const r = props.restaurant?.rating
  return r ? `${r}/5 ⭐` : 'Sin rating'
})
const restaurantLocation = computed(() => props.restaurant?.location || 'No especificado')
const restaurantAddress  = computed(() => props.restaurant.address || 'Sin dirección')
const restaurantHours    = computed(() => props.restaurant?.place?.opening_hours || 'No disponible')
const restaurantContact  = computed(() => props.restaurant?.place?.contact_info || props.restaurant?.phone || 'No disponible')

const restaurantWebsite   = computed(() => props.restaurant?.website || props.restaurant?.link || null)
const restaurantFacebook  = computed(() => props.restaurant?.facebook || null)
const restaurantInstagram = computed(() => props.restaurant?.instagram || null)
const restaurantWhatsapp  = computed(() => props.restaurant?.whatsapp || null)

const hasSocialLinks = computed(() =>
  !!(restaurantWebsite.value || restaurantFacebook.value || restaurantInstagram.value || restaurantWhatsapp.value)
)

// Helpers
const sanitizeDigits = (v?: string) => (v || '').replace(/[^\d]/g, '')

const formattedWhatsapp = computed(() => {
  const num = sanitizeDigits(restaurantWhatsapp.value as string)
  return num ? `https://wa.me/${num}` : null
})

const formattedPhone = computed(() => {
  const num = sanitizeDigits(restaurantContact.value as string)
  return num ? `tel:${num}` : null
})
</script>

<style scoped>
/* ======= Tokens (fallbacks si no existen variables globales) ======= */
:host, .gastronomy-details {
  --gd-bg: var(--bg, #0b0c0f);
  --gd-surface: var(--card-bg, #0f1115);
  --gd-surface-2: var(--bg-secondary, #0d0f13);
  --gd-border: var(--border-light, rgba(255,255,255,0.06));
  --gd-border-strong: var(--border-color, rgba(255,255,255,0.12));
  --gd-text: var(--text-primary, #e6e8eb);
  --gd-text-2: var(--text-secondary, #a8b0ba);
  --gd-muted: var(--text-muted, #8a93a3);
  --gd-accent: var(--primary-blue, #4ea8ff);
  --gd-shadow: var(--shadow-sm, 0 6px 18px rgba(0,0,0,.25));
  --gd-shadow-lg: var(--shadow-lg, 0 12px 32px rgba(0,0,0,.35));
  --gd-radius: 16px;
  --gd-radius-sm: 12px;
  --gd-gap: 20px;
}

.gastronomy-details {
  color: var(--gd-text);
}

/* ======= Grid ======= */
.gd-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
  gap: clamp(16px, 2.5vw, 28px);
  align-items: stretch;      /* iguala altura de filas */
  height: 100%;
}

/* ======= Igualar alturas en columnas ======= */
.gd-main,
.gd-aside {
  display: flex;
  flex-direction: column;
  min-height: 100%;
}

/* ======= Header ======= */
.gd-header { margin-bottom: 8px; }
.gd-title {
  font-weight: 600;
  letter-spacing: -0.02em;
  font-size: clamp(1.5rem, 2.6vw, 2.2rem);
  line-height: 1.15;
  margin: 0 0 6px 0;
}
.gd-lead {
  color: var(--gd-text-2);
  font-size: 1rem;
  line-height: 1.7;
  margin: 0;
}

/* ======= Cards ======= */
.gd-card {
  background: linear-gradient(180deg, rgba(255,255,255,0.02), transparent);
  backdrop-filter: saturate(1.2) blur(6px);
  border: 1px solid var(--gd-border);
  border-radius: var(--gd-radius);
  box-shadow: var(--gd-shadow);
  padding: clamp(16px, 2vw, 22px);
  transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
  margin-top: 16px;
}

.gd-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--gd-shadow-lg);
  border-color: var(--gd-border-strong);
}

.gd-card__title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  margin: 0 0 10px 0;
  color: var(--gd-text);
}
.gd-card__title i { color: var(--gd-muted); font-size: .95rem; }

.gd-card__footer {
  margin-top: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Hace que este card crezca para igualar altura con el aside */
.gd-card--grow {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.gd-card--grow > .gd-card__footer {
  margin-top: auto; /* empuja footer al fondo cuando crece */
}

/* ======= Definition list ======= */
.gd-list { display: grid; gap: 10px; margin: 0; }
.gd-row {
  display: grid;
  grid-template-columns: 140px 1fr;
  align-items: start;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px dashed var(--gd-border);
}
.gd-row:last-child { border-bottom: none; }
dt {
  color: var(--gd-muted);
  font-weight: 500;
  font-size: .9rem;
}
dd {
  margin: 0;
  color: var(--gd-text);
  font-weight: 500;
}

.gd-muted { color: var(--gd-text-2); }

/* ======= Aside ======= */
.gd-aside { position: relative; }

/* El sticky ocupa toda la altura de la columna y permite respiración interna */
.gd-sticky {
  position: sticky;
  top: 16px;
  flex: 1;                     /* llena la columna */
  display: flex;
  flex-direction: column;
  align-self: stretch;         /* mejora compatibilidad con sticky+flex */
}

.gd-sticky-spacer {
  margin-top: auto;            /* empuja el contenido hacia arriba si sobra altura */
}

.gd-summary {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}
.gd-chip {
  display: inline-flex;
  align-items: center;
  padding: 6px 10px;
  border-radius: 999px;
  border: 1px solid var(--gd-border);
  font-size: 0.85rem;
  background: var(--gd-surface-2);
}
.gd-chip--accent {
  border-color: color-mix(in oklab, var(--gd-accent), transparent 70%);
  background: color-mix(in oklab, var(--gd-accent), transparent 90%);
  color: var(--gd-accent);
}
.gd-dot {
  width: 4px; height: 4px; border-radius: 50%;
  background: var(--gd-border-strong);
  display: inline-block;
}

.gd-divider {
  height: 1px;
  background: var(--gd-border);
  margin: 14px 0;
  border-radius: 1px;
}

.gd-mini {
  display: grid;
  grid-template-columns: 20px 1fr;
  gap: 10px;
  align-items: start;
  color: var(--gd-text);
}
.gd-mini i { color: var(--gd-muted); }

.stack { display: grid; gap: 2px; }

/* ======= Social ======= */
.gd-social { margin-top: 14px; }
.gd-subtitle {
  display: flex; align-items: center; gap: 8px;
  font-size: .9rem; font-weight: 600; color: var(--gd-muted); margin: 0 0 8px 0;
}
.gd-social__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}
@media (max-width: 420px) {
  .gd-social__grid { grid-template-columns: 1fr; }
}

.gd-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid var(--gd-border);
  background: var(--gd-surface-2);
  color: var(--gd-text);
  text-decoration: none;
  font-weight: 600;
  font-size: .9rem;
  transition: transform .2s ease, border-color .2s ease, background .2s ease;
}
.gd-btn:hover {
  transform: translateY(-1px);
  border-color: var(--gd-border-strong);
  background: linear-gradient(180deg, rgba(255,255,255,0.03), transparent);
}

/* ======= Links ======= */
.gd-link {
  color: var(--gd-accent);
  text-decoration: none;
  border-bottom: 1px dashed color-mix(in oklab, var(--gd-accent), transparent 60%);
}
.gd-link:hover { border-bottom-style: solid; }

/* ======= Accesibilidad ======= */
.gd-card :focus-visible,
.gd-btn:focus-visible,
.gd-link:focus-visible {
  outline: 2px solid color-mix(in oklab, var(--gd-accent), white 20%);
  outline-offset: 2px;
  border-radius: 10px;
}

/* ======= Responsive ======= */
@media (max-width: 992px) {
  .gd-grid { grid-template-columns: 1fr; }
  .gd-sticky { position: static; top: 0; }
  .gd-card--grow { flex: initial; }
}
</style>
