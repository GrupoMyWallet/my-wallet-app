<script setup>
import { ref, computed, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import Messages from '@/Components/Messages.vue'
import Pagination from '@/Components/Pagination.vue';
import Tabela from '@/Components/Tabela.vue'
import Filtro from '@/Components/Filtro.vue';

const props = defineProps({
  orcamentos: Object,
  categorias: Array,
  filtros: Object,
  categoria_selecionada: Object
})

// Filtros
const filtros = ref({
  ano: props.filtros?.ano || '',
  mes: props.filtros?.mes || '',
  categoria_id: props.filtros?.categoria_id || '',
  tipo: props.filtros?.tipo || ''
})

// Form usando useForm do Inertia
const form = useForm({
  id: '',
  categoria_id: '',
  tipo: '',
  ano: new Date().getFullYear(),
  mes: '',
  valor: ''  // string vazia, não 0
})

function formatarRealInput(event, fieldName, formRef) {
  // Remove tudo que não é número
  let valor = event.target.value.replace(/\D/g, '');

  if (!valor) {
    event.target.value = '';
    formRef[fieldName] = '';
    return;
  }

  // Divide por 100 para centavos
  let valorNumerico = parseInt(valor) / 100;

  // Atualiza o campo do form com valor numérico
  formRef[fieldName] = valorNumerico;

  // Formata para exibição
  const valorFormatado = valorNumerico.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  event.target.value = `R$ ${valorFormatado}`;
}


// Dados estáticos
const meses = {
  1: 'Janeiro', 2: 'Fevereiro', 3: 'Março',
  4: 'Abril', 5: 'Maio', 6: 'Junho',
  7: 'Julho', 8: 'Agosto', 9: 'Setembro',
  10: 'Outubro', 11: 'Novembro', 12: 'Dezembro'
}

const tiposOrcamento = [
  { value: 'anual', label: 'Anual' },
  { value: 'mensal', label: 'Mensal' }
]

const exibirCampoMes = computed(() => form.tipo === 'mensal')

// Computeds
const anosDisponiveis = computed(() => {
  const anoAtual = new Date().getFullYear()
  const anos = []
  for (let i = anoAtual - 5; i <= anoAtual + 5; i++) {
    anos.push(i)
  }
  return anos
})

watch(() => form.tipo, (novoTipo) => {
  if (novoTipo === 'anual') {
    form.mes = ''
  }
})

// Métodos
const abrirModalCadastro = () => {
  form.reset()
  form.ano = new Date().getFullYear()
  if (props.categoria_selecionada) {
    form.categoria_id = props.categoria_selecionada.id
  }
  showModalEdit.value = true
}

const submit = () => {
  const dados = { ...form.data() }
  
  if (dados.tipo === 'anual') {
    dados.mes = null
  }

  if (form.id) {
    form.put(`/orcamentos/${form.id}`, {
      onSuccess: () => closeModal('edit')
    })
  } else {
    form.post('/orcamentos', {
      onSuccess: () => closeModal('edit')
    })
  }
}

const excluirOrcamento = () => {
  router.delete(`/orcamentos/${form.id}`)
  closeModal('delete')
}

const aplicarFiltros = () => {
  router.get('/orcamentos', filtros.value, {
    preserveState: true,
    preserveScroll: true
  })
}

const resetFilters = () => {
  filtros.value = {
    ano: new Date().getFullYear(),
    mes: '',
    categoria_id: '',
    tipo: ''
  }
  aplicarFiltros()
}

const getTipoClass = (tipo) => {
  const classes = {
    'anual': 'bg-purple-100 text-purple-800',
    'mensal': 'bg-blue-100 text-blue-800'
  }
  return classes[tipo] || 'bg-gray-100 text-gray-800'
}

const formatarPeriodo = (orcamento) => {
  if (orcamento.tipo === 'anual') {
    return orcamento.ano
  }
  return `${orcamento.mes_formatado}/${orcamento.ano}`
}

const formatarValor = (valor) => {
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(valor)
}

const fields = [
    { key: 'tipo', label: 'Tipo' },
    { key: 'categoria.nome', label: 'Categoria' },
    { key: 'valor', label: 'Valor (R$)' },
    { key: 'ano', label: 'Ano' },
    { key: 'mes', label: 'Mês' },
];

const showModalDelete = ref(false)
const showModalEdit = ref(false)

function closeModal(modal) {
    if (modal === 'edit') {
        showModalEdit.value = false
    }
    if (modal === 'delete') {
        showModalDelete.value = false
    }
    form.reset();
    form.clearErrors();
}

function openModal(modalRef, orcamento) {
    form.reset(); 
    if (orcamento) {
        Object.keys(orcamento).forEach(key => {
            if (form.hasOwnProperty(key)) {
                form[key] = orcamento[key];
            }
        });
    }
    modalRef.value = true;
}

function onEdit(orcamento) {
    openModal(showModalEdit, orcamento)
}

function onDelete(orcamento) {
    openModal(showModalDelete, orcamento)
}
</script>

<template>
  <AppLayout title="Orçamentos">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
          Orçamentos
        </h2>
      </div>  
      
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Filtros -->
        <Filtro :loading="loading" @update="aplicarFiltros" @reset="resetFilters">
          <template #default="{ onChange }">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Ano</label>
                <select v-model="filtros.ano" @change="onChange" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                  <option value="">Todos os anos</option>
                  <option v-for="ano in anosDisponiveis" :key="ano" :value="ano">{{ ano }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Mês</label>
                <select v-model="filtros.mes" @change="onChange" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                  <option value="">Todos os meses</option>
                  <option v-for="(nome, numero) in meses" :key="numero" :value="numero">{{ nome }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Categoria</label>
                <select v-model="filtros.categoria_id" @change="onChange" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                  <option value="">Todas as categorias</option>
                  <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                    {{ categoria.nome }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-400 mb-1">Tipo</label>
                <select v-model="filtros.tipo" @change="onChange" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                  <option value="">Todos os tipos</option>
                  <option value="anual">Anual</option>
                  <option value="mensal">Mensal</option>
                </select>
              </div>
          </template>
          
        </Filtro>
              
            

        <div class="flex justify-end gap-4 mt-6 mb-6">
          <button
            @click="abrirModalCadastro"
            class="bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Novo Orçamento
          </button>
        </div>
 
        <Tabela :data="orcamentos.data" :fields="fields" @delete="onDelete" @edit="onEdit" />
        <Pagination :pagination="orcamentos" />
        
      </div>
    </div>

    <!-- Modal de Cadastro/Edição -->
    <Modal :show="showModalEdit" @close="() => closeModal('edit')">
      <template #default>
        <div class="p-6">
          <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-slate-200 flex items-center gap-2">
              <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                  viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15.232 5.232l3.536 3.536m-2.036-2.036A9 9 0 1112 3a9 9 0 014.732 3.732zm0 0L21 3" />
              </svg>
              {{ form.id ? 'Editar' : 'Novo' }} Orçamento
          </h2>
          
          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <InputLabel for="categoria_id" value="Categoria" :required="true" />
                <SelectInput v-model="form.categoria_id" :disabled="!!categoria_selecionada" class="dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                  <option value="">Selecione</option>
                  <option v-for="categoria in categorias" :value="categoria.id" :key="categoria.id">{{ categoria.nome }}</option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.categoria_id" />
              </div>

              <div>
                <InputLabel for="tipo" value="Tipo de Orçamento" :required="true" />
                <SelectInput v-model="form.tipo" class="dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                  <option value="">Selecione</option>
                  <option v-for="tipo in tiposOrcamento" :value="tipo.value" :key="tipo.value">{{ tipo.label }}</option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.tipo" />
              </div>

              <div>
                <InputLabel for="valor" value="Valor" :required="true"/>
                <TextInput
                  id="valor"
                  placeholder="R$ 0,00"
                  :value="form.valor ? `R$ ${form.valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : ''"
                  class="mt-1 block w-full"
                  required
                  @input="formatarRealInput($event, 'valor', form)"
                />
                <InputError class="mt-2" :message="form.errors.valor" />
              </div>

              <div>
                <InputLabel for="ano" value="Ano" :required="true"/>
                <TextInput
                  id="ano"
                  v-maska="'####'"
                  min="2000"
                  max="2030"
                  v-model="form.ano"
                  class="mt-1 block w-full"
                  required
                />
                <InputError class="mt-2" :message="form.errors.ano" />
              </div>

              <div v-if="exibirCampoMes" class="md:col-span-2">
                <InputLabel for="mes" value="Mês" :required="true" />
                <SelectInput v-model="form.mes" class="dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                  <option value="">Selecione</option>
                  <option v-for="(nome, valor) in meses" :key="valor" :value="valor">
                    {{ nome }}
                  </option>
                </SelectInput>
                <InputError class="mt-2" :message="form.errors.mes" />
              </div>
            </div>

            <div class="pt-2 flex gap-3">
              <button type="submit"
                class="w-full flex justify-center items-center bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 text-white font-bold py-3 rounded-xl shadow transition disabled:opacity-50"
                :disabled="form.processing">
                <span v-if="form.processing" class="animate-spin mr-2 w-5 h-5 border-2 border-white border-t-transparent rounded-full"></span>
                Salvar Orçamento
              </button>
            </div>
          </form>
        </div>
      </template>
    </Modal>

    <!-- Modal de Confirmação de Exclusão -->
    <Modal :show="showModalDelete" @close="() => closeModal('delete')">
      <template #default>
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4 text-red-600 dark:text-red-400">Confirmar Exclusão</h2>
          <p class="mb-6 dark:text-slate-300">Tem certeza que deseja excluir este orçamento?</p>
          <div class="flex gap-3">
            <button @click="excluirOrcamento" class="bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 text-white px-4 py-2 rounded">
              Excluir
            </button>
            <button @click="closeModal('delete')" class="bg-gray-300 dark:bg-slate-600 hover:bg-gray-400 dark:hover:bg-slate-500 text-gray-800 dark:text-slate-200 px-4 py-2 rounded">
              Cancelar
            </button>
          </div>
        </div>
      </template>
    </Modal>
  </AppLayout>
</template>