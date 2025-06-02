<template>
    <div>
      <Bar v-if="chartData && chartOptions" :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import { Bar } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
  } from 'chart.js'
  
  // Registrar os elementos mínimos do Chart.js
  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
  
  // Definimos as props: labels (array), datasets (array) e options (objeto opcional)
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
  
  // Montamos reativamente o objeto que será passado a <Bar :data="...">
  const chartData = computed(() => ({
    labels: props.labels,
    datasets: props.datasets
  }))
  
  // Montamos as opções padrão e mesclamos com props.options
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
    ...props.options
  }))
  </script>
  
  <style scoped>
  /* Altura mínima para o canvas aparecer bem */
  div {
    position: relative;
    height: 400px;
  }
  </style>
  