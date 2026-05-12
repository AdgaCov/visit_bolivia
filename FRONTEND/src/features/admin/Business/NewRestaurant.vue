<template>
  <div class="container-fluid p-4">
    <h1 class="h4 mb-3">Registrar Restaurante (Demo estático)</h1>
    <p class="text-muted">Completa la información del perfil. No guarda datos (demo).</p>

    <div class="card form-card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre del Restaurante</label>
            <input class="form-control" placeholder="La Llama Feliz" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Slug</label>
            <input class="form-control" placeholder="la-llama-feliz" />
          </div>

          <div class="col-12">
            <label class="form-label">Descripción breve</label>
            <textarea class="form-control" rows="2" placeholder="Descripción resumida"></textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label">Teléfono</label>
            <input class="form-control" placeholder="+591 70000000" />
          </div>
          <div class="col-md-4">
            <label class="form-label">WhatsApp</label>
            <input class="form-control" placeholder="https://wa.me/5917xxxxxx" />
          </div>
          <div class="col-md-4">
            <label class="form-label">Email</label>
            <input class="form-control" placeholder="contacto@restaurante.com" />
          </div>

          <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input class="form-control" placeholder="Av. Principal 123" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Latitud</label>
            <input class="form-control" placeholder="-16.5" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Longitud</label>
            <input class="form-control" placeholder="-68.1" />
          </div>

          <div class="col-12">
            <label class="form-label">Servicios / Amenities</label>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge rounded-pill bg-light text-dark border">WiFi</span>
              <span class="badge rounded-pill bg-light text-dark border">Vegano</span>
              <span class="badge rounded-pill bg-light text-dark border">Sin gluten</span>
              <span class="badge rounded-pill bg-light text-dark border">Terraza</span>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Políticas</label>
            <textarea class="form-control" rows="3" placeholder="Reservas, horarios, etc."></textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Menú destacado</label>
            <div class="row g-2">
              <div class="col-md-6">
                <input class="form-control" placeholder="Pique Macho - $12" />
              </div>
              <div class="col-md-6">
                <input class="form-control" placeholder="Sopa de Maní - $8" />
              </div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Galería de imágenes (URL)</label>
            <div class="row g-2">
              <div class="col-md-4"><input class="form-control" placeholder="https://.../img1.jpg" /></div>
              <div class="col-md-4"><input class="form-control" placeholder="https://.../img2.jpg" /></div>
              <div class="col-md-4"><input class="form-control" placeholder="https://.../img3.jpg" /></div>
            </div>
          </div>

          <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary">Guardar (demo)</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default { name: 'NewRestaurant' }
</script>

<style scoped>
/* el margen se maneja en AdminLayout.vue */
.form-card { background: var(--card-bg); border: 1px solid var(--border-light); border-radius: 14px; }
.form-label { color: var(--text-secondary); font-weight: 600; }
.form-control { border-color: var(--border-light); }
</style>


