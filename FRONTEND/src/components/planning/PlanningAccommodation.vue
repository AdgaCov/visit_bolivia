<template>
  <!-- Sección: Dónde alojarse -->
  <section class="accommodation-section">
    <div class="container">
      <div class="accommodation-content">
        <div class="accommodation-text">
          <h2 class="accommodation-title">Dónde alojarse</h2>
          <p class="accommodation-description">
            Desde hoteles de lujo en las ciudades principales hasta cabañas únicas en la naturaleza, Bolivia ofrece opciones de alojamiento para todos los gustos y presupuestos. Encuentra tu lugar perfecto en La Paz, Santa Cruz, Cochabamba o en los rincones más remotos del país.
          </p>
          <button class="accommodation-button">
            ENCONTRAR ALOJAMIENTO
          </button>
        </div>
        
        <div class="accommodation-image">
          <div class="image-container">
            <img 
              src="/images/5.png" 
              alt="Alojamiento en Bolivia" 
              class="accommodation-photo"
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
  name: 'PlanningAccommodation',
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
   Sección de Alojamiento
   =============================== */
.accommodation-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.accommodation-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 500px;
}

.accommodation-text {
  padding-right: 2rem;
}

.accommodation-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.accommodation-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.accommodation-button {
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

.accommodation-button:hover {
  background: var(--primary-blue);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.accommodation-image {
  position: relative;
  height: 500px;
  overflow: hidden;
}

.image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 0 16px 16px 0;
  overflow: hidden;
}

.accommodation-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.accommodation-photo:hover {
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

/* Responsive para alojamiento */
@media (max-width: 991.98px) {
  .accommodation-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .accommodation-text {
    padding-right: 0;
    text-align: center;
  }
  
  .accommodation-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .accommodation-description {
    max-width: 100%;
  }
  
  .accommodation-image {
    height: 350px;
  }
  
  .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .accommodation-section {
    padding: 3rem 0;
  }
  
  .accommodation-title {
    font-size: 2rem;
  }
  
  .accommodation-description {
    font-size: 1rem;
  }
  
  .accommodation-image {
    height: 250px;
  }
}
</style>
