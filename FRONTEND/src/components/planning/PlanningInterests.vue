<template>
  <!-- Sección: ¿Cuáles son tus intereses? -->
  <section class="interests-section">
    <div class="container">
      <div class="interests-content">
        <h2 class="interests-title">¿Cuáles son tus intereses?</h2>
        <p class="interests-subtitle">Ya sea que estés aquí por un fin de semana o varias semanas, siempre tendrás tiempo suficiente para experiencias extraordinarias.</p>
        
        <div class="interests-cards">
          <div class="interest-card">
            <div class="card-image">
              <img 
                src="/images/mas_destinos/1.png" 
                alt="Cultura boliviana" 
                class="card-photo"
                @error="handleImageError"
              />
            </div>
            <div class="card-content">
              <h3 class="card-title">Cultura boliviana — descubre algo inesperado</h3>
            </div>
          </div>
          
          <div class="interest-card">
            <div class="card-image">
              <img 
                src="/images/mas_destinos/2.png" 
                alt="Naturaleza boliviana" 
                class="card-photo"
                @error="handleImageError"
              />
            </div>
            <div class="card-content">
              <h3 class="card-title">Naturaleza boliviana — desacelera y maravíllate con una vida más salvaje</h3>
            </div>
          </div>
          
          <div class="interest-card">
            <div class="card-image">
              <img 
                src="/images/mas_destinos/3.png" 
                alt="Gastronomía boliviana" 
                class="card-photo"
                @error="handleImageError"
              />
            </div>
            <div class="card-content">
              <h3 class="card-title">Todo sobre la comida tradicional boliviana</h3>
            </div>
          </div>
          
          <div class="interest-card">
            <div class="card-image">
              <img 
                src="/images/mas_destinos/4.png" 
                alt="Aventura en Bolivia" 
                class="card-photo"
                @error="handleImageError"
              />
            </div>
            <div class="card-content">
              <h3 class="card-title">Aventura en Bolivia — explora paisajes únicos</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PlanningInterests',
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
   Sección de Intereses
   =============================== */
.interests-section {
  padding: 4.5rem 0;
  background: var(--off-white);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.interests-content {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}

.interests-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1rem;
  text-align: center;
}

.interests-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 3rem;
  text-align: center;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.interests-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.interest-card {
  background: var(--bg-primary);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.interest-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.card-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.card-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.interest-card:hover .card-photo {
  transform: scale(1.05);
}

.card-content {
  padding: 1.5rem;
}

.card-title {
  font-size: 1rem;
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
  line-height: 1.4;
  margin: 0;
  text-align: left;
}

/* Responsive para intereses */
@media (max-width: 991.98px) {
  .interests-cards {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
  }
  
  .interests-title {
    font-size: 2.5rem;
  }
  
  .interests-subtitle {
    font-size: 1rem;
  }
  
  .card-image {
    height: 180px;
  }
  
  .card-content {
    padding: 1.25rem;
  }
  
  .card-title {
    font-size: 0.95rem;
  }
}

@media (max-width: 575.98px) {
  .interests-section {
    padding: 3rem 0;
  }
  
  .interests-title {
    font-size: 2rem;
  }
  
  .interests-subtitle {
    font-size: 0.95rem;
    margin-bottom: 2rem;
  }
  
  .interests-cards {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .card-image {
    height: 160px;
  }
  
  .card-content {
    padding: 1rem;
  }
  
  .card-title {
    font-size: 0.9rem;
    text-align: center;
  }
}
</style>
