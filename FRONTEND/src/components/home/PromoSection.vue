<template>
  <section class="promo-section">
    <div class="container-fluid">
      <div class="promo-wrapper">
        <div class="promo-media" aria-hidden="true"></div>
        <div class="promo-card">
          <h3 class="promo-title">El tiempo es valioso. Aprovecha cada momento.</h3>
          <p class="promo-text">Descubre experiencias auténticas, itinerarios memorables y consejos locales para un viaje inolvidable por Bolivia.</p>
          <div class="promo-actions">
            <!-- <router-link to="/que-hacer" class="promo-btn promo-btn-primary">Qué hacer</router-link>
            <router-link to="/regiones" class="promo-btn promo-btn-secondary">Explorar regiones</router-link> -->
             <router-link to="/que-hacer" class="promo-btn promo-btn-secondary">Qué hacer</router-link>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PromoSection'
}
</script>

<style scoped>
/* Promo */
.promo-section { 
  /* padding: 4rem 0;  */
  /* border-top: 1px solid var(--border-light); */
  border-bottom: 1px solid var(--border-light);
}

/* Quitar gutter izquierdo del contenedor dentro de Promo */
.promo-section :deep(.container-fluid) {
  padding-left: 0;
}

.promo-wrapper { 
  position: relative; 
  border-radius: 0 16px 16px 0; 
  overflow: hidden;
  
  background: var(--white);
  border: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
}

.promo-media { 
  height: 520px; 
  background: url('https://www.turismo.produccion.gob.bo/wp-content/uploads/2022/11/movimiento-economico-turismo.jpeg') center/cover no-repeat;
}

.promo-card {
  position: absolute;
  right: 1rem;
  bottom: 1rem;
  background: var(--white);
  border: 1px solid var(--border-light);
  border-radius: 16px;
  padding: 1.5rem;
  max-width: 420px;
  box-shadow: var(--shadow-lg);
  backdrop-filter: blur(10px);
}

.promo-title { 
  margin: 0 0 0.75rem; 
  font-weight: 600;
  color: var(--text-primary);
  font-size: 1.25rem;
}

.promo-text { 
  color: var(--text-secondary); 
  margin-bottom: 1rem;
  line-height: 1.6;
}

/* Botones promo minimalistas */
.promo-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.promo-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  letter-spacing: 0.025em;
  min-width: 140px;
}

.promo-btn-primary {
  background: var(--white);
  color: var(--text-primary);
  border-color: var(--white);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.promo-btn-primary:hover {
   background: var(--white);
  color: var(--text-primary);
  border-color: var(--white);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.promo-btn-secondary {
  background: rgba(255, 255, 255, 0.1);
  color: var(--primary-blue);
  border-color: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.promo-btn-secondary:hover {
  background: var(--white);
  color: var(--text-primary);
  border-color: var(--white);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 255, 255, 0.2);
}

/* Responsive */
@media (max-width: 768px) {
  .promo-card { 
    position: static; 
    margin-top: -1.5rem;
    max-width: none;
    padding: 1.25rem;
  }
  
  .promo-title {
    font-size: 1.125rem;
  }
  
  .promo-text {
    font-size: 0.95rem;
  }
  
  .promo-actions {
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
  }
  
  .promo-btn {
    padding: 0.625rem 1.25rem;
    font-size: 0.85rem;
    min-width: 120px;
  }
}
</style>
