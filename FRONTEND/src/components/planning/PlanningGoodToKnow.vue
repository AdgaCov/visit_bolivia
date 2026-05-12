<template>
  <!-- Sección: Bueno saber -->
  <section class="good-to-know-section">
    <div class="container">
      <div class="good-to-know-content">
        <h2 class="good-to-know-title">Bueno saber</h2>
        <p class="good-to-know-subtitle">Ten en cuenta esta información mientras te preparas para tu viaje a Bolivia.</p>
        
        <div class="info-columns">
          <div class="info-column">
            <div class="info-item">
              <h3 class="info-heading">Idioma oficial</h3>
              <p class="info-text">Español, con quechua y aymara ampliamente hablados</p>
            </div>
            
            <div class="info-item">
              <h3 class="info-heading">Zona horaria</h3>
              <p class="info-text">GMT -4 durante todo el año</p>
            </div>
            
            <div class="info-item">
              <h3 class="info-heading">Enchufe eléctrico</h3>
              <p class="info-text">Tipo A y C, con voltaje de 220V y frecuencia de 50Hz</p>
            </div>
          </div>
          
          <div class="info-column">
            <div class="info-item">
              <h3 class="info-heading">Moneda</h3>
              <p class="info-text">Boliviano (Bs)</p>
            </div>
            
            <div class="info-item">
              <h3 class="info-heading">Capital</h3>
              <p class="info-text">Sucre (constitucional) y La Paz (administrativa)</p>
            </div>
            
            <div class="info-item">
              <h3 class="info-heading">Viajar con mascotas</h3>
              <p class="info-text">Tu mascota debe tener pasaporte y chip. Debe estar sana y vacunada. Contacta al SENASAG para más detalles.</p>
            </div>
          </div>
          
          <div class="info-column">
            <div class="info-item">
              <h3 class="info-heading">Número de emergencia</h3>
              <p class="info-text">110 (policía), 118 (bomberos), 119 (ambulancia)</p>
            </div>
            
            <div class="info-item">
              <h3 class="info-heading">Código de país</h3>
              <p class="info-text">+591</p>
            </div>
            
            <div class="info-item">
              <h3 class="info-heading">Propinas en Bolivia</h3>
              <p class="info-text">10% es la propina típica por un servicio excepcional</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PlanningGoodToKnow'
}
</script>

<style scoped>
/* ===============================
   Sección de Bueno Saber
   =============================== */
.good-to-know-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
  position: relative;
}

.good-to-know-section::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: var(--bolivia-green);
}

.good-to-know-content {
  max-width: 1200px;
  margin: 0 auto;
  padding-left: 2rem;
}

.good-to-know-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1rem;
  text-align: left;
}

.good-to-know-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 3rem;
  text-align: left;
}

.info-columns {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 3rem;
}

.info-column {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.info-heading {
  font-size: 1rem;
  font-weight: var(--font-weight-semibold);
  color: var(--bolivia-purple);
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-text {
  font-size: 1rem;
  color: var(--text-primary);
  line-height: 1.5;
  margin: 0;
}

/* Responsive para bueno saber */
@media (max-width: 991.98px) {
  .good-to-know-content {
    padding-left: 1rem;
  }
  
  .good-to-know-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .good-to-know-subtitle {
    text-align: center;
  }
  
  .info-columns {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .info-column {
    gap: 1.5rem;
  }
}

@media (max-width: 575.98px) {
  .good-to-know-section {
    padding: 3rem 0;
  }
  
  .good-to-know-title {
    font-size: 2rem;
  }
  
  .good-to-know-subtitle {
    font-size: 1rem;
  }
  
  .info-columns {
    gap: 1.5rem;
  }
  
  .info-column {
    gap: 1rem;
  }
  
  .info-heading {
    font-size: 0.9rem;
  }
  
  .info-text {
    font-size: 0.9rem;
  }
}
</style>
