<template>
  <section class="stats-page">
    <div class="container py-5">
      <div class="row align-items-center g-4 mb-5">
        <div class="col-lg-8">
          <p class="badge text-bg-light text-uppercase fw-semibold mb-3">Datos oficiales</p>
          <h1 class="display-5 fw-bold mb-3">
            {{ activeTab === 'travelers' ? 'Llegada de viajeros internacionales' : 'Gasto turístico de visitantes extranjeros' }}
          </h1>
          <p class="text-muted fs-5 mb-0">
            <span v-if="activeTab === 'travelers'">
              Serie histórica publicada por el Instituto Nacional de Estadística y la Dirección General de Migración
              (2008&nbsp;-&nbsp;2025). Los módulos inferiores sólo muestran información proveniente de dicho registro.
            </span>
            <span v-else>
              Datos publicados por el Instituto Nacional de Estadística, Banco Central de Bolivia y Viceministerio de Turismo
              (2008&nbsp;-&nbsp;2024). Valores en millones de dólares estadounidenses.
            </span>
          </p>
        </div>
        <div class="col-lg-4">
          <div class="stats-tabs">
            <button
              @click="activeTab = 'travelers'"
              :class="['tab-btn', { active: activeTab === 'travelers' }]"
            >
              <i class="fas fa-users me-2"></i>
              Viajeros
            </button>
            <button
              @click="activeTab = 'expenses'"
              :class="['tab-btn', { active: activeTab === 'expenses' }]"
            >
              <i class="fas fa-dollar-sign me-2"></i>
              Gastos
            </button>
          </div>
          <button
            v-if="activeTab === 'travelers'"
            @click="downloadTravelersPDF"
            :disabled="downloadingPDF"
            class="download-btn mt-3"
          >
            <i class="fas fa-file-pdf me-2"></i>
            <span v-if="downloadingPDF">Generando PDF...</span>
            <span v-else>Descargar PDF</span>
          </button>
          <button
            v-if="activeTab === 'expenses'"
            @click="downloadExpensesPDF"
            :disabled="downloadingPDF"
            class="download-btn mt-3"
          >
            <i class="fas fa-file-pdf me-2"></i>
            <span v-if="downloadingPDF">Generando PDF...</span>
            <span v-else>Descargar PDF</span>
          </button>
        </div>
      </div>

      <!-- Vista de Viajeros -->
      <div v-if="activeTab === 'travelers'" class="stats-content">
        <div class="row g-4 mt-1">
          <div class="col-lg-6">
            <div class="panel h-100" ref="travelerChart1Ref">
              <div class="panel-header">
                <div>
                  <h4 class="mb-1">Evolución anual de viajeros</h4>
                  <small class="text-muted">Variación total vs. extranjeros</small>
                </div>
                <span class="badge text-bg-primary-subtle text-primary">Dinámico</span>
              </div>
              <TravelerTrendChart :labels="chartLabels" :datasets="trendChartDatasets" />
            </div>
          </div>
          <div class="col-lg-6">
            <div class="panel h-100" ref="travelerChart2Ref">
              <div class="panel-header">
                <div>
                  <h4 class="mb-1">Modalidad de ingreso</h4>
                  <small class="text-muted">Comparativo entre vías aéreas y carreteras</small>
                </div>
                <span class="badge text-bg-info-subtle text-info">Stacked</span>
              </div>
              <TravelerModalityChart :labels="chartLabels" :datasets="modalityDatasets" />
            </div>
          </div>
        </div>

        <div class="panel mt-4">
          <div class="panel-header flex-column flex-md-row align-items-md-center">
            <div>
              <h4 class="mb-1">Serie histórica de viajeros internacionales</h4>
              <small class="text-muted">Fuente: Instituto Nacional de Estadística / Dirección General de Migración</small>
            </div>
            <span class="badge text-bg-light text-uppercase">2008 – 2025</span>
          </div>
          <div class="table-responsive stats-table-wrapper">
            <table class="table stats-table align-middle mb-0">
              <thead>
                <tr>
                  <th>Año</th>
                  <th class="text-end">Aéreo (total)</th>
                  <th class="text-end">Carretero (total)</th>
                  <th class="text-end">Total general</th>
                  <th class="text-end">YoY</th>
                  <th class="text-end">% extranjeros</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="{ entry, yoy } in travelerRows" :key="entry.year">
                  <td>
                    <strong>{{ entry.year }}</strong>
                    <small v-if="entry.provisional" class="text-muted ms-1">{{ getProvisionalLabel(entry) }}</small>
                  </td>
                  <td class="text-end">{{ formatNumber(entry.air.total) }}</td>
                  <td class="text-end">{{ formatNumber(entry.road.total) }}</td>
                  <td class="text-end fw-semibold">{{ formatNumber(entry.overall.total) }}</td>
                  <td class="text-end">
                    <span :class="getYoYBadgeClass(yoy)">{{ formatPercent(yoy) }}</span>
                  </td>
                  <td class="text-end">
                    {{ formatPercent((entry.overall.foreign / entry.overall.total) * 100) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Vista de Gastos -->
      <div v-if="activeTab === 'expenses'" class="stats-content">
        <div class="row g-4 mt-1">
          <div class="col-lg-6">
            <div class="panel h-100" ref="expenseChart1Ref">
              <div class="panel-header">
                <div>
                  <h4 class="mb-1">Evolución del gasto total</h4>
                  <small class="text-muted">Gasto turístico anual en millones USD</small>
                </div>
                <span class="badge text-bg-success-subtle text-success">Dinámico</span>
              </div>
              <ExpenseTrendChart :labels="expenseChartLabels" :datasets="expenseTrendDatasets" />
            </div>
          </div>
          <div class="col-lg-6">
            <div class="panel h-100" ref="expenseChart2Ref">
              <div class="panel-header">
                <div>
                  <h4 class="mb-1">Distribución por categoría</h4>
                  <small class="text-muted">Alojamiento, Bienes y Servicios</small>
                </div>
                <span class="badge text-bg-warning-subtle text-warning">Stacked</span>
              </div>
              <ExpenseCategoryChart :labels="expenseChartLabels" :datasets="expenseCategoryDatasets" />
            </div>
          </div>
        </div>

        <div class="panel mt-4">
          <div class="panel-header flex-column flex-md-row align-items-md-center">
            <div>
              <h4 class="mb-1">Serie histórica de gasto turístico</h4>
              <small class="text-muted">Fuente: Instituto Nacional de Estadística / Banco Central de Bolivia / Viceministerio de Turismo</small>
            </div>
            <span class="badge text-bg-light text-uppercase">2008 – 2024</span>
          </div>
          <div class="table-responsive stats-table-wrapper">
            <table class="table stats-table align-middle mb-0">
              <thead>
                <tr>
                  <th>Año</th>
                  <th class="text-end">Alojamiento</th>
                  <th class="text-end">Compra de Bienes</th>
                  <th class="text-end">Gasto en Servicios</th>
                  <th class="text-end">Total</th>
                  <th class="text-end">YoY</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="{ entry, yoy } in expenseRows" :key="entry.year">
                  <td>
                    <strong>{{ entry.year }}</strong>
                    <small v-if="entry.provisional" class="text-muted ms-1">(preliminar)</small>
                  </td>
                  <td class="text-end">${{ formatCurrency(entry.expenses.accommodation) }}</td>
                  <td class="text-end">${{ formatCurrency(entry.expenses.goods.total) }}</td>
                  <td class="text-end">${{ formatCurrency(entry.expenses.services.total) }}</td>
                  <td class="text-end fw-semibold">${{ formatCurrency(entry.expenses.total) }}</td>
                  <td class="text-end">
                    <span :class="getYoYBadgeClass(yoy)">{{ formatPercent(yoy) }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import TravelerTrendChart from '@/components/stats/TravelerTrendChart.vue'
import TravelerModalityChart from '@/components/stats/TravelerModalityChart.vue'
import ExpenseTrendChart from '@/components/stats/ExpenseTrendChart.vue'
import ExpenseCategoryChart from '@/components/stats/ExpenseCategoryChart.vue'
import travelerYearSummaries, { TravelerYearSummary } from '@/data/travelers'
import touristExpenseYears, { TouristExpenseYear } from '@/data/tourist-expenses'
import pdfService from '@/services/pdf.service'

const activeTab = ref<'travelers' | 'expenses'>('travelers')
const downloadingPDF = ref(false)

// Referencias a los elementos de los gráficos
const travelerChart1Ref = ref<HTMLElement | null>(null)
const travelerChart2Ref = ref<HTMLElement | null>(null)
const expenseChart1Ref = ref<HTMLElement | null>(null)
const expenseChart2Ref = ref<HTMLElement | null>(null)

const chartLabels = travelerYearSummaries.map(entry => entry.year.toString())
const expenseChartLabels = touristExpenseYears.map(entry => entry.year.toString())

const trendChartDatasets = [
  {
    label: 'Total general',
    data: travelerYearSummaries.map(entry => entry.overall.total),
    borderColor: '#2563eb',
    backgroundColor: 'rgba(37, 99, 235, 0.12)'
  },
  {
    label: 'Visitantes extranjeros',
    data: travelerYearSummaries.map(entry => entry.overall.foreign),
    borderColor: '#f97316',
    backgroundColor: 'rgba(249, 115, 22, 0.12)'
  }
]

const modalityDatasets = [
  {
    label: 'Aéreo',
    data: travelerYearSummaries.map(entry => entry.air.total),
    backgroundColor: 'rgba(14, 165, 233, 0.85)'
  },
  {
    label: 'Carretero',
    data: travelerYearSummaries.map(entry => entry.road.total),
    backgroundColor: 'rgba(37, 99, 235, 0.85)'
  }
]

const travelerRows = travelerYearSummaries.map((entry, index) => {
  const previous = index > 0 ? travelerYearSummaries[index - 1] : undefined
  const yoy = previous
    ? ((entry.overall.total - previous.overall.total) / previous.overall.total) * 100
    : null
  return { entry, yoy }
})

function formatNumber(value: number) {
  return value.toLocaleString('es-BO')
}

function formatPercent(value: number | null) {
  if (value === null || Number.isNaN(value)) return '—'
  const formatted = value.toFixed(1)
  return `${value >= 0 ? '+' : ''}${formatted}%`
}

function getYoYBadgeClass(value: number | null) {
  if (value === null) return 'badge text-bg-secondary-subtle text-secondary'
  return value >= 0
    ? 'badge text-bg-success-subtle text-success'
    : 'badge text-bg-danger-subtle text-danger'
}

function getProvisionalLabel(entry: TravelerYearSummary) {
  return entry.provisional ? '(preliminar)' : ''
}

// Datos de gastos
const expenseTrendDatasets = [
  {
    label: 'Gasto total',
    data: touristExpenseYears.map(entry => entry.expenses.total),
    borderColor: '#10b981',
    backgroundColor: 'rgba(16, 185, 129, 0.12)'
  }
]

const expenseCategoryDatasets = [
  {
    label: 'Alojamiento',
    data: touristExpenseYears.map(entry => entry.expenses.accommodation),
    backgroundColor: 'rgba(59, 130, 246, 0.85)'
  },
  {
    label: 'Compra de Bienes',
    data: touristExpenseYears.map(entry => entry.expenses.goods.total),
    backgroundColor: 'rgba(168, 85, 247, 0.85)'
  },
  {
    label: 'Gasto en Servicios',
    data: touristExpenseYears.map(entry => entry.expenses.services.total),
    backgroundColor: 'rgba(236, 72, 153, 0.85)'
  }
]

const expenseRows = touristExpenseYears.map((entry, index) => {
  const previous = index > 0 ? touristExpenseYears[index - 1] : undefined
  const yoy = previous
    ? ((entry.expenses.total - previous.expenses.total) / previous.expenses.total) * 100
    : null
  return { entry, yoy }
})

function formatCurrency(value: number) {
  return value.toFixed(2)
}

// Funciones para descargar PDFs
async function downloadTravelersPDF() {
  downloadingPDF.value = true
  try {
    // Esperar un momento para que los gráficos se rendericen completamente
    await new Promise(resolve => setTimeout(resolve, 500))

    const tableData = travelerRows.map(({ entry, yoy }) => ({
      year: entry.year,
      air: entry.air.total,
      road: entry.road.total,
      total: entry.overall.total,
      yoy: yoy,
      foreignPercent: (entry.overall.foreign / entry.overall.total) * 100,
      provisional: entry.provisional
    }))

    await pdfService.generateTravelersPDF(
      travelerChart1Ref.value,
      travelerChart2Ref.value,
      tableData,
      {
        title: 'Llegada de Viajeros Internacionales',
        subtitle: 'Serie histórica de visitantes',
        source: 'Instituto Nacional de Estadística / Dirección General de Migración',
        period: '2008 - 2025'
      }
    )
  } catch (error) {
    console.error('Error generando PDF de viajeros:', error)
    alert('Error al generar el PDF. Por favor, intente nuevamente.')
  } finally {
    downloadingPDF.value = false
  }
}

async function downloadExpensesPDF() {
  downloadingPDF.value = true
  try {
    // Esperar un momento para que los gráficos se rendericen completamente
    await new Promise(resolve => setTimeout(resolve, 500))

    const tableData = expenseRows.map(({ entry, yoy }) => ({
      year: entry.year,
      accommodation: entry.expenses.accommodation,
      goods: entry.expenses.goods.total,
      services: entry.expenses.services.total,
      total: entry.expenses.total,
      yoy: yoy,
      provisional: entry.provisional
    }))

    await pdfService.generateExpensesPDF(
      expenseChart1Ref.value,
      expenseChart2Ref.value,
      tableData,
      {
        title: 'Gasto Turístico de Visitantes Extranjeros',
        subtitle: 'Gastos en millones de dólares estadounidenses',
        source: 'Instituto Nacional de Estadística / Banco Central de Bolivia / Viceministerio de Turismo',
        period: '2008 - 2024'
      }
    )
  } catch (error) {
    console.error('Error generando PDF de gastos:', error)
    alert('Error al generar el PDF. Por favor, intente nuevamente.')
  } finally {
    downloadingPDF.value = false
  }
}
</script>

<style scoped>
.stats-page {
  background: radial-gradient(circle at top, #f8fafc, #fff);
  min-height: 100vh;
}

.panel {
  background: #fff;
  border-radius: 18px;
  padding: 1.75rem;
  border: 1px solid rgba(15, 23, 42, 0.05);
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.stats-table-wrapper {
  max-height: 520px;
  overflow: auto;
  border: 1px solid rgba(15, 23, 42, 0.08);
  border-radius: 16px;
}

.stats-table thead th {
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  color: rgba(15, 23, 42, 0.6);
  border-bottom-width: 1px;
}

.stats-table tbody td {
  border-top-color: rgba(15, 23, 42, 0.06);
}

.stats-table tbody tr:hover {
  background: rgba(15, 23, 42, 0.02);
}

.stats-tabs {
  display: flex;
  gap: 0.75rem;
  background: #fff;
  padding: 0.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
  border: 1px solid rgba(15, 23, 42, 0.05);
}

.tab-btn {
  flex: 1;
  padding: 0.75rem 1.25rem;
  border: none;
  background: transparent;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  color: rgba(15, 23, 42, 0.6);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tab-btn:hover {
  background: rgba(15, 23, 42, 0.04);
  color: rgba(15, 23, 42, 0.8);
}

.tab-btn.active {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #fff;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.stats-content {
  animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.download-btn {
  width: 100%;
  padding: 0.75rem 1.25rem;
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.download-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
}

.download-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}
</style>


