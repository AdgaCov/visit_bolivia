<template>
  <div class="container-fluid p-4">
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="h3 mb-1">
          <i class="fas fa-cog text-primary me-2"></i>
          Configuración
        </h1>
        <p class="text-muted">Preferencias del portal y branding</p>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent">
            <h5 class="mb-0">Branding</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Logo</label>
              <div class="d-flex align-items-center gap-3">
                <img :src="previewLogo || logo" alt="logo" style="width:64px;height:64px;object-fit:contain;background:var(--light-gray);border-radius:8px;">
                <input type="file" accept="image/*" @change="onLogo">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Color primario</label>
                <input type="color" class="form-control form-control-color" v-model="primary">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Fuente base</label>
                <select class="form-select" v-model="font">
                  <option value="Poppins">Poppins</option>
                  <option value="Roboto">Roboto</option>
                  <option value="Segoe UI">Segoe UI</option>
                </select>
              </div>
            </div>

            <button class="btn btn-primary" @click="saveBranding">
              <i class="fas fa-save me-2"></i>
              Guardar cambios
            </button>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent">
            <h5 class="mb-0">Preferencias</h5>
          </div>
          <div class="card-body">
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="autoApprove" v-model="autoApprove">
              <label class="form-check-label" for="autoApprove">
                Aprobar automáticamente envíos de usuarios verificados
              </label>
            </div>

            <div class="mb-3">
              <label class="form-label">Zona horaria</label>
              <select class="form-select" v-model="timezone">
                <option value="America/La_Paz">America/La_Paz</option>
                <option value="UTC">UTC</option>
              </select>
            </div>

            <button class="btn btn-secondary" @click="savePreferences">
              <i class="fas fa-save me-2"></i>
              Guardar preferencias
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminSettings',
  data() {
    return {
      logo: '/icons/logo_tmp.png',
      previewLogo: '',
      primary: '#1e40af',
      font: 'Poppins',
      autoApprove: false,
      timezone: 'America/La_Paz'
    }
  },
  methods: {
    onLogo(e) {
      const f = e.target.files?.[0]
      if (!f) return
      this.previewLogo = URL.createObjectURL(f)
    },
    saveBranding() {
      alert('Branding guardado (demo)')
    },
    savePreferences() {
      alert('Preferencias guardadas (demo)')
    }
  }
}
</script>

<style scoped>
.card { border-radius: 12px; }
</style>



