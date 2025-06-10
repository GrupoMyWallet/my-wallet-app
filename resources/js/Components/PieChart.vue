<template>
    <div>
      <Pie v-if="chartData && chartOptions" :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import { Pie } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement
  } from 'chart.js'
  
  // Registrar os elementos necessários para gráfico de pizza
  ChartJS.register(Title, Tooltip, Legend, ArcElement)
  
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
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          padding: 20,
          usePointStyle: true
        }
      },
      title: {
        display: false
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            const label = context.label || ''
            const value = context.parsed || 0
            const total = context.dataset.data.reduce((a, b) => a + b, 0)
            const percentage = ((value / total) * 100).toFixed(1)
            
            return `${label}: ${value.toLocaleString('pt-BR', { 
              style: 'currency', 
              currency: 'BRL' 
            })} (${percentage}%)`
          }
        }
      }
    },
    ...props.options
  }))
  </script>
  
  <style scoped>
  div {
    position: relative;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  </style>