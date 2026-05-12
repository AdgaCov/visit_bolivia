<template>
  <div class="row g-2 align-items-end">
    <div class="col-12 col-md-4">
      <label class="form-label">Búsqueda</label>
      <input v-model="localQuery" type="text" class="form-control" placeholder="Buscar por nombre o descripción" @input="emitChange" />
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Región</label>
      <select v-model="localRegion" class="form-select" @change="emitChange">
        <option value="">Todas</option>
        <option v-for="r in regiones" :key="r" :value="r">{{ r }}</option>
      </select>
    </div>
    <div class="col-12 col-md-4 text-md-end">
      <button class="btn btn-outline-secondary me-2" @click="clear">Limpiar</button>
      <button class="btn btn-primary" @click="emitChange">Aplicar</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PlaceFilters',
  props: {
    query: { type: String, default: '' },
    region: { type: String, default: '' },
    regiones: { type: Array, default: () => [] }
  },
  emits: ['update:query', 'update:region', 'apply'],
  data() {
    return {
      localQuery: this.query,
      localRegion: this.region
    }
  },
  watch: {
    query(val) { this.localQuery = val },
    region(val) { this.localRegion = val }
  },
  methods: {
    emitChange() {
      this.$emit('update:query', this.localQuery)
      this.$emit('update:region', this.localRegion)
      this.$emit('apply')
    },
    clear() {
      this.localQuery = ''
      this.localRegion = ''
      this.emitChange()
    }
  }
}
</script>

<style scoped>
</style> 