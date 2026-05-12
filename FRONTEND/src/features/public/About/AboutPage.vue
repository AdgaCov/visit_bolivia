<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="row mb-5">
      <div class="col-12 text-center">
        <h1 class="display-5 fw-bold mb-3">
          <i class="fas fa-info-circle text-primary me-3"></i>
          Acerca de Bolivia Turismo
        </h1>
        <p class="lead text-muted">Descubre más sobre nuestro proyecto</p>
      </div>
    </div>

    <!-- Información del proyecto -->
    <div class="row mb-5">
      <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-5">
            <h3 class="card-title mb-4">
              <i class="fas fa-mountain text-primary me-2"></i>
              Nuestra Misión
            </h3>
            <p class="card-text">
              Bolivia Turismo nace con el objetivo de mostrar al mundo la belleza y diversidad 
              de Bolivia. Desde el majestuoso Salar de Uyuni hasta la exuberante Amazonía, 
              queremos que descubras los lugares más increíbles de nuestro país.
            </p>
            
            <h4 class="mt-4 mb-3">
              <i class="fas fa-map-marked-alt text-success me-2"></i>
              Lo que ofrecemos
            </h4>
            <ul class="list-unstyled">
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Información detallada de lugares turísticos
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Guías prácticas para viajeros
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Información por regiones geográficas
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Consejos para llegar a cada destino
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-5">
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="fas fa-map-marker-alt text-primary display-4 mb-3"></i>
          <h3 class="fw-bold text-primary">8</h3>
          <p class="text-muted">Lugares Turísticos</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="fas fa-globe-americas text-success display-4 mb-3"></i>
          <h3 class="fw-bold text-success">4</h3>
          <p class="text-muted">Regiones</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="fas fa-images text-info display-4 mb-3"></i>
          <h3 class="fw-bold text-info">24</h3>
          <p class="text-muted">Imágenes</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="fas fa-users text-warning display-4 mb-3"></i>
          <h3 class="fw-bold text-warning">100%</h3>
          <p class="text-muted">Gratuito</p>
        </div>
      </div>
    </div>

    <!-- Enlaces útiles -->
    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <h4 class="card-title mb-4">
              <i class="fas fa-link text-primary me-2"></i>
              Enlaces Útiles
            </h4>
            <div class="row">
              <div class="col-md-4 mb-3">
                <a href="/lugares" class="btn btn-outline-primary w-100">
                  <i class="fas fa-map-marker-alt me-2"></i>
                  Explorar Lugares
                </a>
              </div>
              <div class="col-md-4 mb-3">
                <a href="/regiones" class="btn btn-outline-success w-100">
                  <i class="fas fa-globe-americas me-2"></i>
                  Ver Regiones
                </a>
              </div>
              <div class="col-md-4 mb-3">
                <a href="/como-llegar" class="btn btn-outline-info w-100">
                  <i class="fas fa-plane me-2"></i>
                  Cómo Llegar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enlace discreto al admin -->
    <div class="row mt-4">
      <div class="col-12 text-center">
        <small class="text-muted">
          <a href="/admin/login" class="text-decoration-none text-muted">
            <i class="fas fa-cog me-1"></i>
            Panel de Administración
          </a>
        </small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AboutPage'
}
</script>

<style scoped>
.card {
  border-radius: 15px;
}

.display-4 {
  font-size: 3rem;
}

@media (max-width: 768px) {
  .display-4 {
    font-size: 2rem;
  }
}
</style> 