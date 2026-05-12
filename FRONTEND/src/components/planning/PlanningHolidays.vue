<template>
  <!-- Sección: Feriados públicos -->
  <section class="holidays-section">
    <div class="container">
      <div class="holidays-content">
        <div class="holidays-text">
          <h2 class="holidays-title">Feriados públicos</h2>
          <p class="holidays-intro">
            La mayoría de tiendas, bancos y restaurantes permanecen abiertos durante los feriados públicos, con excepción del 25 de diciembre y 1 de enero, cuando la mayoría de negocios permanecen cerrados.
          </p>
          
          <div class="holidays-list">
            <div class="holiday-item">
              <div class="holiday-date">1 de enero</div>
              <div class="holiday-name">Año Nuevo</div>
              <div class="holiday-description">Día tranquilo después de las celebraciones de fin de año.</div>
            </div>
            
            <div class="holiday-item">
              <div class="holiday-date">22 de enero</div>
              <div class="holiday-name">Día del Estado Plurinacional</div>
              <div class="holiday-description">Conmemoración de la fundación del Estado Plurinacional de Bolivia.</div>
            </div>
            
            <div class="holiday-item">
              <div class="holiday-date">Viernes Santo</div>
              <div class="holiday-name">Semana Santa</div>
              <div class="holiday-description">Celebrado con procesiones religiosas y tradiciones católicas en todo el país.</div>
            </div>
            
            <div class="holiday-item">
              <div class="holiday-date">1 de mayo</div>
              <div class="holiday-name">Día del Trabajador</div>
              <div class="holiday-description">Día de conmemoración de los trabajadores con marchas y celebraciones sindicales.</div>
            </div>
            
            <div class="holiday-item">
              <div class="holiday-date">6 de agosto</div>
              <div class="holiday-name">Día de la Independencia</div>
              <div class="holiday-description">Fiesta nacional con desfiles militares, eventos culturales y celebraciones familiares.</div>
            </div>
            
            <div class="holiday-item">
              <div class="holiday-date">2 de noviembre</div>
              <div class="holiday-name">Día de los Difuntos</div>
              <div class="holiday-description">Día de conmemoración de los fallecidos, visitas a cementerios y ofrendas tradicionales.</div>
            </div>
            
            <div class="holiday-item">
              <div class="holiday-date">25 de diciembre</div>
              <div class="holiday-name">Navidad</div>
              <div class="holiday-description">Celebración familiar con tradiciones católicas, pesebres y reuniones familiares.</div>
            </div>
          </div>
        </div>
        
        <div class="holidays-image">
          <div class="image-container">
            <img 
              src="/images/feriado.png" 
              alt="Celebraciones en Bolivia" 
              class="holidays-photo"
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
  name: 'PlanningHolidays',
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
   Sección de Feriados
   =============================== */
.holidays-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.holidays-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: start;
  min-height: 600px;
}

.holidays-text {
  padding-right: 2rem;
  /* En desktop, limitar la altura al alto de la imagen y permitir scroll */
  max-height: 600px; /* coincide con .holidays-image */
  overflow-y: auto;
  /* Ocultar scrollbar (cross-browser) */
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */
}

/* WebKit (Chrome/Safari/Edge Chromium) */
.holidays-text::-webkit-scrollbar { display: none; }

.holidays-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.holidays-intro {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 500px;
}

.holidays-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.holiday-item {
  border-bottom: 1px solid var(--border-light);
  padding-bottom: 1.5rem;
}

.holiday-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.holiday-date {
  font-size: 1rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.holiday-name {
  font-size: 1.125rem;
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.holiday-description {
  font-size: 0.95rem;
  color: var(--text-secondary);
  line-height: 1.5;
}

.holidays-image {
  position: relative;
  height: 600px;
  overflow: hidden;
}

.holidays-image .image-container {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 0 16px 16px 0;
  overflow: hidden;
}

.holidays-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.holidays-photo:hover {
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

/* Responsive para feriados */
@media (max-width: 991.98px) {
  .holidays-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .holidays-text {
    padding-right: 0;
    text-align: center;
    /* En layouts de una columna, permitir flujo natural del texto */
    max-height: none;
    overflow: visible;
  }
  
  .holidays-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .holidays-intro {
    max-width: 100%;
  }
  
  .holidays-image {
    height: 400px;
  }
  
  .holidays-image .image-container {
    border-radius: 16px;
  }
}

@media (max-width: 575.98px) {
  .holidays-section {
    padding: 3rem 0;
  }
  
  .holidays-title {
    font-size: 2rem;
  }
  
  .holidays-intro {
    font-size: 1rem;
  }
  
  .holidays-image {
    height: 300px;
  }
  
  .holidays-list {
    gap: 1rem;
  }
  
  .holiday-item {
    padding-bottom: 1rem;
  }
  
  .holiday-date {
    font-size: 0.9rem;
  }
  
  .holiday-name {
    font-size: 1rem;
  }
  
  .holiday-description {
    font-size: 0.85rem;
  }
}
</style>
