<template>
  <AdminLocations
    page-title="Destinos"
    page-description="Destinos turísticos con nombre, descripción, fotos, videos, latitud y longitud."
    entity-label="destino"
    fixed-type="destination"
    :show-type-filter="false"
    :show-type-field="false"
    destination-mode
    header-icon="fas fa-route text-primary me-2"
    new-button-label="Nuevo destino"
    new-modal-prefix="Nuevo"
  />
</template>

<script>
import AdminLocations from './AdminLocations.vue'

export default {
  name: 'AdminDestinations',
  components: {
    AdminLocations
  }
}
</script>
