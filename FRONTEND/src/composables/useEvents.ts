import { ref, computed, onMounted } from 'vue'
import { eventsService } from '@/services/eventsService'
import type { EventItem, ApiResponse } from '@/types/events'

export function useEvents(autoLoad = true) {
  const events = ref<EventItem[]>([])
  const total = ref<number>(0)
  const loading = ref<boolean>(false)
  const error = ref<string | null>(null)

  async function load(params: Record<string, unknown> = {}) {
    loading.value = true
    error.value = null
    try {
      const res = await eventsService.getAllEvents(params)
      if (res.success && Array.isArray(res.data)) {
        events.value = res.data
        total.value = res.data.length
      } else {
        throw new Error('Error en la respuesta de la API')
      }
    } catch (e: any) {
      error.value = e?.message || 'Error cargando eventos'
    } finally {
      loading.value = false
    }
  }

  const hasEvents = computed(() => events.value.length > 0)

  onMounted(() => {
    if (autoLoad) load()
  })

  return { events, total, loading, error, hasEvents, load }
}

export default useEvents

