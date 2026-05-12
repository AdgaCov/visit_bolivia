<template>
  <div class="chart-wrapper">
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  Chart,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
} from 'chart.js'
import { Bar } from 'vue-chartjs'

Chart.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend)

const props = defineProps<{
  labels: Array<string | number>
  datasets: Array<{
    label: string
    data: number[]
    backgroundColor: string
  }>
}>()

const chartData = computed(() => ({
  labels: props.labels,
  datasets: props.datasets.map(dataset => ({
    ...dataset,
    hoverBackgroundColor: dataset.backgroundColor.replace('0.85', '1'),
    borderRadius: 4,
    borderSkipped: false,
    animation: {
      duration: 300,
      easing: 'easeOutQuart'
    }
  }))
}) as any)

const chartOptions: any = {
  responsive: true,
  maintainAspectRatio: false,
  animation: {
    duration: 2000,
    easing: 'easeOutQuart',
    delay: (context: any) => {
      return context.dataIndex * 80
    },
    x: {
      type: 'number',
      easing: 'easeOutQuart',
      duration: 2000,
      from: NaN
    },
    y: {
      type: 'number',
      easing: 'easeOutQuart',
      duration: 2000,
      from: (ctx: any) => {
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
    },
    colors: {
      duration: 1000,
      easing: 'easeOutQuart'
    }
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
      position: 'bottom',
      labels: {
        usePointStyle: true
      }
    },
    tooltip: {
      callbacks: {
        label: (context: any) => {
          const value = context.parsed.y
          return `${context.dataset.label}: $${value.toFixed(2)} millones`
        }
      }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  },
  onHover: (event: any, activeElements: any[]) => {
    if (event.native) {
      event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default'
    }
  },
  scales: {
    x: {
      stacked: true,
      grid: { display: false }
    },
    y: {
      stacked: true,
      beginAtZero: true,
      grid: { color: 'rgba(15, 23, 42, 0.05)' },
      ticks: {
        callback: (value: number | string) => {
          const numericValue = typeof value === 'string' ? Number(value) : value
          return `$${numericValue.toFixed(0)}M`
        }
      },
      title: {
        display: true,
        text: 'Millones de USD'
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

