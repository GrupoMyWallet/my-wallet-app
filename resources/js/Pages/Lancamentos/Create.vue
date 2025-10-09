<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { watchEffect } from 'vue'
import { useForm } from "@inertiajs/vue3";
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
    categorias: Array,
    user: Object,
});

const form = useForm({
    lancamentos: [],
});

function createNewLancamento() {
    return {
        tipo: 'despesa',
        valor: '',
        descricao: '',
        categoria_id: '',
        data: new Date().toLocaleDateString('pt-BR'),
        intervalo_meses: 0,             
        intervalo_selecionado: '0',
        valor_intervalo_custom: 1,
        fim_da_recorrencia: '',
        esta_ativa: true,
    };
}

const addLancamento = () => {
    form.lancamentos.push(createNewLancamento());
};

const removeLancamento = (index) => {
    form.lancamentos.splice(index, 1);
};

if (form.lancamentos.length === 0) {
    addLancamento();
}

const submit = () => {
    const dataToSubmit = {
        ...form.data(),
        lancamentos: form.lancamentos.map(lancamento => {
            
            const { intervalo_selecionado, valor_intervalo_custom, ...lancamentoParaEnviar } = lancamento;
            return lancamentoParaEnviar; 
        })
    };

    
    form.transform(() => dataToSubmit) 
        .post(route('lancamentos.store'), {
            onSuccess: () => {
                console.log('Formulário enviado com sucesso!');
                
            },
            onError: (errors) => {
                console.error('Erros no formulário:', errors);
                
            },
            onFinish: () => {
                form.processing = false;
            }
        });
};

function updateIntervaloMeses(lancamento) {
    if (lancamento.intervalo_selecionado === 'custom') {
        
        if (!lancamento.valor_intervalo_custom || lancamento.valor_intervalo_custom < 1) {
            lancamento.valor_intervalo_custom = 1;
        }
        
        lancamento.intervalo_meses = parseInt(lancamento.valor_intervalo_custom) || 1;
    } else {
        
        lancamento.intervalo_meses = parseInt(lancamento.intervalo_selecionado);
    }
}

</script>

<template>
    <AppLayout title="Lançamentos de Receitas e Despesas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
                Registrar lançamentos
            </h2>
        </template>

        <div class="max-w-4xl mx-auto py-6 px-4">
            <form @submit.prevent="submit" class="space-y-4">
                <div v-for="(lancamento, index) in form.lancamentos" :key="index"
                    class="border rounded-xl p-4 mb-6 shadow-sm bg-white dark:bg-slate-800 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-lg dark:text-slate-200">
                            Lançamento {{ index + 1 }}
                        </h3>
                        <button type="button" class="text-red-600 dark:text-red-400 hover:underline text-sm"
                            v-if="form.lancamentos.length > 1" @click="removeLancamento(index)">
                            Remover
                        </button>
                    </div>

                    <!-- IDs dinâmicos para acessibilidade e funcionalidade correta com múltiplos itens -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <InputLabel :for="'tipo_' + index" value="Tipo" :required="true"/>
                            <select
                                v-model="lancamento.tipo"
                                :id="'tipo_' + index"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                required>
                                <option value="" disabled>Selecione...</option>
                                <option value="despesa">Despesa</option>
                                <option value="receita">Receita</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.tipo`]" />
                        </div>
                        <div>
                            <InputLabel :for="'valor_' + index" value="Valor" :required="true"/>
                            <TextInput
                                :id="'valor_' + index"
                                v-mask-decimal
                                placeholder="0.00"
                                v-model="lancamento.valor"
                                class="mt-1 block w-full"
                                step="0.01"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.valor`]" />
                        </div>
                        <div>
                            <InputLabel :for="'descricao_' + index" value="Descrição" :required="true"/>
                            <TextInput
                                :id="'descricao_' + index"
                                v-model="lancamento.descricao"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.descricao`]" />
                        </div>
                        <div>
                            <InputLabel :for="'categoria_' + index" value="Categoria" :required="true"/>
                            <select
                                :id="'categoria_' + index"
                                v-model="lancamento.categoria_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                required>
                                <option value="">Selecione</option>
                                <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">{{ categoria.nome }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.categoria_id`]" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                        <div>
                            <InputLabel :for="'data_' + index" value="Data" :required="true"/>
                            <TextInput
                                :id="'data_' + index"
                                v-model="lancamento.data"
                                v-maska="'##/##/####'"
                                class="mt-1 block w-full"
                                placeholder="dia/mês/ano"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.data`]" />
                        </div>

                        <!-- Intervalo em Meses (Select) -->
                        <div>
                            <InputLabel :for="'intervalo_meses_select_' + index" value="Recorrência (meses)" :required="true"/>
                            <select
                                v-model="lancamento.intervalo_selecionado"
                                :id="'intervalo_meses_select_' + index"
                                @change="updateIntervaloMeses(lancamento)"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                required>
                                <option value="0">Sem Recorrência</option>
                                <option value="1">Mensal (1 mês)</option>
                                <option value="12">Anual (12 meses)</option>
                                <option value="custom">Personalizado</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.intervalo_meses`]" />
                        </div>

                        <!-- Campo personalizado para intervalo -->
                        <div v-if="lancamento.intervalo_selecionado === 'custom'">
                            <InputLabel :for="'valor_intervalo_custom_' + index" value="Intervalo (meses)" :required="lancamento.intervalo_selecionado === 'custom'"/>
                            <TextInput
                                :id="'valor_intervalo_custom_' + index"
                                v-model.number="lancamento.valor_intervalo_custom"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                                placeholder="Ex: 3"
                                :required="lancamento.intervalo_selecionado === 'custom'"
                                @input="lancamento.intervalo_meses = parseInt(lancamento.valor_intervalo_custom) || 1"
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.intervalo_meses`]" />
                        </div>

                        <!-- Fim da Recorrência -->
                        <div v-if="lancamento.intervalo_meses !== 0"> 
                            <InputLabel :for="'fim_da_recorrencia_' + index" value="Fim da recorrência" />
                            <TextInput
                                :id="'fim_da_recorrencia_' + index"
                                v-model="lancamento.fim_da_recorrencia"
                                v-maska="'##/##/####'"
                                placeholder="dia/mês/ano"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.fim_da_recorrencia`]" />
                        </div>

                        <!-- Esta Ativa -->
                        <div class="flex items-center mt-4">
                            <input
                                :id="'esta_ativa_' + index"
                                v-model="lancamento.esta_ativa"
                                type="checkbox"
                                class="lancamento-checkbox h-5 w-5 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm m-1 block dark:bg-slate-700 dark:border-slate-600"
                            />
                            <InputLabel :for="'esta_ativa_' + index" value="Está ativa?" class="ml-2 mb-0 dark:text-slate-300" /> 
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.esta_ativa`]" />
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <button type="button" class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition"
                        @click="addLancamento">
                        + Adicionar Lançamento
                    </button>

                    <button type="submit" :disabled="form.processing"
                        class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded hover:bg-green-700 dark:hover:bg-green-800 transition">
                        Salvar Todos
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>