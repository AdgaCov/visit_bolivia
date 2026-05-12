<template>
  <section v-if="items && items.length > 0" class="menu-section my-4">
    <div class="card p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h5 m-0"><i class="fas fa-list me-2"></i>Servicios </h3>
        <div class="d-flex gap-2">
          <input v-model="q" type="text" class="form-control form-control-sm" placeholder="Buscar servicio..." />
        </div>
      </div>

      <div v-if="filtered.length" class="menu-grid">
        <article v-for="(item, index) in visibleItems" :key="item.id" class="menu-card" @click="openItemModal(item)">
          <div class="menu-media">
            <img 
              :src="getDishImage(item, index)" 
              :alt="item.name" 
              @error="handleImageError"
            />
            <!-- Badge para tipo de item dinámico -->
            <div v-if="item.type" class="item-type-badge">{{ getItemTypeLabel(item.type) }}</div>
          </div>
          <div class="menu-content">
            <h4 class="h6 mb-1">{{ item.name }}</h4>
            <p class="text-muted mb-2 item-description">{{ item.description }}</p>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <strong class="price">{{ formatPrice(item.price) }}</strong>
              <div class="d-flex align-items-center gap-2">
                <!-- Categoría o tipo -->
                <span class="badge bg-light text-dark border">{{ item.category || getItemTypeLabel(item.type || '') }}</span>
              </div>
            </div>
            <!-- Botones de acción -->
            <div class="menu-actions">
              <!-- <button class="btn btn-whatsapp btn-sm" @click.stop="contactWhatsApp(item)" title="Contactar por WhatsApp">
                <i class="fab fa-whatsapp"></i>
              </button> -->
              <!-- <button class="btn btn-primary btn-sm" @click.stop="reserveItem(item)" title="Reservar">
                <i class="fas fa-calendar"></i>
              </button> -->
            </div>
          </div>
        </article>
      </div>
      <div v-if="canShowMore" class="text-center mt-3">
        <button class="btn btn-outline-primary btn-sm" @click="showMore">Ver más</button>
      </div>
    </div>
    
    <!-- Modal para mostrar detalles del item -->
    <div v-if="selectedItem" class="modal-overlay" @click="closeItemModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">{{ selectedItem.name }}</h3>
          <button class="modal-close" @click="closeItemModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="modal-image" v-if="selectedItem.image_url">
            <!-- <img :src="selectedItem.image_url" :alt="selectedItem.name" /> -->
            <img :src="$buildImg ? $buildImg(selectedItem.image_url) : selectedItem.image_url" :alt="selectedItem.name" />
          </div>
          <div class="modal-info">
            <p class="modal-description">{{ selectedItem.description }}</p>
            <div class="modal-details">
              <div class="detail-row">
                <strong>Precio:</strong> {{ formatPrice(selectedItem.price) }}
              </div>
              <div class="detail-row">
                <strong>Tipo:</strong> {{ getItemTypeLabel(selectedItem.type || '') }}
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-whatsapp" @click="contactWhatsApp(selectedItem)">
            <i class="fab fa-whatsapp me-2"></i>Contactar
          </button>
          <button class="btn btn-primary" @click="reserveItem(selectedItem)">
            <i class="fas fa-calendar me-2"></i>Reservar
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, getCurrentInstance } from 'vue'
const instance = getCurrentInstance()
const $buildImg = instance?.appContext.config.globalProperties.$buildImg
interface MenuItem { 
  id: string | number; 
  name: string; 
  description?: string; 
  price: string | number; 
  category?: string; 
  photo?: string;
  type?: string;
  review?: string;
  image_url?: string;
}

// Props para recibir items dinámicos y control de paginación
const props = defineProps<{ 
  pageSize?: number;
  items?: MenuItem[];
}>()

// Items SOLO desde props (base de datos) - SIN datos estáticos
const items = ref<MenuItem[]>(props.items || [])

const q = ref('')
const selectedItem = ref<MenuItem | null>(null)

const filtered = computed(() => {
  const needle = q.value.trim().toLowerCase()
  if (!needle) return items.value
  return items.value.filter(i =>
    (i.name || '').toLowerCase().includes(needle) ||
    (i.description || '').toLowerCase().includes(needle) ||
    (i.category || '').toLowerCase().includes(needle)
  )
})

const pageSize = computed(() => Math.max(1, props.pageSize ?? 8))
const visibleCount = ref<number>(pageSize.value)
const visibleItems = computed(() => filtered.value.slice(0, visibleCount.value))
const canShowMore = computed(() => filtered.value.length > visibleCount.value)
const showMore = () => { visibleCount.value = Math.min(filtered.value.length, visibleCount.value + pageSize.value) }

// const getDishImage = (item: MenuItem, itemIndex: number) => {
//   console.log('=== getDishImage called ===')
//   console.log('Item:', item)
//   console.log('ItemIndex:', itemIndex)
  
//   // SOLO usar image_url de la base de datos
//   if (item.image_url) {
//     console.log(`Using image_url from database for: ${item.name}`)
//     return item.image_url
//   }
  
//   // Si no hay image_url, usar placeholder
//   console.log(`No image_url found for: ${item.name}, using placeholder`)
//   return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0xNzUgMTI1SDIyNVYxNzVIMTc1VjEyNVoiIGZpbGw9IiNEOUQ5RDkiLz4KPHBhdGggZD0iTTE4NSAxMzVIMjE1VjE2NUgxODVWMTM1WiIgZmlsbD0iI0NDQ0NDQyIvPgo8dGV4dCB4PSIyMDAiIHk9IjIwMCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5OTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5TaW4gaW1hZ2VuPC90ZXh0Pgo8L3N2Zz4='
// }
const getDishImage = (item: MenuItem, itemIndex: number) => {
  console.log('=== getDishImage called ===')
  console.log('Item:', item)
  console.log('ItemIndex:', itemIndex)
  
  // SOLO usar image_url de la base de datos
  if (item.image_url) {
    console.log(`Using image_url from database for: ${item.name}`)
    // ✅ Procesar con $buildImg
    return $buildImg ? $buildImg(item.image_url) : item.image_url
  }
  
  // Si no hay image_url, usar placeholder
  console.log(`No image_url found for: ${item.name}, using placeholder`)
  return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0xNzUgMTI1SDIyNVYxNzVIMTc1VjEyNVoiIGZpbGw9IiNEOUQ5RDkiLz4KPHBhdGggZD0iTTE4NSAxMzVIMjE1VjE2NUgxODVWMTM1WiIgZmlsbD0iI0NDQ0NDQyIvPgo8dGV4dCB4PSIyMDAiIHk9IjIwMCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5OTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5TaW4gaW1hZ2VuPC90ZXh0Pgo8L3N2Zz4='
}
const handleImageError = (event: Event) => {
  console.log('=== Image Error ===')
  const target = event.target as HTMLImageElement
  console.log('Failed image URL:', target.src)
  // Fallback a una imagen placeholder si la imagen no se carga
  target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0xNzUgMTI1SDIyNVYxNzVIMTc1VjEyNVoiIGZpbGw9IiNEOUQ5RDkiLz4KPHBhdGggZD0iTTE4NSAxMzVIMjE1VjE2NUgxODVWMTM1WiIgZmlsbD0iI0NDQ0NDQyIvPgo8dGV4dCB4PSIyMDAiIHk9IjIwMCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5OTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZW4gbm8gZGlzcG9uaWJsZTwvdGV4dD4KPC9zdmc+'
  console.log('Using fallback image')
}

const formatPrice = (price: string | number) => {
  const n = Math.abs(typeof price === 'string' ? parseFloat(price) : price)
  if (Number.isNaN(n)) return 'Consultar'
  return `$${n.toFixed(0)}`
}

// Convertir tipo de item a etiqueta legible
const getItemTypeLabel = (type: string) => {
  const labels: Record<string, string> = {
    'room': 'Habitación',
    'service': 'Servicio',
    'offer': 'Oferta',
    'food': 'Comida',
    'drink': 'Bebida'
  }
  return labels[type] || 'Servicio'
}

// Función para contactar por WhatsApp
const contactWhatsApp = (item: MenuItem) => {
  const message = `Hola! Me interesa ${item.name} - ${formatPrice(item.price)}. ¿Podrían darme más información?`
  const whatsappUrl = `https://wa.me/59176765709?text=${encodeURIComponent(message)}`
  window.open(whatsappUrl, '_blank')
}

// Función para reservar item
const reserveItem = (item: MenuItem) => {
  const message = `Hola! Quiero reservar ${item.name} - ${formatPrice(item.price)}. ¿Está disponible?`
  const whatsappUrl = `https://wa.me/59176765709?text=${encodeURIComponent(message)}`
  window.open(whatsappUrl, '_blank')
}

// Funciones para el modal
const openItemModal = (item: MenuItem) => {
  selectedItem.value = item
}

const closeItemModal = () => {
  selectedItem.value = null
}
</script>

<style scoped>
.menu-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
  gap: 1.5rem; 
}

.menu-card { 
  background: var(--card-bg);
  border: 1px solid var(--border-light);
  border-radius: 12px;
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
  overflow: hidden;
  cursor: pointer;
  max-width: 320px;
}

.menu-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: var(--border-color);
}

.menu-media { 
  height: 180px; 
  overflow: hidden; 
  background: var(--light-gray); 
  position: relative; 
}

.menu-media img { 
  width: 100%; 
  height: 100%; 
  object-fit: cover; 
  display: block; 
  transition: transform 0.3s ease;
}

.menu-card:hover .menu-media img {
  transform: scale(1.05);
}

.menu-content { 
  padding: 1.25rem; 
}

.price { color: var(--primary-blue); }

/* Limitar descripción a 2 líneas */
.item-description {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
  max-height: 2.8em; /* 2 líneas * 1.4 line-height */
}

/* Badge de tipo de item */
.item-type-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: var(--white);
  color: var(--text-primary);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: var(--font-weight-medium);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-light);
}


/* Botones de acción */
.menu-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
  justify-content: center;
}

.btn-whatsapp {
  background-color: #25D366;
  border: none;
  color: white;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  box-shadow: var(--shadow-sm);
  font-size: 1rem;
}

.btn-whatsapp:hover {
  background-color: #1ea952;
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.btn-primary {
  background-color: var(--primary-blue);
  border: none;
  color: white;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  box-shadow: var(--shadow-sm);
  font-size: 1rem;
}

.btn-primary:hover {
  background-color: var(--primary-blue-dark);
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

/* Responsive */
@media (max-width: 768px) {
  .menu-grid {
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1rem;
  }
  
  .menu-media {
    height: 160px;
  }
  
  .menu-content {
    padding: 1rem;
  }
  
  .item-type-badge {
    font-size: 0.7rem;
    padding: 4px 8px;
    top: 8px;
    right: 8px;
  }
  
  .menu-actions {
    gap: 0.5rem;
  }
  
  .menu-actions .btn {
    width: 40px;
    height: 40px;
    font-size: 0.9rem;
  }
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: var(--card-bg);
  border: 1px solid var(--border-light);
  border-radius: 16px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.5rem 1rem;
  border-bottom: 1px solid var(--border-light);
}

.modal-title {
  font-size: 1.25rem;
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
  margin: 0;
  line-height: 1.4;
}

.modal-close {
  background: var(--light-gray);
  border: none;
  font-size: 1rem;
  color: var(--text-muted);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background-color: var(--border-color);
  color: var(--text-primary);
  transform: scale(1.05);
}

.modal-body {
  padding: 0 1.5rem 1.5rem;
}

.modal-image {
  width: 100%;
  height: 240px;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-sm);
}

.modal-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.modal-image:hover img {
  transform: scale(1.02);
}

.modal-description {
  font-size: 0.95rem;
  line-height: 1.6;
  color: var(--text-secondary);
  margin-bottom: 2rem;
  font-weight: var(--font-weight-regular);
}

.modal-details {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background: var(--bg-secondary);
  padding: 1.25rem;
  border-radius: 12px;
  border: 1px solid var(--border-light);
}

.detail-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.9rem;
  padding: 0.5rem 0;
}

.detail-row strong {
  color: var(--text-primary);
  font-weight: var(--font-weight-medium);
  min-width: 80px;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.modal-footer {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid var(--border-light);
  background-color: var(--white);
  border-radius: 0 0 16px 16px;
}

.modal-footer .btn {
  flex: 1;
  padding: 0.75rem 1rem;
  font-weight: var(--font-weight-medium);
  border-radius: 8px;
  transition: all 0.2s ease;
  border: none;
  box-shadow: var(--shadow-sm);
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.modal-footer .btn-whatsapp {
  background-color: #25D366;
  color: white;
}

.modal-footer .btn-whatsapp:hover {
  background-color: #1ea952;
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.modal-footer .btn-primary {
  background-color: var(--primary-blue);
  color: white;
}

.modal-footer .btn-primary:hover {
  background-color: var(--primary-blue-dark);
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

/* Responsive modal */
@media (max-width: 768px) {
  .modal-content {
    margin: 0.5rem;
    max-width: calc(100vw - 1rem);
    border-radius: 12px;
  }
  
  .modal-header {
    padding: 1.25rem 1.25rem 0.75rem;
  }
  
  .modal-title {
    font-size: 1.1rem;
  }
  
  .modal-body {
    padding: 0 1.25rem 1.25rem;
  }
  
  .modal-image {
    height: 200px;
    margin-bottom: 1.25rem;
  }
  
  .modal-description {
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }
  
  .modal-details {
    padding: 1rem;
    gap: 0.75rem;
  }
  
  .detail-row {
    font-size: 0.85rem;
    padding: 0.375rem 0;
  }
  
  .modal-footer {
    padding: 1.25rem;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .modal-footer .btn {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
  }
}
</style>
