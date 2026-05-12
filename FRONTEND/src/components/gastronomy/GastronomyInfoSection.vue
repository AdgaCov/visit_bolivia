<template>
  <section class="gastronomy-info-section my-4">
    <div class="row g-4">
      <div class="col-12 col-lg-8">
        <div class="info-content">
          <h3 class="section-title mb-4">
            <i class="fas fa-info-circle me-2"></i>Información de {{ entityTypeLabel }}
          </h3>
          
          <!-- Debug temporal -->
          <div class="debug-info mb-3" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; font-size: 0.8rem;">
            <strong>Debug - Datos del restaurante:</strong>
            <pre>{{ JSON.stringify(props.restaurant, null, 2) }}</pre>
          </div>
          
          <div class="info-grid">
            <div class="info-item">
              <h4 class="info-label">
                <i class="fas fa-tag me-2"></i>Tipo
              </h4>
              <p class="info-value">{{ restaurantType }}</p>
            </div>
            
            <div class="info-item">
              <h4 class="info-label">
                <i class="fas fa-star me-2"></i>Rating
              </h4>
              <p class="info-value">{{ restaurantRating }}</p>
            </div>
            
            <div class="info-item">
              <h4 class="info-label">
                <i class="fas fa-dollar-sign me-2"></i>Precio
              </h4>
              <p class="info-value">{{ restaurantPrice }}</p>
            </div>
            
            <div class="info-item">
              <h4 class="info-label">
                <i class="fas fa-clock me-2"></i>Horarios
              </h4>
              <p class="info-value">{{ restaurantHours }}</p>
            </div>
            
            <div class="info-item">
              <h4 class="info-label">
                <i class="fas fa-phone me-2"></i>Contacto
              </h4>
              <p class="info-value">{{ restaurantContact }}</p>
            </div>
            
            <div class="info-item">
              <h4 class="info-label">
                <i class="fas fa-map-marker-alt me-2"></i>Dirección
              </h4>
              <p class="info-value">{{ restaurantAddress }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-12 col-lg-4">
        <div class="social-card">
          <h3 class="card-title mb-3">
            <i class="fas fa-share-alt me-2"></i>Síguenos
          </h3>
          
          <div v-if="hasSocialLinks" class="social-links">
            <a v-if="restaurantWebsite" :href="restaurantWebsite" target="_blank" rel="noopener noreferrer" class="social-link social-website">
              <i class="fas fa-globe me-2"></i>
              Sitio Web
            </a>
            <a v-if="restaurantFacebook" :href="restaurantFacebook" target="_blank" rel="noopener noreferrer" class="social-link social-facebook">
              <i class="fab fa-facebook-f me-2"></i>
              Facebook
            </a>
            <a v-if="restaurantInstagram" :href="restaurantInstagram" target="_blank" rel="noopener noreferrer" class="social-link social-instagram">
              <i class="fab fa-instagram me-2"></i>
              Instagram
            </a>
            <a v-if="restaurantWhatsapp" :href="restaurantWhatsapp" target="_blank" rel="noopener noreferrer" class="social-link social-whatsapp">
              <i class="fab fa-whatsapp me-2"></i>
              WhatsApp
            </a>
          </div>
          
          <div v-else class="no-social">
            <i class="fas fa-share-alt text-muted"></i>
            <p class="text-muted mb-0">No hay redes sociales disponibles</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'

// Props para recibir el restaurante
const props = defineProps<{
  restaurant: any
}>()

// Computed properties para mostrar los datos reales del restaurante
const restaurantType = computed(() => {
  console.log('GastronomyInfoSection - restaurant data:', props.restaurant)
  const type = props.restaurant?.cuisine_type || props.restaurant?.type || 'restaurante'
  return type.charAt(0).toUpperCase() + type.slice(1)
})

const restaurantRating = computed(() => {
  const rating = props.restaurant?.rating
  if (!rating) return 'No disponible'
  return `${rating}/5 ⭐`
})

const restaurantPrice = computed(() => {
  const price = props.restaurant?.price_range || props.restaurant?.price
  if (!price) return 'Consultar'
  return price
})

const restaurantHours = computed(() => {
  const hours = props.restaurant?.hours || props.restaurant?.opening_hours
  if (!hours) return 'Consultar horarios'
  return hours
})

const restaurantContact = computed(() => {
  const phone = props.restaurant?.phone || props.restaurant?.contact_phone || props.restaurant?.whatsapp
  if (!phone) return 'No disponible'
  return phone
})

const restaurantAddress = computed(() => {
  const address = props.restaurant?.address || 
                 props.restaurant?.location?.address || 
                 props.restaurant?.full_address ||
                 props.restaurant?.location?.name ||
                 props.restaurant?.place?.name
  if (!address) return 'Dirección no disponible'
  return address
})

// Redes sociales
const restaurantWebsite = computed(() => props.restaurant?.website || props.restaurant?.web_url)
const restaurantFacebook = computed(() => props.restaurant?.facebook_url || props.restaurant?.facebook)
const restaurantInstagram = computed(() => props.restaurant?.instagram_url || props.restaurant?.instagram)
const restaurantWhatsapp = computed(() => props.restaurant?.whatsapp || props.restaurant?.whatsapp_url)

const hasSocialLinks = computed(() => {
  return restaurantWebsite.value || restaurantFacebook.value || 
         restaurantInstagram.value || restaurantWhatsapp.value
})

// Etiqueta de tipo de entidad para títulos
const entityTypeLabel = computed(() => {
  const type = props.restaurant?.type || 'restaurante'
  return type.charAt(0).toUpperCase() + type.slice(1)
})
</script>

<style scoped>
.gastronomy-info-section {
  background: var(--bg-primary);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--shadow-sm);
}

.section-title {
  font-family: var(--font-family-base);
  font-size: 1.75rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  letter-spacing: -0.02em;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.info-item {
  padding: 1rem;
  background: var(--bg-secondary);
  border-radius: 12px;
  border-left: 4px solid var(--primary-blue);
}

.info-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: 1rem;
  color: var(--text-primary);
  margin: 0;
  font-weight: 500;
}

.social-card {
  background: var(--bg-secondary);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
  height: fit-content;
}

.card-title {
  font-family: var(--font-family-base);
  font-size: 1.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.social-links {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.social-link {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  background: var(--white);
  border-radius: 12px;
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.social-link:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  text-decoration: none;
}

.social-website:hover {
  border-color: var(--primary-blue);
  color: var(--primary-blue);
}

.social-facebook:hover {
  border-color: #1877F2;
  color: #1877F2;
}

.social-instagram:hover {
  border-color: #E4405F;
  color: #E4405F;
}

.social-whatsapp:hover {
  border-color: #25D366;
  color: #25D366;
}

.no-social {
  text-align: center;
  padding: 2rem 1rem;
}

.no-social i {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .gastronomy-info-section {
    padding: 1.5rem;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .section-title {
    font-size: 1.5rem;
  }
  
  .social-card {
    margin-top: 1rem;
  }
}
</style>
