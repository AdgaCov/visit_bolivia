<template>
  <section class="gastronomy-list">
    <div class="row g-4">
      <div
        v-for="restaurant in restaurants"
        :key="restaurant.id"
        class="col-12 col-sm-6 col-lg-4"
      >
        <GastronomyCard
          :image="restaurant.image"
          :title="restaurant.title"
          :location="restaurant.location"
          :rating="restaurant.rating"
          :description="restaurant.description"
          :badge="restaurant.cuisine"
          :to="{ name: 'GastronomyDetail', params: { id: restaurant.id } }"
        />
      </div>
    </div>
  </section>
</template>

<script>
import GastronomyCard from '@/components/home/GastronomyCard.vue'

export default {
  name: 'GastronomyList',
  components: { GastronomyCard },
  props: {
    restaurants: { type: Array, required: true },
    to: { type: [String, Object], required: false, default: null }
  }
}
</script>

<style scoped>
</style>


