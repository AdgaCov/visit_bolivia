<template>
  <section class="events-list">
    <div class="row g-4">
      <div
        v-for="event in events"
        :key="event.id"
        class="col-12 col-sm-6 col-lg-4"
      >
        <InterestCard
          :title="event.title"
          :subtitle="event.date"
          :description="event.location"
          :cover="event.image"
          :badge="'Evento'"
          :to="{ name: 'EventDetail', params: { id: event.id } }"
          :icon="'far fa-calendar-alt'"
        />
      </div>
    </div>
  </section>
</template>

<script>
import InterestCard from '@/components/home/InterestCard.vue'

export default {
  name: 'EventsList',
  components: { InterestCard },
  props: {
    events: { type: Array, required: true },
    to: { type: [String, Object], required: false, default: null }
  }
}
</script>

<style scoped>
</style>



