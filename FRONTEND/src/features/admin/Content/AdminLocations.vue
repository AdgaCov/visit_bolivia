<template>
  <div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1"><i class="fas fa-map-marker-alt text-primary me-2"></i>Locaciones</h1>
        <p class="text-muted mb-0">Hoteles, eventos, atractivos, ciudades, museos, restaurantes, etc.</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div v-if="!isSuperAdmin && planLimits && planLimits.max_locations !== null" class="text-muted small">
          <i class="fas fa-info-circle"></i>
          {{ locationsLimitMessage }}
        </div>
        <div v-else-if="!isSuperAdmin && loadingPlan" class="text-muted small">
          <i class="fas fa-spinner fa-spin"></i>
          Cargando límites del plan...
        </div>
        <button 
          class="btn btn-primary" 
          @click="openNew()"
          :disabled="!canCreateNewLocation"
          :title="!canCreateNewLocation ? 'Has alcanzado el límite de locaciones permitidas en tu plan' : ''"
        >
          <i class="fas fa-plus me-2"></i>Nueva locación
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6"><input class="form-control" placeholder="Buscar por nombre..." v-model="search"></div>
          <div class="col-md-3">
            <select class="form-select" v-model="type">
              <option value="">Todos los tipos</option>
              <option v-for="opt in locationTypeOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th style="width:80px;">Imagen</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Departamento</th>
                <th>Dirección</th>
                <th>Creado</th>
                <th class="text-end" style="width:110px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="loc in filtered" :key="loc.id">
                <td>
                  <img :src="loc.cover" alt="" style="width:64px;height:40px;object-fit:cover;border-radius:6px;background:var(--light-gray);">
                </td>
                <td>
                  <strong>{{ loc.name }}</strong>
                  <div class="text-muted small" v-if="loc.description">{{ truncate(loc.description, 90) }}</div>
                </td>
                <td><span class="badge bg-light text-dark">{{ loc.type }}</span></td>
                <td>{{ loc.department || '-' }}</td>
                <td><span class="text-muted small">{{ loc.address || '-' }}</span></td>
                <td><span class="text-muted small">{{ formatDate(loc.created_at) }}</span></td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" @click="openImages(loc)" title="Imágenes">
                      <i class="fas fa-images"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary" @click="openEdit(loc)" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(loc.id)" title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="loading" class="text-center p-4">
          <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
          <span class="ms-2">Cargando...</span>
        </div>
        <div v-if="!loading && filtered.length === 0" class="text-center p-4 text-muted">Sin resultados</div>
        <div class="card-footer bg-white border-top" v-if="hasMore || items.length > 0">
          <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Mostrando {{ items.length }} de {{ pagination.count || items.length }}</small>
            <button class="btn btn-outline-primary btn-sm" @click="loadMore" :disabled="loadingMore || !hasMore">
              <span v-if="loadingMore" class="spinner-border spinner-border-sm me-2"></span>
              Cargar más
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="show" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ form.id ? 'Editar' : 'Nueva' }} locación</h5>
            <button type="button" class="btn-close" @click="close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-4">
              <div class="col-lg-8">
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Nombre</label>
                    <input class="form-control" v-model="form.name">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" v-model="form.type">
                      <option value="">Selecciona un tipo</option>
                      <option v-for="opt in locationTypeOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Departamento</label>
                    <select class="form-select" v-model="form.department_id">
                      <option value="">Selecciona un departamento</option>
                      <option v-for="dept in departments" :key="dept.id" :value="String(dept.id)">{{ dept.name }}</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Latitud</label>
                    <input class="form-control" v-model="form.latitude" placeholder="-19.05...">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Longitud</label>
                    <input class="form-control" v-model="form.longitude" placeholder="-65.25...">
                  </div>
                  <div class="col-12 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" @click="openMapPicker">
                      <i class="fas fa-map-marked-alt me-2"></i>Seleccionar en mapa
                    </button>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input class="form-control" v-model="form.address">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" rows="6" v-model="form.description"></textarea>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                  <div class="card-body">
                    <label class="form-label">Portada</label>
                    <div class="ratio ratio-16x9 mb-2" style="border-radius: 8px; overflow: hidden; background: var(--bg-secondary);">
                      <img v-if="form.cover" :src="form.cover" alt="" style="width:100%;height:100%;object-fit:cover;">
                      <div v-else class="d-flex align-items-center justify-content-center text-muted">Sin imagen</div>
                    </div>
                    <div class="d-grid gap-2">
                      <input type="file" accept="image/*" class="form-control" @change="onCover">
                      <input class="form-control" placeholder="o pega URL de imagen" v-model="form.cover" @input="onCoverUrlInput">
                      <button class="btn btn-outline-secondary" type="button" @click="clearCover">Quitar imagen</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="close" :disabled="formLoading">Cancelar</button>
            <button class="btn btn-primary" @click="save" :disabled="formLoading">
              <span v-if="formLoading" class="spinner-border spinner-border-sm me-2"></span>
              Guardar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Images modal -->
    <div v-if="showImages" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Imágenes — {{ imagesLocationName }}</h5>
            <button type="button" class="btn-close" @click="showImages=false"></button>
          </div>
          <div class="modal-body">
            <div v-if="imagesLoading" class="text-center p-4">
              <span class="spinner-border spinner-border-sm"></span>
              <span class="ms-2">Cargando imágenes…</span>
            </div>
            <div v-else>
              <div v-if="images.length === 0" class="text-muted text-center">Sin imágenes</div>
              <div class="row g-3" v-else>
                <div class="col-6 col-md-4" v-for="img in images" :key="img.id">
                  <div class="position-relative" style="border-radius:8px;overflow:hidden;background:var(--bg-secondary);">
                    <img :src="img.url" alt="" style="width:100%;height:140px;object-fit:cover;">
                    <span v-if="img.is_main" class="badge bg-primary position-absolute top-0 start-0 m-2">Principal</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showImages=false">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Map Picker modal -->
    <div v-if="showMapPicker" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-map-marker-alt text-primary me-2"></i>Seleccionar ubicación en el mapa</h5>
            <button type="button" class="btn-close" @click="closeMapPicker"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-2">Haz click en el mapa para establecer la ubicación. Se llenarán automáticamente los campos de latitud y longitud.</p>
            <div ref="mapContainer" style="height: 420px; border-radius: 8px; overflow: hidden; background: var(--bg-secondary);"></div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeMapPicker">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import adminLocationsService from '@/services/admin/locations.admin.service'
import apiClient from '@/services/api'
import API_ENDPOINTS from '@/services/endpoints'
import { usePlanLimits } from '@/composables/usePlanLimits'
import { useAuthStore } from '@/store/auth'
import { patchLeafletForVueLifecycle } from '@/utils/leafletSafety'

export default {
  name: 'AdminLocations',
  setup() {
    const planLimits = usePlanLimits()
    const authStore = useAuthStore()
    return {
      ...planLimits,
      authStore
    }
  },
  data() {
    return {
      loading: false,
      loadingMore: false,
      search: '',
      type: '',
      locationTypeOptions: [
        { value: 'Internacional', label: 'Internacional' },
        { value: 'Local', label: 'Local' },
        { value: 'Nacional', label: 'Nacional' },
        { value: 'Regional', label: 'Regional' },
      ],
      items: [],
      pagination: { limit: 50, offset: 0, count: 0 },
      hasMore: true,
      // plan limits
      userLocationsCount: 0,
      // modal
      show: false,
      formLoading: false,
      // departments
      departments: [],
      // images modal
      showImages: false,
      imagesLoading: false,
      images: [],
      imagesLocationName: '',
      // map picker
      showMapPicker: false,
      leafletLoaded: false,
      mapInstance: null,
      mapMarker: null,
      form: {
        id: 0,
        name: '',
        description: '',
        type: '',
        address: '',
        department_id: '',
        latitude: '',
        longitude: '',
        cover: '',
        coverFile: null,
        coverChanged: false,
      }
    }
  },
  computed: {
    filtered() {
      return this.items.filter(l =>
        (!this.search || (l.name || '').toLowerCase().includes(this.search.toLowerCase())) &&
        (!this.type || l.type === this.type)
      )
    },
    canCreateNewLocation() {
      if (this.isSuperAdmin) return true
      // Si está cargando el plan, permitir crear (se validará después)
      if (this.loadingPlan || this.planLimits.max_locations === null) return true
      return this.canCreateLocation(this.userLocationsCount)
    },
    locationsLimitMessage() {
      if (this.isSuperAdmin) return ''
      return this.getLimitMessage('locations', this.userLocationsCount)
    }
  },
  watch: {
    type() { this.reload() }
  },
  async mounted() {
    // Cargar el plan del usuario si solo tiene plan_id
    if (!this.isSuperAdmin && this.currentUser?.plan_id && !this.userPlan) {
      await this.loadUserPlan()
    }
    await this.loadDepartments()
    this.reload()
  },
  methods: {
    async loadDepartments() {
      try {
        const res = await apiClient.get(API_ENDPOINTS.DEPARTMENTS.BASE)
        if (res && res.success && Array.isArray(res.data)) {
          this.departments = res.data.map(dept => ({
            id: dept.id,
            name: dept.name
          }))
        } else {
          this.departments = []
        }
      } catch (e) {
        console.error('Error cargando departamentos:', e)
        this.departments = []
      }
    },
    async reload() {
      this.pagination.offset = 0
      this.items = []
      this.hasMore = true
      await this.load(true)
    },
    async openImages(loc) {
      this.showImages = true
      this.imagesLoading = true
      this.images = []
      this.imagesLocationName = loc.name
      try {
        const res = await adminLocationsService.get(loc.id)
        const l = res?.data || {}
        const list = Array.isArray(l.images) ? l.images : []
        this.images = list.map(img => ({
          id: img.id,
          url: this.$buildImg ? this.$buildImg(img.image_url) : img.image_url,
          is_main: !!img.is_main
        }))
      } catch (e) {
        console.error('Error cargando imágenes de la locación', e)
      } finally {
        this.imagesLoading = false
      }
    },
    async load(reset = false) {
      try {
        if (reset) this.loading = true; else this.loadingMore = true
        
        const currentUser = this.authStore.currentUser
        const userId = currentUser?.id
        
        // Si es super_admin (role_id = 1), listar todas las locaciones
        // Si no, listar solo las del usuario actual
        let res
        if (this.isSuperAdmin) {
          res = await adminLocationsService.list({
            limit: this.pagination.limit,
            offset: this.pagination.offset,
            type: this.type || undefined
          })
        } else {
          // Usar endpoint específico para obtener locaciones del usuario
          if (!userId) {
            console.error('No se pudo obtener el ID del usuario')
            this.items = []
            return
          }
          res = await adminLocationsService.listByUser(userId, {
            limit: this.pagination.limit,
            offset: this.pagination.offset,
            type: this.type || undefined
          })
        }
        
        const data = res?.data || []
        const mapped = data.map(loc => ({
          id: loc.id,
          name: loc.name,
          description: loc.description || '',
          type: loc.type,
          department: loc.department?.name || '',
          address: loc.address || '',
          created_at: loc.created_at || '',
          user_id: loc.user_id || loc.created_by || userId, // Para filtrar por usuario
          cover: this.$buildImg
            ? this.$buildImg((loc.images && (loc.images.find(i => i.is_main) || loc.images[0])?.image_url) || loc.department?.image_url || '')
            : ((loc.images && (loc.images.find(i => i.is_main) || loc.images[0])?.image_url) || loc.department?.image_url || '')
        }))
        if (reset) this.items = mapped; else this.items.push(...mapped)
        
        // Contar locaciones del usuario actual
        this.updateUserLocationsCount()
        if (res?.pagination) {
          this.pagination.count = res.pagination.count
          this.hasMore = (this.pagination.offset + data.length) < res.pagination.count
        } else {
          this.hasMore = data.length === this.pagination.limit
        }
      } catch (e) {
        console.error('Error cargando locaciones', e)
      } finally {
        this.loading = false
        this.loadingMore = false
      }
    },
    async loadMore() {
      if (!this.hasMore || this.loadingMore) return
      this.pagination.offset += this.pagination.limit
      await this.load(false)
    },
    async openMapPicker() {
      this.showMapPicker = true
      await this.$nextTick()
      await this.ensureLeaflet()
      this.initMap()
    },
    closeMapPicker() {
      this.showMapPicker = false
      // Destroy map to avoid stale container references on next open
      try {
        if (this.mapMarker) {
          try { this.mapMarker.closePopup && this.mapMarker.closePopup() } catch {}
          try { this.mapMarker.unbindPopup && this.mapMarker.unbindPopup() } catch {}
          try { this.mapMarker.closeTooltip && this.mapMarker.closeTooltip() } catch {}
          try { this.mapMarker.unbindTooltip && this.mapMarker.unbindTooltip() } catch {}
          try { this.mapMarker.off && this.mapMarker.off() } catch {}
          try { this.mapMarker.remove && this.mapMarker.remove() } catch {}
        }
        if (this.mapInstance) {
          try { this.mapInstance.off && this.mapInstance.off() } catch {}
          try { this.mapInstance.stop && this.mapInstance.stop() } catch {}
          this.mapInstance.remove()
        }
      } catch {}
      this.mapInstance = null
      this.mapMarker = null
    },
    openNew() {
      // Validar límite antes de abrir el formulario
      if (!this.canCreateNewLocation) {
        const message = this.getLimitMessage('locations', this.userLocationsCount)
        alert(message || 'Has alcanzado el límite de locaciones permitidas en tu plan.')
        return
      }
      
      this.show = true
      this.formLoading = false
      this.form = {
        id: 0,
        name: '',
        description: '',
        type: '',
        address: '',
        department_id: '',
        latitude: '',
        longitude: '',
        cover: '',
        coverFile: null,
        coverChanged: false
      }
    },
    async openEdit(loc) {
      this.show = true
      this.formLoading = true
      try {
        const res = await adminLocationsService.get(loc.id)
        const l = res?.data || {}
        const mainImg = (l.images && (l.images.find(i => i.is_main) || l.images[0])?.image_url) || ''
        this.form = {
          id: l.id,
          name: l.name || '',
          description: l.description || '',
          type: l.type || '',
          address: l.address || '',
          department_id: l.department?.id ? String(l.department.id) : '',
          latitude: l.latitude || '',
          longitude: l.longitude || '',
          cover: this.$buildImg ? this.$buildImg(mainImg) : mainImg,
          coverFile: null,
          coverChanged: false,
        }
      } catch (e) {
        console.error('Error cargando locación:', e)
      } finally {
        this.formLoading = false
      }
    },
    close() { this.show = false },
    onCover(e) {
      const f = e.target.files?.[0]
      if (f) {
        this.form.coverFile = f
        this.form.cover = URL.createObjectURL(f)
        this.form.coverChanged = true
      }
    },
    onCoverUrlInput() {
      this.form.coverFile = null
      this.form.coverChanged = true
    },
    clearCover() {
      this.form.cover = ''
      this.form.coverFile = null
      this.form.coverChanged = true
    },
    async ensureLeaflet() {
      if (window.L) {
        patchLeafletForVueLifecycle(window.L)
        this.leafletLoaded = true
        return
      }
      if (this.leafletLoaded) return
      const cssId = 'leaflet-css'
      if (!document.getElementById(cssId)) {
        const link = document.createElement('link')
        link.id = cssId
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
        document.head.appendChild(link)
      }
      await new Promise(resolve => {
        const existing = document.getElementById('leaflet-js')
        if (existing) {
          existing.addEventListener('load', resolve, { once: true })
          if (window.L) {
            patchLeafletForVueLifecycle(window.L)
            resolve()
          }
          return
        }
        const script = document.createElement('script')
        script.id = 'leaflet-js'
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
        script.async = true
        script.onload = () => {
          patchLeafletForVueLifecycle(window.L)
          resolve()
        }
        document.body.appendChild(script)
      })
      this.leafletLoaded = true
    },
    initMap() {
      const L = window.L
      if (!L) return
      patchLeafletForVueLifecycle(L)
      const container = this.$refs.mapContainer
      if (!container) return
      // If a previous Leaflet instance is bound to this container, clear it
      if (container._leaflet_id) {
        try { container._leaflet_id = null } catch {}
      }
      // Default center (La Paz) or use existing coordinates
      const lat = parseFloat(this.form.latitude)
      const lng = parseFloat(this.form.longitude)
      const hasCoords = !isNaN(lat) && !isNaN(lng)
      const center = hasCoords ? [lat, lng] : [-16.4897, -68.1193]
      // Always initialize a fresh map instance for reliability within modal
      this.mapInstance = L.map(container, {
        zoomControl: false,
        scrollWheelZoom: false,
        touchZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        zoomAnimation: false,
        markerZoomAnimation: false,
        fadeAnimation: false
      }).setView(center, hasCoords ? 14 : 12, { animate: false, noMoveStart: true })
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(this.mapInstance)
      this.mapInstance.on('click', (e) => {
        const { latlng } = e
        this.form.latitude = latlng.lat.toFixed(6)
        this.form.longitude = latlng.lng.toFixed(6)
        if (this.mapMarker) {
          this.mapMarker.setLatLng(latlng)
        } else {
          this.mapMarker = L.marker(latlng).addTo(this.mapInstance)
        }
      })
      // Place marker if coords pre-exist
      if (hasCoords) {
        const latlng = L.latLng(lat, lng)
        if (this.mapMarker) {
          this.mapMarker.setLatLng(latlng)
        } else {
          this.mapMarker = L.marker(latlng).addTo(this.mapInstance)
        }
      }
      // Ensure proper sizing after modal animation/render
      setTimeout(() => {
        try { this.mapInstance.invalidateSize() } catch {}
      }, 250)
    },
    async save() {
      if (!this.form.name || !this.form.name.trim()) { alert('El nombre es requerido'); return }
      if (!this.form.type) { alert('El tipo es requerido'); return }
      if (!this.form.department_id) { alert('El departamento es requerido'); return }
      
      const isCreating = this.form.id === 0
      
      // Validar límite solo al crear una nueva locación
      if (isCreating && !this.canCreateNewLocation) {
        const message = this.getLimitMessage('locations', this.userLocationsCount)
        alert(message || 'Has alcanzado el límite de locaciones permitidas en tu plan.')
        return
      }
      
      try {
        this.formLoading = true
        // Si hay imagen, usa FormData
        if (this.form.coverFile) {
          const fd = new FormData()
          fd.append('name', this.form.name.trim())
          if (this.form.description) fd.append('description', this.form.description.trim())
          if (this.form.type) fd.append('type', this.form.type)
          if (this.form.address) fd.append('address', this.form.address.trim())
          if (this.form.department_id) fd.append('department_id', String(this.form.department_id))
          if (this.form.latitude) fd.append('latitude', String(this.form.latitude))
          if (this.form.longitude) fd.append('longitude', String(this.form.longitude))
          fd.append('image', this.form.coverFile)
          
          const res = isCreating
            ? await adminLocationsService.create(fd)
            : await adminLocationsService.update(this.form.id, fd)
          
          if (res?.success && res?.data) {
            this.close()
            await this.reload()
            this.updateUserLocationsCount()
          } else {
            throw new Error(res?.message || 'Respuesta inválida del servidor')
          }
        } else {
          // JSON plano sin imagen
          const body = {
            name: this.form.name.trim(),
            description: this.form.description ? this.form.description.trim() : '',
            type: this.form.type || '',
            address: this.form.address ? this.form.address.trim() : '',
            department_id: this.form.department_id ? Number(this.form.department_id) : null,
            latitude: this.form.latitude ? Number(this.form.latitude) : null,
            longitude: this.form.longitude ? Number(this.form.longitude) : null,
          }

          if (this.form.coverChanged && this.form.cover) {
            body.image_url = this.form.cover
          }
          
          const res = isCreating
            ? await adminLocationsService.create(body)
            : await adminLocationsService.update(this.form.id, body)
          
          if (res?.success && res?.data) {
            this.close()
            await this.reload()
          } else {
            throw new Error(res?.message || 'Respuesta inválida del servidor')
          }
        }
      } catch (e) {
        console.error(`Error ${isCreating ? 'creando' : 'actualizando'} locación:`, e)
        alert(`No se pudo ${isCreating ? 'crear' : 'actualizar'} la locación: ` + (e?.response?.data?.message || e?.message || e))
      } finally {
        this.formLoading = false
      }
    },
    async remove(id) {
      if (!confirm('¿Eliminar esta locación?')) return
      try {
        await adminLocationsService.remove(id)
        this.items = this.items.filter(x => x.id !== id)
        if (this.pagination.count) this.pagination.count = Math.max(0, this.pagination.count - 1)
        this.updateUserLocationsCount()
      } catch (e) {
        console.error('Error eliminando locación', e)
        alert('No se pudo eliminar la locación')
      }
    },
    updateUserLocationsCount() {
      const currentUser = this.authStore.currentUser
      if (!currentUser) {
        this.userLocationsCount = 0
        return
      }
      
      // Si es super_admin, no necesita contar (tiene acceso ilimitado)
      if (this.isSuperAdmin) {
        this.userLocationsCount = 0
        return
      }
      
      // Para otros usuarios, contar todas las locaciones visibles
      // Ya que estamos usando listByUser, todas las locaciones son del usuario actual
      this.userLocationsCount = this.items.length
      
      // También usar el count de paginación si está disponible (más preciso)
      if (this.pagination.count && this.pagination.count > 0) {
        this.userLocationsCount = this.pagination.count
      }
    },
    truncate(text, max) {
      if (!text) return ''
      return text.length > max ? `${text.slice(0, max)}â€¦` : text
    },
    formatDate(iso) {
      if (!iso) return '-'
      try {
        const d = new Date(iso)
        return d.toLocaleDateString('es-BO')
      } catch {
        return '-'
      }
    }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>
