<template>
  <!-- Sección: Información médica -->
  <section class="medical-section">
    <div class="container">
      <div class="medical-content">
        <h2 class="medical-title">Información médica</h2>
        <p class="medical-description">
          Se recomienda encarecidamente contratar un seguro de viaje antes de visitar Bolivia. Los residentes de países con convenios de salud deben verificar si su proveedor de atención médica tiene un contrato con el sistema de salud boliviano. Se recomienda obtener una <strong>Tarjeta de Seguro de Salud Internacional</strong> antes de partir. Esta tarjeta no es un sustituto del seguro médico y de viaje, pero permite a los ciudadanos de países con convenios acceder a tratamiento médico estatal en las mismas condiciones que los ciudadanos bolivianos.
        </p>
      </div>
      <div class="visas-content">
        <h2 class="visas-title">Visas</h2>
        <p class="visas-description">
          Bolivia permite la entrada sin visa a ciudadanos de la mayoría de países de América del Sur, América del Norte y Europa por períodos de hasta 90 días. Los ciudadanos de otros países deben consultar el sitio web del Ministerio de Relaciones Exteriores de Bolivia para obtener una lista de países que requieren visa y detalles de aplicación. Si necesitas una visa, puedes solicitarla en una embajada o consulado de Bolivia en tu país de origen. Si no hay una embajada o consulado de Bolivia en tu país, puedes solicitar la visa en la oficina de un país que represente a Bolivia para la emisión de visas.
        </p>
      </div>
      <div class="accessibility-content">
        <h2 class="accessibility-title">Accesibilidad</h2>
        <p class="accessibility-description">
          Los principales transportes comerciales y hoteles en Bolivia han sido equipados con entradas y facilidades accesibles. Sin embargo, siempre es bueno verificar antes de llegar. Los buses interdepartamentales en Bolivia tienen asientos prioritarios (marcados con símbolos internacionales) para personas con movilidad limitada y hay espacios para sillas de ruedas equipados con cinturones de seguridad. Los pasajeros en sillas de ruedas pueden abordar los buses usando las puertas designadas, incluyendo rampas ajustables y botones para abrir las puertas diseñados para uso por pasajeros en sillas de ruedas.
        </p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PlanningMedical'
}
</script>

<style scoped>
/* ===============================
   Sección de Información Médica
   =============================== */
.medical-section {
  padding: 4.5rem 0;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-light);
  border-bottom: 1px solid var(--border-light);
}

.medical-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: left;
}

.medical-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.medical-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 0;
}

.medical-description strong {
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  text-decoration: underline;
  cursor: pointer;
  transition: color 0.3s ease;
}

.medical-description strong:hover {
  color: var(--primary-blue);
}

/* ===============================
   Sección de Visas
   =============================== */
.visas-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: left;
}

.visas-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.visas-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 0;
}

/* ===============================
   Sección de Accesibilidad
   =============================== */
.accessibility-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: left;
}

.accessibility-title {
  font-size: 3.25rem;
  font-weight: var(--font-weight-light);
  color: var(--text-primary);
  letter-spacing: -0.02em;
  line-height: 1.05;
  margin-bottom: 1.5rem;
  text-align: left;
}

.accessibility-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 0;
}

/* Responsive para información médica */
@media (max-width: 991.98px) {
  .medical-content,
  .visas-content,
  .accessibility-content {
    max-width: 100%;
    padding: 0 1rem;
  }
  
  .medical-title,
  .visas-title,
  .accessibility-title {
    text-align: center;
    font-size: 2.5rem;
  }
  
  .medical-description,
  .visas-description,
  .accessibility-description {
    text-align: center;
  }
}

@media (max-width: 575.98px) {
  .medical-section {
    padding: 3rem 0;
  }
  
  .medical-title,
  .visas-title,
  .accessibility-title {
    font-size: 2rem;
  }
  
  .medical-description,
  .visas-description,
  .accessibility-description {
    font-size: 1rem;
  }
}
</style>
