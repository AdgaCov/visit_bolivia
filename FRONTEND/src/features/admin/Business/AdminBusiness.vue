<template>
  <div class="container-fluid p-4">
    <div class="row mb-4">
      <div class="col-12 d-flex align-items-center justify-content-between">
        <div>
          <h1 class="h3 mb-1">Empresas</h1>
          <p class="text-muted mb-0">Gestiona hoteles y restaurantes (demo estático)</p>
        </div>
        <div class="d-flex gap-2">
          <router-link to="/admin/business/new/hotel" class="btn btn-primary">
            <i class="fas fa-bed me-2"></i>Nuevo Hotel
          </router-link>
          <router-link to="/admin/business/new/restaurant" class="btn btn-outline-primary">
            <i class="fas fa-utensils me-2"></i>Nuevo Restaurante
          </router-link>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <div class="card business-card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box me-3"><i class="fas fa-bed"></i></div>
              <div>
                <h5 class="mb-1">Hoteles</h5>
                <p class="text-muted mb-0">Perfiles de alojamiento (estático)</p>
              </div>
            </div>
            <ul class="list-unstyled mb-0 small text-muted">
              <li>• Portada, galería, servicios, políticas</li>
              <li>• Habitaciones y tarifas destacadas</li>
              <li>• CTA WhatsApp/Reservar/Llamar</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card business-card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-box me-3"><i class="fas fa-utensils"></i></div>
              <div>
                <h5 class="mb-1">Restaurantes</h5>
                <p class="text-muted mb-0">Perfiles gastronómicos (estático)</p>
              </div>
            </div>
            <ul class="list-unstyled mb-0 small text-muted">
              <li>• Menú destacado y etiquetas</li>
              <li>• Servicios/amenities y políticas</li>
              <li>• CTA WhatsApp/Llamar/Reservar</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default { name: 'AdminBusiness' }
</script>

<style scoped>
/* el margen se maneja en AdminLayout.vue */
.business-card {
  background: var(--card-bg);
  border: 1px solid var(--border-light);
  border-radius: 14px;
  box-shadow: var(--shadow-sm);
}
.icon-box {
  width: 44px; height: 44px; border-radius: 10px;
  background: var(--bg-secondary);
  display: flex; align-items: center; justify-content: center;
  color: var(--primary-blue);
}
</style>


