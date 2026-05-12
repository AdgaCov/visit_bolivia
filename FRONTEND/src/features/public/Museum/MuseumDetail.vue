<template>
  <GastronomyPage :id="route.params.id" />
</template>

<script setup>
import { useRoute } from 'vue-router'
import GastronomyPage from '@/features/public/Gastronomy/GastronomyPage.vue'

const route = useRoute()
</script>

<style scoped>
</style>


