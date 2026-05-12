import { ref, onMounted, onUnmounted } from 'vue'

export function useLazyLoading() {
  const isIntersecting = ref(false)
  const target = ref<HTMLElement>()

  const observer = new IntersectionObserver(
    ([entry]) => {
      isIntersecting.value = entry.isIntersecting
    },
    { threshold: 0.1 }
  )

  onMounted(() => {
    if (target.value) {
      observer.observe(target.value)
    }
  })

  onUnmounted(() => {
    if (target.value) {
      observer.unobserve(target.value)
    }
  })

  return { isIntersecting, target }
}
