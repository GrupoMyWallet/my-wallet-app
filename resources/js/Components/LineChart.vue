<template>
    <div>
      <Line v-if="chartData && chartOptions" :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import { Line } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
  } from 'chart.js'
  
  // Registrar os elementos necessários para gráfico de linha
  ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)
  
  // Props do componente
  const props = defineProps({
    labels: {
      type: Array,
      required: true
    },
    datasets: {
      type: Array,
      required: true
    },
    options: {
      type: Object,
      default: () => ({})
    }
  })
  
  // Dados do gráfico
  const chartData = computed(() => ({
    labels: props.labels,
    datasets: props.datasets
  }))
  
  // Opções do gráfico
  const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: value =>
            value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
        }
      }
    },
    plugins: {
      legend: {
        position: 'top'
      },
      title: {
        display: false
      }
    },
    elements: {
      line: {
        tension: 0.4 // Suaviza as linhas
      },
      point: {
        radius: 4,
        hoverRadius: 6
      }
    },
    interaction: {
      intersect: false,
      mode: 'index'
    },
    ...props.options
  }))
  </script>
  
  <style scoped>
  div {
    position: relative;
    height: 400px;
  }
  </style>