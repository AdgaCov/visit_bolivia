<template>
  <div class="chart-wrapper">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  Chart,
  LineElement,
  PointElement,
  LinearScale,
  CategoryScale,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import { Line } from 'vue-chartjs'

Chart.register(LineElement, PointElement, LinearScale, CategoryScale, Tooltip, Legend, Filler)

interface Dataset {
  label: string
  data: number[]
  borderColor: string
  backgroundColor?: string
  tension?: number
  fill?: boolean | string
  borderWidth?: number
}

const props = defineProps<{
  labels: Array<string | number>
  datasets: Dataset[]
}>()

const chartData = computed(() => ({
  labels: props.labels,
  datasets: props.datasets.map((dataset, index) => ({
    tension: 0.4,
    borderWidth: 3,
    pointRadius: 0,
    pointHoverRadius: 6,
    pointHoverBorderWidth: 3,
    pointHoverBackgroundColor: dataset.borderColor,
    pointHoverBorderColor: '#fff',
    fill: false,
    // Animación de puntos al hacer hover
    pointHoverAnimation: true,
    ...dataset
  }))
}) as any)

const chartOptions: any = {
  responsive: true,
  maintainAspectRatio: false,
  animation: {
    duration: 2000,
    easing: 'easeOutQuart',
    delay: (context: any) => {
      // Animación progresiva: cada punto aparece con un pequeño delay
      return context.dataIndex * 50
    },
    x: {
      type: 'number',
      easing: 'easeOutQuart',
      duration: 2000,
      from: NaN // Anima desde el primer punto
    },
    y: {
      type: 'number',
      easing: 'easeOutQuart',
      duration: 2000,
      from: (ctx: any) => {
        // Anima desde cero
        return ctx.chart.scales.y.getPixelForValue(0)
      }
    }
  },
  animations: {
    x: {
      duration: 2000,
      easing: 'easeOutQuart'
    },
    y: {
      duration: 2000,
      easing: 'easeOutQuart'
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  },
  transitions: {
    show: {
      animations: {
        x: {
          from: 0
        },
        y: {
          from: 0
        }
      }
    },
    hide: {
      animations: {
        x: {
          to: 0
        },
        y: {
          to: 0
        }
      }
    }
  },
  plugins: {
    legend: {
      display: true,
      labels: {
        usePointStyle: true,
        pointStyle: 'circle'
      }
    },
    tooltip: {
      callbacks: {
        label: (context: any) => {
          const value = context.parsed.y
          return `${context.dataset.label}: ${value.toLocaleString('es-BO')}`
        }
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      }
    },
    y: {
      beginAtZero: true,
      ticks: {
        callback: (value: number | string) => {
          const numericValue = typeof value === 'string' ? Number(value) : value
          if (numericValue >= 1000000) {
            return `${(numericValue / 1000000).toFixed(1)}M`
          }
          if (numericValue >= 1000) {
            return `${(numericValue / 1000).toFixed(0)}K`
          }
          return numericValue
        }
      }
    }
  }
}
</script>

<style scoped>
.chart-wrapper {
  width: 100%;
  min-height: 320px;
}
</style>


