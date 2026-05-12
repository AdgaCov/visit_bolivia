<template>
  <component
    :is="to ? 'router-link' : 'div'"
    :to="to || undefined"
    class="inspiration-card"
    @click="$emit('click')"
  >
    <div class="inspiration-image">
      <div class="inspiration-image-media" :style="{ backgroundImage: `url(${image})` }" />
    </div>
    <div class="inspiration-content">
      <h3 class="inspiration-title">{{ title }}</h3>
      <p class="inspiration-text">{{ text }}</p>
    </div>
  </component>
  
</template>

<script>
export default {
  name: 'InspirationCard',
  emits: ['click'],
  props: {
    image: { type: String, required: true },
    title: { type: String, required: true },
    text: { type: String, required: true },
    to: { type: [String, Object], default: null }
  }
}
</script>

<style scoped>
.inspiration-card { text-decoration: none; color: inherit; display: block; }
.inspiration-card:visited { color: inherit; }
.inspiration-card:hover { text-decoration: none; }
.inspiration-card { background: transparent; border: none; border-radius: 0; overflow: visible; box-shadow: none; }
.inspiration-image { height: 260px; position: relative; border-radius: 24px; overflow: hidden; background: var(--light-gray); transition: border-radius 300ms ease; }
.inspiration-image-media { position: absolute; inset: 0; background-size: cover; background-position: center; background-repeat: no-repeat; transform: scale(1); transition: transform 400ms ease; will-change: transform; }
.inspiration-card:hover .inspiration-image-media { transform: scale(1.05); }
.inspiration-card:hover .inspiration-image { border-radius: 24px 12px 24px 24px; }
.inspiration-content { padding: 0.75rem 0 0; }
.inspiration-title { margin: 0.75rem 0 0.25rem; font-family: var(--font-family-base); font-size: 1.75rem; font-weight: 400; letter-spacing: -0.02em; line-height: 1.25; color: var(--text-primary); }
.inspiration-text { margin: 0; color: var(--text-secondary); font-family: var(--font-family-base); font-size: 1.125rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; }
</style>


