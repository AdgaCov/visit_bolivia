<template>
  <section class="transport-section">
    <div class="container">
      <div class="transport-header">
        <h2 class="transport-title">Cómo moverse por Bolivia</h2>
        <p class="transport-subtitle">Elige tu medio de transporte preferido y explora hasta los rincones más remotos del país.</p>
      </div>

      <div class="transport-content">
        <div class="transport-options">
          <div class="transport-list">
            <button 
              v-for="option in options" 
              :key="option.id"
              class="transport-option"
              :class="{ active: selectedTransport === option.id }"
              @click="$emit('update:selected', option.id)"
            >
              <i :class="option.icon"></i>
              <span>{{ option.name }}</span>
            </button>
          </div>
        </div>

        <div class="transport-map">
          <div class="bolivia-map">
            <slot />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PlanningTransport',
  props: {
    options: { type: Array, required: true },
    selectedTransport: { type: String, required: true }
  },
  emits: ['update:selected']
}
</script>

<style scoped>
.transport-section { padding: 4.5rem 0; background: var(--bg-secondary); border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light); }
.transport-header { text-align: center; margin-bottom: 3rem; }
.transport-title { font-size: 3.25rem; font-weight: var(--font-weight-light); color: var(--text-primary); letter-spacing: -0.02em; line-height: 1.05; margin-bottom: 1.25rem; }
.transport-subtitle { font-size: 1.125rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto; line-height: 1.6; }
.transport-content { display: grid; grid-template-columns: 1fr 2fr; gap: 4rem; align-items: start; }
.transport-list { display: flex; flex-direction: column; gap: 0.75rem; }
.transport-option { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: var(--white); border: 1px solid var(--border-light); border-radius: 12px; font-size: 1rem; font-weight: var(--font-weight-medium); color: var(--text-primary); cursor: pointer; transition: all 0.3s ease; text-align: left; width: 100%; }
.transport-option:hover { background: var(--light-gray); border-color: var(--primary-blue); transform: translateX(4px); }
.transport-option.active { background: var(--primary-blue); color: var(--white); border-color: var(--primary-blue); box-shadow: var(--shadow-md); }
.transport-option i { font-size: 1.25rem; width: 24px; text-align: center; }
.transport-map { background: var(--white); border-radius: 20px; padding: 2rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-light); }
.bolivia-map { width: 100%; height: 500px; border-radius: 16px; overflow: hidden; }
@media (max-width: 991.98px) { .transport-section { padding: 3.5rem 0; } .transport-title { font-size: 2.25rem; line-height: 1.15; } .transport-content { grid-template-columns: 1fr; gap: 2rem; } .transport-list { flex-direction: row; overflow-x: auto; padding-bottom: 1rem; gap: 0.5rem; } .transport-option { min-width: 140px; flex-shrink: 0; padding: 0.75rem 1rem; font-size: 0.9rem; } .transport-option i { font-size: 1rem; } .bolivia-map { height: 400px; } }
@media (max-width: 768px) { .transport-option { flex-direction: column; text-align: center; gap: 0.5rem; min-width: 100px; padding: 0.75rem 0.5rem; } .transport-option span { font-size: 0.8rem; } }
</style>



