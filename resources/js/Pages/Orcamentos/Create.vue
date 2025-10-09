<script setup>
import { ref, computed } from 'vue'
import AppLayout from "@/Layouts/AppLayout.vue";
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import Messages from '@/Components/Messages.vue'
import { useForm } from "@inertiajs/vue3";


const props = defineProps({
    categorias: Array,
    categoria_selecionada: Object
})

const tiposOrcamento = [
    { value: 'anual', label: 'Anual' },
    { value: 'mensal_padrao', label: 'Mensal (Padrão)' },
    { value: 'mensal_excecao', label: 'Mensal (Exceção)' },
]

const meses = [
  { valor: 1,  nome: 'Janeiro' },
  { valor: 2,  nome: 'Fevereiro' },
  { valor: 3,  nome: 'Março' },
  { valor: 4,  nome: 'Abril' },
  { valor: 5,  nome: 'Maio' },
  { valor: 6,  nome: 'Junho' },
  { valor: 7,  nome: 'Julho' },
  { valor: 8,  nome: 'Agosto' },
  { valor: 9,  nome: 'Setembro' },
  { valor: 10, nome: 'Outubro' },
  { valor: 11, nome: 'Novembro' },
  { valor: 12, nome: 'Dezembro' }
]

// useForm com os campos
const form = useForm({
    categoria_id: props.categoria_selecionada ? props.categoria_selecionada.id : '',
    tipo: '',
    ano: '',
    mes: '',
    valor: ''
})

const exibirCampoMes = computed(() => form.tipo === 'mensal_excecao')

// Envio do formulário
function submit() {
    form.post('/orcamentos', {
        onSuccess: () => form.reset()
    })
}
</script>

<template>

    <AppLayout title="Metas">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-slate-200">Cadastrar Orçamento</h2>
            <button
                type="button"
                @click="$inertia.visit(route('categorias.index'))"
                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 rounded-xl text-gray-600 dark:text-slate-300 font-medium shadow-sm transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Voltar
            </button>
        </div>

        <div class="max-w-xl mx-auto bg-white dark:bg-slate-800 rounded-2xl shadow p-8 space-y-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="categoria_id" value="Categoria" :required="true" />
                        <SelectInput v-model="form.categoria_id" :disabled="!!categoria_selecionada" class="dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option v-for="categoria in categorias" :value="categoria.id" :key="categoria.id">{{ categoria.nome }}</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.categoria_id" />
                        
                    </div>

                    <div>
                        <InputLabel for="tipo" value="Tipos de Orçamento" :required="true" />
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
                            v-mask-decimal
                            placeholder="0.00"
                            v-model="form.valor"
                            class="mt-1 block w-full"
                            step="0.01"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.valor" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="ano" value="Ano" :required="true"/>
                            <TextInput
                                id="ano"
                                v-maska="'####'"
                                min="2000"
                                max="2025"
                                v-model="form.ano"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.ano" />
                        </div>
                        <div v-if="exibirCampoMes">
                            <InputLabel for="mes" value="Mês" :required="true" />
                            <SelectInput v-model="form.mes" class="dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                <option value="">Selecione</option>
                                <option v-for="mes in meses" :key="mes.valor" :value="mes.valor">
                                    {{ mes.nome }}
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
    </AppLayout>
    
</template>