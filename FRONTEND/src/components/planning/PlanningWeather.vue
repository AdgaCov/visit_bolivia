<template>
  <!-- Sección: Clima y tiempo -->
  <section class="weather-section">
    <div class="container">
      <div class="weather-content">
        <div class="weather-image">
          <div class="image-container">
            <img 
              src="/images/climatiempo.jpg" 
              alt="Clima de Bolivia" 
              class="weather-photo"
              @error="handleImageError"
            />
            <div class="photo-credit">
              <span>Foto: Bolivia Turismo</span>
            </div>
          </div>
        </div>
        
        <div class="weather-text">
          <h2 class="weather-title">Clima y tiempo</h2>
          <p class="weather-description">
            El clima en Bolivia varía según la altitud y región. En el altiplano, las temperaturas son frescas durante todo el año, con días soleados y noches frías. En los valles, el clima es templado y agradable. En las tierras bajas del oriente, el clima es tropical con lluvias estacionales. La mejor época para visitar es de mayo a octubre, durante la temporada seca.
          </p>
          <button class="weather-button">
            VER PRONÓSTICO LOCAL
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PlanningWeather',
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
   Sección de Clima
   =============================== */
.weather-section {
  padding: 4.5rem 0;
  background: var(--off-white);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.weather-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 500px;
}

.weather-image {
  position: relative;
  height: 500px;
  overflow: hidden;
}

.weather-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 16px 0 0 16px;
  overflow: hidden;
}

.weather-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.weather-photo:hover {
  transform: scale(1.02);
}

.weather-text {
  padding-left: 2rem;
}

.weather-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.weather-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.weather-button {
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

.weather-button:hover {
  background: var(--primary-blue);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
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

/* Responsive para clima */
@media (max-width: 991.98px) {
  .weather-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .weather-text {
    padding-left: 0;
    text-align: center;
  }
  
  .weather-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .weather-description {
    max-width: 100%;
  }
  
  .weather-image {
    height: 350px;
  }
  
  .weather-image .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .weather-section {
    padding: 3rem 0;
  }
  
  .weather-title {
    font-size: 2rem;
  }
  
  .weather-description {
    font-size: 1rem;
  }
  
  .weather-image {
    height: 250px;
  }
}
</style>
