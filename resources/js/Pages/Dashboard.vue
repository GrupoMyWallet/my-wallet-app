<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Bar } from 'vue-chartjs'
import { useDark, useToggle } from "@vueuse/core";

const isDark = useDark();
const toggleDark = useToggle(isDark);

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const props = defineProps({
  dashboard: {
    type: Object,
    default: () => ({
      resumo_anual: { receitas: 0, despesas: 0, saldo: 0 },
      resumo_mensal: null,
      grafico_receitas_despesas: { labels: [], receitas: [], despesas: [] },
      grafico_orcamento_categoria: { labels: [], orcado: [], gasto: [], dados: [] },
      categorias: []
    })
  },
  filters: {
    type: Object,
    default: () => ({
      ano: new Date().getFullYear(),
      mes: null,
      categoria_id: null
    })
  }
})

const filters = ref({ ...props.filters })
const dashboard = ref(props.dashboard)
const loading = ref(false)

const months = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
]

const availableYears = computed(() => {
  const currentYear = new Date().getFullYear()
  return Array.from({ length: 10 }, (_, i) => currentYear - 5 + i)
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value || 0)
}

const getPercentualClass = (percentual) => {
  if (percentual <= 50) return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
  if (percentual <= 80) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
  if (percentual <= 100) return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
  return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
}

// Dados do gráfico Receitas vs Despesas
const receitasDespesasChartData = computed(() => {
  const grafico = dashboard.value?.grafico_receitas_despesas
  if (!grafico || !grafico.labels || grafico.labels.length === 0) {
    return null
  }

  return {
    labels: grafico.labels,
    datasets: [
      {
        label: 'Receitas',
        data: grafico.receitas || [],
        backgroundColor: 'rgba(34, 197, 94, 0.8)',
        borderColor: 'rgba(34, 197, 94, 1)',
        borderWidth: 2,
        borderRadius: 4,
        borderSkipped: false,
      },
      {
        label: 'Despesas',
        data: grafico.despesas || [],
        backgroundColor: 'rgba(239, 68, 68, 0.8)',
        borderColor: 'rgba(239, 68, 68, 1)',
        borderWidth: 2,
        borderRadius: 4,
        borderSkipped: false,
      }
    ]
  }
})

const receitasDespesasChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: 'index',
    intersect: false,
  },
  scales: {
    x: {
      grid: {
        display: false,
      },
      ticks: {
        font: {
          size: 12,
          weight: '500'
        }
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.1)',
      },
      ticks: {
        callback: function (value) {
          return 'R$ ' + value.toLocaleString('pt-BR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          })
        },
        font: {
          size: 11
        }
      }
    }
  },
  plugins: {
    legend: {
      position: 'top',
      labels: {
        usePointStyle: true,
        padding: 20,
        font: {
          size: 12,
          weight: '500'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: 'white',
      bodyColor: 'white',
      borderColor: 'rgba(255, 255, 255, 0.1)',
      borderWidth: 1,
      cornerRadius: 8,
      displayColors: true,
      callbacks: {
        label: function (context) {
          return context.dataset.label + ': ' + formatCurrency(context.parsed.y)
        }
      }
    }
  },
  animation: {
    duration: 1000,
    easing: 'easeInOutQuart'
  }
}

// Dados do gráfico Orçamento vs Categoria
const orcamentoCategoriaChartData = computed(() => {
  const grafico = dashboard.value?.grafico_orcamento_categoria
  if (!grafico || !grafico.labels || grafico.labels.length === 0) {
    return null
  }

  return {
    labels: grafico.labels,
    datasets: [
      {
        label: 'Orçado',
        data: grafico.orcado || [],
        backgroundColor: 'rgba(59, 130, 246, 0.8)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 2,
        borderRadius: 4,
        borderSkipped: false,
      },
      {
        label: 'Gasto',
        data: grafico.gasto || [],
        backgroundColor: 'rgba(239, 68, 68, 0.8)',
        borderColor: 'rgba(239, 68, 68, 1)',
        borderWidth: 2,
        borderRadius: 4,
        borderSkipped: false,
      }
    ]
  }
})

const orcamentoCategoriaChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    mode: 'index',
    intersect: false,
  },
  scales: {
    x: {
      grid: {
        display: false,
      },
      ticks: {
        maxRotation: 45,
        font: {
          size: 12,
          weight: '500'
        }
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.1)',
      },
      ticks: {
        callback: function (value) {
          return 'R$ ' + value.toLocaleString('pt-BR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          })
        },
        font: {
          size: 11
        }
      }
    }
  },
  plugins: {
    legend: {
      position: 'top',
      labels: {
        usePointStyle: true,
        padding: 20,
        font: {
          size: 12,
          weight: '500'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: 'white',
      bodyColor: 'white',
      borderColor: 'rgba(255, 255, 255, 0.1)',
      borderWidth: 1,
      cornerRadius: 8,
      displayColors: true,
      callbacks: {
        label: function (context) {
          return context.dataset.label + ': ' + formatCurrency(context.parsed.y)
        }
      }
    }
  },
  animation: {
    duration: 1000,
    easing: 'easeInOutQuart'
  }
}

const updateDashboard = async () => {
  loading.value = true

  try {
    router.get('/dashboard', filters.value, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page) => {
        dashboard.value = page.props.dashboard
      },
      onFinish: () => {
        loading.value = false
      }
    })
  } catch (error) {
    
    loading.value = false
  }
}

const resetFilters = () => {
  filters.value = {
    ano: new Date().getFullYear(),
    mes: null,
    categoria_id: null
  }
  updateDashboard()
}

onMounted(() => {
  
})
</script>

<template>
  <AppLayout>

    <Head title="Dashboard Financeiro" />


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- Filtros -->
      <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
          <h2 class="text-xl font-semibold mb-4 text-current dark:text-white">Filtros</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-400">Ano</label>
              <select v-model="filters.ano" @change="updateDashboard"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                <option v-for="year in availableYears" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-400">Mês</label>
              <select v-model="filters.mes" @change="updateDashboard"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                <option :value="null">Todos os meses</option>
                <option v-for="(month, index) in months" :key="index" :value="index + 1">
                  {{ month }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-400">Categoria</label>
              <select v-model="filters.categoria_id" @change="updateDashboard"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                <option :value="null">Todas as categorias</option>
                <option v-for="categoria in dashboard.categorias" :key="categoria.id" :value="categoria.id">
                  {{ categoria.nome }}
                </option>
              </select>
            </div>

            <div class="flex items-end">
              <button @click="resetFilters"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 dark:bg-slate-600 dark:hover:bg-slate-700"
                :disabled="loading">
                <span v-if="loading" class="inline-flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                  </svg>
                  Carregando...
                </span>
                <span v-else>Limpar Filtros</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-indigo-500"></div>
      </div>

      <!-- Dashboard Content -->
      <div v-else>
        <!-- Resumos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Resumo Anual -->
          <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-slate-200">
                <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>
                Resumo Anual {{ filters.ano }}
              </h3>
              <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900 rounded-lg">
                  <span class="text-green-700 dark:text-green-300 font-medium">
                    <i class="fas fa-arrow-up mr-2"></i>Receitas:
                  </span>
                  <span class="font-bold text-green-800 dark:text-green-200">
                    {{ formatCurrency(dashboard.resumo_anual?.receitas || 0) }}
                  </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900 rounded-lg">
                  <span class="text-red-700 dark:text-red-300 font-medium">
                    <i class="fas fa-arrow-down mr-2"></i>Despesas:
                  </span>
                  <span class="font-bold text-red-800 dark:text-red-200">
                    {{ formatCurrency(dashboard.resumo_anual?.despesas || 0) }}
                  </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-slate-700 rounded-lg border-t-2 border-gray-300 dark:border-slate-600">
                  <span class="font-bold text-gray-700 dark:text-slate-300">
                    <i class="fas fa-wallet mr-2"></i>Saldo:
                  </span>
                  <span :class="(dashboard.resumo_anual?.saldo || 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                    class="font-bold text-lg">
                    {{ formatCurrency(dashboard.resumo_anual?.saldo || 0) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Resumo Mensal -->
          <div v-if="dashboard.resumo_mensal" class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-slate-200">
                <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                Resumo {{ dashboard.resumo_mensal.nome_mes }} {{ dashboard.resumo_mensal.ano }}
              </h3>
              <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900 rounded-lg">
                  <span class="text-green-700 dark:text-green-300 font-medium">
                    <i class="fas fa-arrow-up mr-2"></i>Receitas:
                  </span>
                  <span class="font-bold text-green-800 dark:text-green-200">
                    {{ formatCurrency(dashboard.resumo_mensal.receitas) }}
                  </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900 rounded-lg">
                  <span class="text-red-700 dark:text-red-300 font-medium">
                    <i class="fas fa-arrow-down mr-2"></i>Despesas:
                  </span>
                  <span class="font-bold text-red-800 dark:text-red-200">
                    {{ formatCurrency(dashboard.resumo_mensal.despesas) }}
                  </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-slate-700 rounded-lg border-t-2 border-gray-300 dark:border-slate-600">
                  <span class="font-bold text-gray-700 dark:text-slate-300">
                    <i class="fas fa-wallet mr-2"></i>Saldo:
                  </span>
                  <span :class="dashboard.resumo_mensal.saldo >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                    class="font-bold text-lg">
                    {{ formatCurrency(dashboard.resumo_mensal.saldo) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card de Informação quando não há mês selecionado -->
          <div v-else class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
              <i class="fas fa-info-circle text-4xl text-blue-500 mb-4"></i>
              <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-slate-200">Resumo Mensal</h3>
              <p class="text-gray-600 dark:text-slate-400">Selecione um mês específico para ver o resumo mensal detalhado.</p>
            </div>
          </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
          <!-- Gráfico Receitas vs Despesas -->
          <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-slate-200">
                <i class="fas fa-chart-bar mr-2 text-purple-500"></i>
                Receitas vs Despesas {{ filters.ano }}
              </h3>
              <div class="h-80">
                <Bar v-if="receitasDespesasChartData && receitasDespesasChartData.labels.length > 0"
                  :data="receitasDespesasChartData" :options="receitasDespesasChartOptions" />
                <div v-else class="flex items-center justify-center h-full text-gray-500 dark:text-slate-500">
                  <div class="text-center">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p>Nenhum dado disponível</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico Orçamento vs Categoria -->
          <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-slate-200">
                <i class="fas fa-chart-line mr-2 text-orange-500"></i>
                Orçamento vs Despesas por Categoria
              </h3>
              <div class="h-80">
                <Bar v-if="orcamentoCategoriaChartData && orcamentoCategoriaChartData.labels.length > 0"
                  :data="orcamentoCategoriaChartData" :options="orcamentoCategoriaChartOptions" />
                <div v-else class="flex items-center justify-center h-full text-gray-500 dark:text-slate-500">
                  <div class="text-center">
                    <i class="fas fa-chart-line text-4xl mb-2"></i>
                    <p>Nenhum orçamento configurado</p>
                  </div>
                </div>
              </div>

              <!-- Tabela de detalhes do orçamento -->
              <div v-if="dashboard.grafico_orcamento_categoria?.dados?.length > 0" class="mt-6">
                <h4 class="text-md font-semibold mb-3 text-gray-700 dark:text-slate-300">Detalhes por Categoria</h4>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-800">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                          Categoria</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                          Orçado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Gasto
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">%
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700">
                      <tr v-for="item in dashboard.grafico_orcamento_categoria.dados" :key="item.categoria">
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-300">{{ item.categoria }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-slate-100">{{ formatCurrency(item.orcado) }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-slate-100">{{ formatCurrency(item.gasto) }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                          <span :class="getPercentualClass(item.percentual)"
                            class="px-2 py-1 rounded-full text-xs font-medium">
                            {{ item.percentual.toFixed(1) }}%
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>



<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

/* Melhorar a aparência dos selects */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}

/* Responsive table */
@media (max-width:1000px) {
  .overflow-x-auto table {
    font-size: 0.875rem;
  }

  .overflow-x-auto th,
  .overflow-x-auto td {
    padding: 0.5rem 0.25rem;
  }
}
</style>