<template>
  <section class="accommodation-details my-4">
    <div class="row g-4">
      <div class="col-12 col-lg-7">
        <h2 class="visually-hidden">Descripción del hotel</h2>
        <p class="lead">{{ accommodationDescription }}</p>
        <div class="accommodation-info">
          <div class="info-item mb-3">
            <h4 class="h6 mb-2">
              <i class="fas fa-info-circle me-2"></i>Información del Hotel
            </h4>
            <p class="mb-2">
              <strong>Tipo:</strong> {{ accommodationType }}
            </p>
            <p class="mb-2">
              <strong>Precio:</strong> {{ accommodationPrice }}
            </p>
            <p class="mb-0">
              <strong>Capacidad:</strong> {{ accommodationCapacity }}
            </p>
          </div>
          
          <div class="info-item mb-3">
            <h4 class="h6 mb-2">
              <i class="fas fa-map-marker-alt me-2"></i>Ubicación
            </h4>
            <p class="mb-2">
              <strong>Lugar:</strong> {{ accommodationLocation }}
            </p>
            <p class="mb-2">
              <strong>Dirección:</strong> {{ accommodationAddress }}
            </p>
            <p class="mb-0">
              <strong>Horarios:</strong> {{ accommodationHours }}
            </p>
          </div>
          
          <div class="info-item">
            <h4 class="h6 mb-2">
              <i class="fas fa-phone me-2"></i>Contacto
            </h4>
            <p class="mb-2">
              <strong>Teléfono:</strong> {{ accommodationContact }}
            </p>
            <div class="social-links mt-3" v-if="hasSocialLinks">
              <h5 class="h6 mb-2 text-muted">Síguenos</h5>
              <div class="d-flex flex-wrap gap-2">
                <a v-if="accommodationWebsite" :href="accommodationWebsite" target="_blank" rel="noopener noreferrer" class="social-link social-website">
                  <i class="fas fa-globe me-1"></i>
                  Sitio Web
                </a>
                <a v-if="accommodationFacebook" :href="accommodationFacebook" target="_blank" rel="noopener noreferrer" class="social-link social-facebook">
                  <i class="fab fa-facebook-f me-1"></i>
                  Facebook
                </a>
                <a v-if="accommodationInstagram" :href="accommodationInstagram" target="_blank" rel="noopener noreferrer" class="social-link social-instagram">
                  <i class="fab fa-instagram me-1"></i>
                  Instagram
                </a>
                <a v-if="accommodationWhatsapp" :href="accommodationWhatsapp" target="_blank" rel="noopener noreferrer" class="social-link social-whatsapp">
                  <i class="fab fa-whatsapp me-1"></i>
                  WhatsApp
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-5">
        <div class="card p-4 mb-3" style="background: var(--bg-secondary);">
          <h3 class="h5 mb-3">
            <i class="fas fa-bed me-2"></i>Información del Hotel
          </h3>
          <div class="accommodation-info">
            <p class="mb-2">
              <span class="text-primary">{{ accommodationType }}</span> - <span class="text-primary">{{ accommodationPrice }}</span>
            </p>
            <p class="mb-0">
              <!-- <strong>Fin:</strong><br> -->
              <!-- <span class="text-primary">{{ endDate }}</span> -->
            </p>
          </div>
        </div>

        <div class="card p-4 mb-3">
          <h3 class="h5 mb-3">
            <i class="fas fa-map-marker-alt me-2"></i>Ubicación
          </h3>
          <p class="mb-2">
            <strong>{{ accommodationLocation }}</strong>
          </p>
          <p class="mb-0 text-muted">
            <i class="fas fa-map-pin me-1"></i>{{ accommodationAddress }}
          </p>
        </div>

        <div class="card p-4">
          <h3 class="h5 mb-3">
            <i class="fas fa-info-circle me-2"></i>Información Adicional
          </h3>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-tag me-2"></i>
            <span><strong>Tipo:</strong> {{ accommodationType }}</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-dollar-sign me-2"></i>
            <span><strong>Precio:</strong> {{ accommodationPrice }}</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-phone me-2"></i>
            <span><strong>Contacto:</strong> {{ accommodationContact }}</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <i class="fas fa-clock me-2"></i>
            <span><strong>Horarios:</strong> {{ accommodationHours }}</span>
          </div>
          
          <!-- Redes Sociales en la sidebar -->
          <div v-if="hasSocialLinks" class="mt-3 pt-3 border-top">
            <h5 class="h6 mb-3 text-muted">Síguenos</h5>
            <div class="d-flex flex-wrap gap-2">
              <a v-if="accommodationWebsite" :href="accommodationWebsite" target="_blank" rel="noopener noreferrer" class="social-link social-website">
                <i class="fas fa-globe me-1"></i>
                Web
              </a>
              <a v-if="accommodationFacebook" :href="accommodationFacebook" target="_blank" rel="noopener noreferrer" class="social-link social-facebook">
                <i class="fab fa-facebook-f me-1"></i>
                FB
              </a>
              <a v-if="accommodationInstagram" :href="accommodationInstagram" target="_blank" rel="noopener noreferrer" class="social-link social-instagram">
                <i class="fab fa-instagram me-1"></i>
                IG
              </a>
              <a v-if="accommodationWhatsapp" :href="accommodationWhatsapp" target="_blank" rel="noopener noreferrer" class="social-link social-whatsapp">
                <i class="fab fa-whatsapp me-1"></i>
                WA
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { AccommodationApi } from '@/types'

// Props para recibir el hotel
const props = defineProps<{
  accommodation: AccommodationApi
}>()

// Computed properties para mostrar los datos reales del hotel
const accommodationDescription = computed(() => {
  return props.accommodation?.description || 'Descripción no disponible'
})

const accommodationType = computed(() => {
  const type = props.accommodation?.room_type || props.accommodation?.badge || 'hotel'
  return type.charAt(0).toUpperCase() + type.slice(1)
})

const accommodationPrice = computed(() => {
  const price = props.accommodation?.price_per_night
  if (!price || price === '0.00') {
    return 'Consultar precio'
  }
  const numPrice = Math.abs(parseFloat(price))
  if (isNaN(numPrice)) return 'Consultar precio'
  return `$${numPrice.toFixed(0)}/noche`
})

const accommodationCapacity = computed(() => {
  const capacity = props.accommodation?.capacity
  if (!capacity || capacity === 0) {
    return 'Consultar'
  }
  return `${capacity} personas`
})

const accommodationLocation = computed(() => {
  console.log('locacion ',props.accommodation?.whatsapp);
  return  props.accommodation?.location || 'Ubicación no disponible'
})

const accommodationAddress = computed(() => {
  return props.accommodation?.place?.address || 'Dirección no disponible'
})

const accommodationHours = computed(() => {
  return props.accommodation?.place?.opening_hours || 'Horarios no disponibles'
})

const accommodationContact = computed(() => {
  return props.accommodation?.whatsapp || 'Contacto no disponible'
})

// Computed properties para redes sociales
const accommodationWebsite = computed(() => {
  return props.accommodation?.website || props.accommodation?.link || null
})

const accommodationFacebook = computed(() => {
  return props.accommodation?.facebook || null
})

const accommodationInstagram = computed(() => {
  return props.accommodation?.instagram || null
})

const accommodationWhatsapp = computed(() => {
  return props.accommodation?.whatsapp || null
})

const hasSocialLinks = computed(() => {
  return accommodationWebsite.value || accommodationFacebook.value || accommodationInstagram.value || accommodationWhatsapp.value
})
</script>

<style scoped>
/* Estilo minimalista usando variables CSS del sistema */
.lead {
  font-size: 1.125rem;
  font-weight: var(--font-weight-regular);
  line-height: 1.6;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
}

.visually-hidden {
  position: absolute !important;
  height: 1px; width: 1px; overflow: hidden; clip: rect(1px, 1px, 1px, 1px);
}

.accommodation-info {
  margin-top: 2rem;
}

.info-item {
  background: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid var(--border-light);
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.info-item:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-color: var(--border-color);
}

.info-item h4 {
  color: var(--text-primary);
  font-weight: var(--font-weight-semibold);
  font-size: 1rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
}

.info-item h4 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
  color: var(--text-muted);
}

.info-item p {
  color: var(--text-secondary);
  font-weight: var(--font-weight-regular);
  margin-bottom: 0.5rem;
}

.info-item p strong {
  color: var(--text-primary);
  font-weight: var(--font-weight-medium);
}

.accommodation-info {
  background: var(--white);
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
}

.accommodation-info p {
  margin-bottom: 0.75rem;
}

.accommodation-info .text-primary {
  color: var(--primary-blue) !important;
  font-weight: var(--font-weight-medium);
}

/* Cards minimalistas */
.card {
  background: var(--card-bg);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
  overflow: hidden;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: var(--border-color);
}

.card h3 {
  color: var(--text-primary);
  font-weight: var(--font-weight-semibold);
  font-size: 1.1rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
}

.card h3 i {
  margin-right: 0.5rem;
  color: var(--text-muted);
}

.card p {
  color: var(--text-secondary);
  font-weight: var(--font-weight-regular);
  margin-bottom: 0.5rem;
}

.card p strong {
  color: var(--text-primary);
  font-weight: var(--font-weight-medium);
}

.card .text-muted {
  color: var(--text-muted) !important;
  font-size: 0.9rem;
}

/* Iconos con colores neutros */
.card .d-flex i {
  color: var(--text-muted);
  font-size: 0.9rem;
}

.card .text-muted i {
  color: var(--text-muted);
}

/* Colores del sistema */
.text-primary { color: var(--primary-blue) !important; }

/* Estilos para enlaces de redes sociales */
.social-links {
  margin-top: 1rem;
}

.social-link {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.3s ease;
  border: 1px solid transparent;
  background: var(--bg-secondary);
  color: var(--text-secondary);
}

.social-link:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  text-decoration: none;
}

.social-website:hover {
  background: var(--primary-blue);
  color: var(--white);
  border-color: var(--primary-blue);
}

.social-facebook:hover {
  background: #1877f2;
  color: var(--white);
  border-color: #1877f2;
}

.social-instagram:hover {
  background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
  color: var(--white);
  border-color: #bc1888;
}

.social-whatsapp:hover {
  background: #25d366;
  color: var(--white);
  border-color: #25d366;
}

.social-link i {
  font-size: 0.9rem;
}

/* Responsive minimalista */
@media (max-width: 768px) {
  .info-item {
    padding: 1rem;
    margin-bottom: 0.75rem;
  }
  
  .accommodation-info {
    padding: 0.75rem;
  }
  
  .card {
    margin-bottom: 1rem;
  }
  
  .lead {
    font-size: 1rem;
    margin-bottom: 1rem;
  }
  
  .info-item h4 {
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
  }
}
</style>
