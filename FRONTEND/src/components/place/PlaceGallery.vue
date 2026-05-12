<template>
  <div class="row g-3">
    <div v-for="(img, idx) in images" :key="idx" class="col-6 col-md-4">
      <img :src="img" :alt="`Imagen ${idx+1}`" class="img-fluid rounded shadow-sm" style="object-fit: cover; width: 100%; height: 160px;" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'PlaceGallery',
  props: {
    images: {
      type: Array,
      default: () => []
    }
  }
}
</script>

<style scoped>
</style> 