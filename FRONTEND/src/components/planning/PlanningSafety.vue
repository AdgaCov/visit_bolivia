<template>
  <!-- Sección: Seguridad -->
  <section class="safety-section">
    <div class="container">
      <div class="safety-content">
        <div class="safety-image">
          <div class="image-container">
            <img 
              src="/images/seguro.png" 
              alt="Seguridad en Bolivia" 
              class="safety-photo"
              @error="handleImageError"
            />
            <div class="photo-credit">
              <span>Foto: Bolivia Turismo</span>
            </div>
          </div>
        </div>
        
        <div class="safety-text">
          <h2 class="safety-title">Seguridad</h2>
          <p class="safety-description">
            Bolivia es un país seguro para los turistas, con bajas tasas de criminalidad en la mayoría de las áreas turísticas. Como en cualquier país, debes cuidar tus pertenencias y estar atento a los carteristas en áreas urbanas concurridas. En las ciudades principales, evita caminar solo por la noche en áreas poco iluminadas y mantén tus objetos de valor seguros.
          </p>
          <p class="safety-description">
            El agua del grifo es segura para beber en la mayoría de las ciudades principales, aunque en áreas rurales se recomienda beber agua embotellada. Las carreteras pueden estar en mal estado en algunas áreas rurales, por lo que se recomienda conducir con precaución.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PlanningSafety',
  methods: {
    handleImageError(event) {
      // Fallback a una imagen placeholder si la imagen no se carga
      event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0xNzUgMTI1SDIyNVYxNzVIMTc1VjEyNVoiIGZpbGw9IiNEOUQ5RDkiLz4KPHBhdGggZD0iTTE4NSAxMzVIMjE1VjE2NUgxODVWMTM1WiIgZmlsbD0iI0NDQ0NDQyIvPgo8dGV4dCB4PSIyMDAiIHk9IjIwMCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5OTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZW4gbm8gZGlzcG9uaWJsZTwvdGV4dD4KPC9zdmc+'
    }
  }
}
</script>

<style scoped>
/* ===============================
   Sección de Seguridad
   =============================== */
.safety-section {
  padding: 4.5rem 0;
  background: var(--off-white);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.safety-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 500px;
}

.safety-image {
  position: relative;
  height: 500px;
  overflow: hidden;
}

.safety-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 16px 0 0 16px;
  overflow: hidden;
}

.safety-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.safety-photo:hover {
  transform: scale(1.02);
}

.safety-text {
  padding-left: 2rem;
}

.safety-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.safety-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
  max-width: 500px;
}

.safety-description:last-child {
  margin-bottom: 0;
}

.photo-credit {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: var(--font-weight-medium);
}

/* Responsive para seguridad */
@media (max-width: 991.98px) {
  .safety-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .safety-text {
    padding-left: 0;
    text-align: center;
  }
  
  .safety-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .safety-description {
    max-width: 100%;
  }
  
  .safety-image {
    height: 350px;
  }
  
  .safety-image .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .safety-section {
    padding: 3rem 0;
  }
  
  .safety-title {
    font-size: 2rem;
  }
  
  .safety-description {
    font-size: 1rem;
  }
  
  .safety-image {
    height: 250px;
  }
}
</style>
