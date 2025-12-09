<template>
  <div class="disc-chart-wrapper">
    <Line :data="chartData" :options="options" :height="height" :width="width" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  Chart,
  LineController,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler,
  Tooltip,
  Legend,
  Title,
} from 'chart.js'
import annotationPlugin from 'chartjs-plugin-annotation'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import { Line } from 'vue-chartjs'

Chart.register(
  LineController,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler,
  Tooltip,
  Legend,
  Title,
  annotationPlugin,
  ChartDataLabels,
)

const props = defineProps({
  // Valores normalizados a escala 12–48 para graficar
  d: { type: Number, required: true },
  i: { type: Number, required: true },
  s: { type: Number, required: true },
  c: { type: Number, required: true },
  // Valores crudos (0–120) para mostrar en etiquetas/tooltip si se proveen
  rawD: { type: Number, required: false, default: null },
  rawI: { type: Number, required: false, default: null },
  rawS: { type: Number, required: false, default: null },
  rawC: { type: Number, required: false, default: null },
  // Modo de etiqueta sobre cada punto: 'normalized' (12–48) o 'raw' (0–120)
  labelMode: { type: String, default: 'normalized' },
  width: { type: Number, default: 900 },
  height: { type: Number, default: 520 },
})

// Usar valores crudos en escala 12–48 (sin normalizar)
const clampVal = (v) => Math.max(12, Math.min(48, Number(v) || 12))

const labels = ['D', 'I', 'S', 'C']
const pointColors = ['#d32f2f', '#f57c00', '#388e3c', '#1976d2']

const points = computed(() => [clampVal(props.d), clampVal(props.i), clampVal(props.s), clampVal(props.c)])

const chartData = computed(() => ({
  labels,
  datasets: [
    {
      label: 'Perfil DISC',
      data: points.value,
      borderColor: '#444',
      backgroundColor: 'rgba(25,118,210,0.08)',
      pointBackgroundColor: pointColors,
      pointBorderColor: pointColors,
      pointBorderWidth: 2,
      pointRadius: 7,
      pointHoverRadius: 9,
      fill: false,
      tension: 0, // líneas rectas
    },
  ],
}))

const options = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    x: {
      offset: true,
      grid: { color: '#ccc' },
      ticks: {
        color: '#000',
        font: { weight: '700', size: 14 },
      },
    },
    y: {
      min: 12,
      max: 48,
      ticks: {
        stepSize: 4,
        callback: (val) => `${val}`,
      },
      grid: { color: '#ccc' },
    },
  },
  plugins: {
    legend: { display: false },
    tooltip: {
      enabled: true,
      callbacks: {
        label: (ctx) => {
          const idx = ctx.dataIndex
          const labels = ['D', 'I', 'S', 'C']
          const label = labels[idx] || ''
          const normalized = ctx.parsed.y
          const raws = [props.rawD, props.rawI, props.rawS, props.rawC]
          const raw = raws[idx]
          if (typeof raw === 'number' && !Number.isNaN(raw)) {
            return `${label}: Perfil ${Math.round(normalized)} • Total ${raw}`
          }
          return `${label}: ${Math.round(normalized)}`
        },
      },
    },
    title: { display: false },
    datalabels: {
      align: 'top',
      anchor: 'end',
      offset: 6,
      color: (ctx) => pointColors[ctx.dataIndex] || '#000',
      font: { weight: '700' },
      formatter: (value, ctx) => {
        try {
          const idx = ctx.dataIndex
          const raws = [props.rawD, props.rawI, props.rawS, props.rawC]
          const raw = raws[idx]
          // Si el usuario pide etiquetas crudas y existen, mostramos crudo; de lo contrario, el valor normalizado.
          if (props.labelMode === 'raw' && typeof raw === 'number' && !Number.isNaN(raw)) {
            return raw
          }
          return typeof value === 'number' ? Math.round(value) : value
        } catch (e) {
          return value
        }
      }
    },
    annotation: {
      annotations: {
        band: {
          type: 'box',
          yMin: 28,
          yMax: 32,
          xMin: -0.5,
          xMax: 3.5,
          backgroundColor: 'rgba(128,128,128,0.25)',
          borderWidth: 0,
        },
        midline: {
          type: 'line',
          yMin: 30,
          yMax: 30,
          borderColor: '#666',
          borderWidth: 1,
          borderDash: [4, 3],
        },
      },
    },
  },
  layout: {
    padding: {
      left: 24,
      right: 24,
    },
  },
}))
</script>

<style scoped>
.disc-chart-wrapper { width: 100%; height: 100%; min-height: 360px; }
</style>
