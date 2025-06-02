<template>
    <AppLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Dashboard Financeiro
        </h2>
      </template>
  
      <div class="p-6 space-y-8">
        <!-- Seletor de ano -->
        <div class="flex items-center gap-4">
          <label for="anoSelect" class="font-medium">Ano:</label>
          <select
            id="anoSelect"
            v-model="anoSelecionado"
            @change="carregarGraficoReceitaDespesa"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
          >
            <option v-for="ano in anosDisponiveis" :key="ano" :value="ano">
              {{ ano }}
            </option>
          </select>
        </div>
  
        <!-- Gráfico de Receitas vs Despesas -->
        <div class="bg-white rounded-xl shadow p-6">
          <h3 class="text-lg font-semibold mb-4">Receitas vs Despesas (por mês)</h3>
          <BarChart
            v-if="chartReady"
            :labels="labels"
            :datasets="datasets"
            :options="opcoesGrafico"
          />
          <div v-else class="text-gray-500 flex justify-center items-center h-64">
            Carregando gráfico...
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import AppLayout from '@/Layouts/AppLayout.vue'
  import BarChart from '@/Components/BarChart.vue'
  
  // --- STATE REATIVO ---
  const anoSelecionado = ref(new Date().getFullYear())
  const anosDisponiveis = ref([2023, 2024, 2025]) // Ajuste conforme necessário
  const labels = ref([])
  const datasets = ref([])
  const chartReady = ref(false)
  
  // Opções adicionais para o Chart.js
  const opcoesGrafico = ref({
    plugins: {
      title: { display: false }
    }
  })
  
  async function carregarGraficoReceitaDespesa() {
  chartReady.value = false

  try {
    // Ajuste aqui para a rota correta (web.php ou api.php conforme sua configuração)
    const response = await axios.get('/dashboard/receita-vs-despesa', {
      params: { ano: anoSelecionado.value }
    })

    if (!response.data.success) {
      console.error('Backend retornou success=false:', response.data.message)
      return
    }

    const rawDados = response.data.data.dados
    console.log('rawDados que veio do backend:', rawDados)
    if (!Array.isArray(rawDados) || rawDados.length === 0) {
      console.error('rawDados está vazio ou não é array.')
      return
    }

    // Array de abreviações (pt-BR) para 1–12
    const mesesPtBR = [
      'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
      'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
    ]

    // Agora usamos item.mes_numero (1–12) para indexar mesesPtBR
    labels.value = rawDados.map(item => {
      if (
        typeof item.mes_numero === 'number' &&
        item.mes_numero >= 1 &&
        item.mes_numero <= 12
      ) {
        return mesesPtBR[item.mes_numero - 1]
      } else {
        console.warn('item.mes_numero inválido ou ausente:', item)
        return '??'
      }
    })

    // Para os valores, usamos item.receitas e item.despesas
    const arrayReceita = rawDados.map(item =>
      item.receitas != null ? Number(item.receitas) : 0
    )
    const arrayDespesa = rawDados.map(item =>
      item.despesas != null ? Number(item.despesas) : 0
    )

    datasets.value = [
      {
        label: 'Receitas',
        data: arrayReceita,
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      },
      {
        label: 'Despesas',
        data: arrayDespesa,
        backgroundColor: 'rgba(255, 99, 132, 0.6)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1
      }
    ]

    chartReady.value = true
  } catch (error) {
    console.error('Erro ao buscar dados do gráfico:', error)
  }
}

  
  onMounted(() => {
    carregarGraficoReceitaDespesa()
  })
  </script>
  
  <style scoped>
  /* Se precisar de ajustes extras, coloque aqui. */
  </style>
  