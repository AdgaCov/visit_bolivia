<template>
  <!-- Sección: Opciones de viaje para personas con discapacidades -->
  <section class="disability-travel-section">
    <div class="container">
      <div class="disability-travel-content">
        <div class="disability-travel-text">
          <h2 class="disability-travel-title">Opciones de viaje en Bolivia para personas con discapacidades</h2>
          <p class="disability-travel-description">
            Bolivia ofrece muchas opciones de recreación y ocio para personas con discapacidades. Planificar con anticipación garantiza una gran experiencia de viaje.
          </p>
          <button class="disability-travel-button">
            LEER MÁS
          </button>
        </div>
        
        <div class="disability-travel-image">
          <div class="image-container">
            <img 
              src="/images/dis.jpg" 
              alt="Transporte accesible en Bolivia" 
              class="disability-travel-photo"
              @error="handleImageError"
            />
            <div class="photo-credit">
              <span>Foto: Bolivia Turismo</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PlanningDisabilityTravel',
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
   Sección de Opciones de Viaje para Personas con Discapacidades
   =============================== */
.disability-travel-section {
  padding: 4.5rem 0;
  background: var(--bg-secondary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.disability-travel-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 500px;
}

.disability-travel-text {
  padding-right: 2rem;
}

.disability-travel-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.disability-travel-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.disability-travel-button {
  background: transparent;
  border: 1px solid var(--primary-blue);
  color: var(--primary-blue);
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: var(--font-weight-medium);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: var(--font-family-base);
}

.disability-travel-button:hover {
  background: var(--primary-blue);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.disability-travel-image {
  position: relative;
  height: 500px;
  overflow: hidden;
}

.disability-travel-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 0 16px 16px 0;
  overflow: hidden;
}

.disability-travel-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.disability-travel-photo:hover {
  transform: scale(1.02);
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

/* Responsive para opciones de viaje para personas con discapacidades */
@media (max-width: 991.98px) {
  .disability-travel-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .disability-travel-text {
    padding-right: 0;
    text-align: center;
  }
  
  .disability-travel-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .disability-travel-description {
    max-width: 100%;
  }
  
  .disability-travel-image {
    height: 350px;
  }
  
  .disability-travel-image .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .disability-travel-section {
    padding: 3rem 0;
  }
  
  .disability-travel-title {
    font-size: 2rem;
  }
  
  .disability-travel-description {
    font-size: 1rem;
  }
  
  .disability-travel-image {
    height: 250px;
  }
}
</style>
