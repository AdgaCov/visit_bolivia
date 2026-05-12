<template>
  <InteractiveMap :lugares="lugares" :lugar-activo="lugarActivo" :center="center" :zoom="zoom" />
</template>

<script>
import InteractiveMap from '@/components/InteractiveMap.vue'

export default {
  name: 'PlaceMap',
  components: { InteractiveMap },
  props: {
    lugares: { type: Array, default: () => [] },
    lugarActivo: { type: Object, default: null },
    center: { type: Array, default: () => [-16.5, -68.15] },
    zoom: { type: Number, default: 6 }
  }
}
</script>

<style scoped>
</style> 