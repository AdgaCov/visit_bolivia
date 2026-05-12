<template>
  <div class="container-fluid p-4">
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h3 mb-1">
            <i class="fas fa-users text-primary me-2"></i>
            Usuarios
          </h1>
          <p class="text-muted">Gestión de usuarios y roles</p>
        </div>
        <button class="btn btn-primary" @click="openForm()">
          <i class="fas fa-user-plus me-2"></i>
          Nuevo usuario
        </button>
      </div>
       <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-3 mb-2">
          <div class="col-md-4"><input class="form-control" placeholder="Buscar..." v-model="search"></div>
        </div>
      </div>
    </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="loading" class="text-center p-4">
          <span class="spinner-border spinner-border-sm text-primary"></span>
          <span class="ms-2">Cargando usuarios...</span>
        </div>
        <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Plan</th>
                <th>Estado</th>
                <th>Negocio</th>
                <th>Sector</th>
                <th>Último acceso</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in users" :key="u.id">
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <img :src="u.avatar || '/icons/icono_b.svg'" alt="" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                    <div>
                      <strong>{{ u.name || u.username }}</strong>
                      <div class="text-muted small">{{ u.email }}</div>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-light text-dark">{{ u.role.name}}</span></td>
                <td><span class="badge bg-light text-dark">{{ u.plan.name}}</span></td>
                <td>
                  <button 
                    type="button"
                    :class="['badge', 'border-0', 'btn', (u.status !== undefined ? u.status : u.active) ? 'bg-success' : 'bg-secondary']"
                    @click="toggleStatus(u)"
                    :disabled="loading"
                    :title="(u.status !== undefined ? u.status : u.active) ? 'Desactivar usuario' : 'Activar usuario'"
                    style="cursor: pointer;"
                  >
                    {{ (u.status !== undefined ? u.status : u.active) ? 'Activo' : 'Inactivo' }}
                  </button>
                </td>
                <td>{{ u.business || 'Sin Negocio'}}</td>
                <td>{{ u.sector || 'Sin sector' }}</td>
                <td>{{ formatDate(u.created_at) || '-' }}</td>
                <td class="text-end">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" @click="openForm(u)"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" @click="remove(u.id)"><i class="fas fa-trash"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="!loading && users.length === 0" class="text-center p-4 text-muted">No hay usuarios registrados</div>
        </div>
      </div>
    </div>

    <div v-if="show" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editing ? 'Editar' : 'Nuevo' }} usuario</h5>
            <button type="button" class="btn-close" @click="close" :disabled="formLoading"></button>
          </div>
          <!-- Progress Bar -->
          <div v-if="formLoading" class="progress" style="height: 4px; border-radius: 0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                 role="progressbar" 
                 style="width: 100%;">
            </div>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" v-model="form.username" :disabled="formLoading">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" v-model="form.email" :disabled="formLoading">
            </div>
            <div v-if="!editing" class="mb-3">
              <label class="form-label">Contraseña <span class="text-danger">*</span></label>
              <input type="password" class="form-control" v-model="form.password" :disabled="formLoading" placeholder="Mínimo 6 caracteres">
              <small class="text-muted">Requerida para crear un nuevo usuario</small>
            </div>
            <div class="mb-3">
              <label class="form-label">Rol</label>
              <select class="form-select" v-model.number="form.role" :disabled="formLoading">
                <option :value="1">Rol 1</option>
                <option :value="2">Rol 2</option>
                <option :value="3">Rol 3</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Planes</label>
              <select class="form-select" v-model.number="form.plan" :disabled="formLoading">
                <option :value="1">Basico</option>
                <option :value="2">Pro</option>
                <option :value="3">Premiun</option>
              </select>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="active" v-model="form.active" :disabled="formLoading">
              <label class="form-check-label" for="active">Activo</label>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="close" :disabled="formLoading">Cancelar</button>
            <button class="btn btn-primary" @click="save" :disabled="formLoading">
              <span v-if="formLoading" class="spinner-border spinner-border-sm me-2"></span>
              <span v-if="formLoading">{{ editing ? 'Actualizando...' : 'Guardando...' }}</span>
              <span v-else>{{ editing ? 'Actualizar' : 'Crear' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import adminUsersService from '@/services/admin/users.admin.service'

export default {
  name: 'AdminUsers',
  data() {
    return {
      loading: true,
      error: '',
      show: false,
      editing: false,
      formLoading: false,
      search:'',
      users: [],
      form: { id: 0, username: '', email: '', password: '', role: 1, active: true , plan:1}
    }
  },
  computed:{
    filtered() {
      if (!this.search) return this.users
      const s = this.search.toLowerCase()
      return this.users.filter(cat =>
        (cat.name && cat.name.toLowerCase().includes(s)) ||
        (cat.rol && cat.rol.toLowerCase().includes(s))
      )
    }
  },
  mounted() {
    this.reload()
  },
  methods: {
    async reload() {
      this.loading = true
      this.error = ''
      try {
        const res = await adminUsersService.list()
        this.users = res.data || []
      } catch (e) {
        this.error = e?.response?.data?.message || e?.message || 'Error al cargar usuarios'
        this.users = []
        console.error('Error al cargar usuarios:', e)
      } finally {
        this.loading = false
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('es-BO', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },
    openForm(user) {
      if (user) {
        this.editing = true
        this.form = { 
          id: user.id, 
          username: user.name || user.username || '', 
          email: user.email || '', 
          role: user.role_id || (user.role ? parseInt(user.role) : 1), 
          active: user.status !== undefined ? user.status : (user.active !== undefined ? user.active : true),
          plan:user.plan_id || (user.plan ? parseInt(user.plan) : 1),
          business:user.business
        }
      } else {
        this.editing = false
        this.form = { id: 0, username: '', email: '', password: '', role: 1, active: true, plan:1 }
      }
      this.show = true
    },
    close() { 
      if (!this.formLoading) {
        this.show = false 
      }
    },
    async save() {
      if (!this.form.username || !this.form.username.trim()) { 
        alert('El nombre de usuario es requerido')
        return 
      }
      if (!this.form.email || !this.form.email.trim()) { 
        alert('El email es requerido')
        return 
      }
      // Validar password solo al crear un nuevo usuario
      if (!this.editing && (!this.form.password || this.form.password.trim().length < 6)) {
        alert('La contraseña es requerida y debe tener al menos 6 caracteres')
        return 
      }
      this.formLoading = true
      try {
        // Asegurar que role_id sea un número válido
        let roleId = 1 // Valor por defecto
        let planId = 1 // Valor por defecto
        if (this.form.role !== undefined && this.form.role !== null) {
          roleId = typeof this.form.role === 'number' ? this.form.role : parseInt(this.form.role)
          if (isNaN(roleId) || roleId < 1 || roleId > 3) {
            roleId = 1 // Si no es válido, usar 1 por defecto
          }
        }
        if (this.form.plan !== undefined && this.form.plan !== null) {
          planId = typeof this.form.plan === 'number' ? this.form.plan : parseInt(this.form.plan)
          if (isNaN(planId) || planId < 1 || planId > 3) {
            planId = 1 // Si no es válido, usar 1 por defecto
          }
        }
        
        // Mapear los campos del formulario a los que espera la API
        const payload = {
          name: this.form.username.trim(), // La API espera 'name', no 'username'
          email: this.form.email.trim(),
          role_id: roleId, // Asegurar que siempre sea un número válido del 1 al 3
          status: this.form.active ? 1 : 0 ,// Convertir boolean a número (1 o 0)
          plan_id:planId
        }
        
        // Incluir password solo al crear un nuevo usuario
        if (!this.editing && this.form.password) {
          payload.password = this.form.password.trim()
        }
        
        // Debug: Verificar el payload antes de enviar
        console.log('📤 Payload a enviar:', payload)
        console.log('📋 Tipo de role_id:', typeof payload.role_id, 'Valor:', payload.role_id)
        
        // Validar que el rol esté entre 1 y 3
        if (payload.role_id < 1 || payload.role_id > 3) {
          alert('El rol debe ser un número entre 1 y 3')
          this.formLoading = false
          return
        }
        // Si es edición (tiene ID), usar update (POST), si no, usar create (POST)
        if (this.editing && this.form.id > 0) {
          await adminUsersService.update(this.form.id, payload)
        } else {
          await adminUsersService.create(payload)
        }
        // Cerrar modal antes de recargar
        this.show = false
        await this.reload()
      } catch (e) {
        const action = this.editing ? 'actualizar' : 'crear'
        alert(`No se pudo ${action} el usuario: ` + (e?.response?.data?.message || e?.message))
      } finally {
        this.formLoading = false
      }
    },
    async remove(id) {
      if (!confirm('¿Eliminar este usuario?')) return
      try {
        await adminUsersService.remove(id)
        await this.reload()
      } catch (e) {
        alert('No se pudo eliminar el usuario: ' + (e?.response?.data?.message || e?.message))
      }
    },
    async toggleStatus(user) {
      try {
        await adminUsersService.toggleStatus(user.id)
        // Actualizar el estado localmente sin recargar toda la lista
        const index = this.users.findIndex(u => u.id === user.id)
        if (index !== -1) {
          // Actualizar el estado del usuario en la lista
          this.users[index].status = !(user.status !== undefined ? user.status : user.active)
          // También actualizar active para compatibilidad
          this.users[index].active = this.users[index].status
        }
      } catch (e) {
        alert('No se pudo cambiar el estado del usuario: ' + (e?.response?.data?.message || e?.message))
        // Recargar en caso de error para asegurar consistencia
        await this.reload()
      }
    }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>



