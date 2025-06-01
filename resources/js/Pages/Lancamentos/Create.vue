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
    lancamentos: [
        {
            user_id: props.user.id,
            tipo: "despesa",
            valor: null,
            descricao: "",
            categoria_id: "",
            date: "",
            tipo_recorrencia: "none",
            recorrencia_diferente_meses: null,
            fim_da_recorrencia: "",
            esta_ativa: true,
        },
    ],
});

const addLancamento = () => {
    form.lancamentos.push({
        user_id: props.user.id,
        tipo: "",
        valor: null,
        descricao: "",
        categoria_id: "",
        date: "",
        tipo_recorrencia: "none",
        recorrencia_diferente_meses: null,
        fim_da_recorrencia: "",
        esta_ativa: true,
    });
};

const removeLancamento = (index) => {
    form.lancamentos.splice(index, 1);
};

const submit = () => {
    form.post("/lancamentos", {
        onError: (errors) => {
            console.error("Erros do backend:", errors);
        },
    });
};

watchEffect(() => {
  form.lancamentos.forEach((lancamento) => {
    if (lancamento.tipo_recorrencia === 'none' && lancamento.fim_da_recorrencia) {
      lancamento.fim_da_recorrencia = ''
    }
    if (lancamento.tipo_recorrencia !== 'diferente' && lancamento.recorrencia_diferente_meses) {
      lancamento.recorrencia_diferente_meses = null
    }
  })
})

</script>

<template>
    <AppLayout title="Lançamentos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registrar lançamentos
            </h2>
        </template>

        <div class="max-w-4xl mx-auto py-6 px-4">
            <form @submit.prevent="submit" class="space-y-4">
                <div v-for="(lancamento, index) in form.lancamentos" :key="index"
                    class="border rounded-xl p-4 mb-6 shadow-sm bg-white space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-lg">
                            Lançamento {{ index + 1 }}
                        </h3>
                        <button type="button" class="text-red-600 hover:underline text-sm"
                            v-if="form.lancamentos.length > 1" @click="removeLancamento(index)">
                            Remover
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <InputLabel for="tipo" value="Tipo" :required="true"/>
                            <select 
                                v-model="lancamento.tipo"
                                id="tipo"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                required>
                                <option value="" disabled>Selecione...</option>
                                <option value="despesa">Despesa</option>
                                <option value="receita">Receita</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.tipo`]" />
                        </div>
                        <!-- Valor -->
                        <div>
                            <InputLabel for="valor" value="Valor" :required="true"/>
                            <TextInput
                                id="valor"
                                v-mask-decimal
                                placeholder="0.00"
                                v-model="lancamento.valor"
                                class="mt-1 block w-full"
                                step="0.01"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.valor`]" />
                        </div>
                        <!-- Descrição -->
                        <div >
                            <InputLabel for="descricao" value="Descrição" :required="true"/>
                            <TextInput
                                id="descricao"
                                v-model="lancamento.descricao"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.descricao`]" />                  
                        </div>
                        <!-- Categoria -->
                        <div>
                            <InputLabel for="categoria" value="Categoria" :required="true"/>
                            <select
                                id="categoria" 
                                v-model="lancamento.categoria_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
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
                            <InputLabel for="data" value="Data" :required="true"/>
                            <TextInput
                                id="data"
                                v-model="lancamento.data"
                                v-maska="'##/##/####'"
                                class="mt-1 block w-full"
                                placeholder="dia/mês/ano"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.data`]" />
                        </div>
                        <!-- Tipo de Recorrência -->
                        <div>
                            <InputLabel for="tipo_recorrencia" value="Tipo da recorrência" :required="true"/>
                            <select 
                                v-model="lancamento.tipo_recorrencia"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                required>
                                <option value="none">Sem Recorrência</option>
                                <option value="mensal">Mensal</option>
                                <option value="anual">Anual</option>
                                <option value="diferente">Outro (Personalizado)</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.esta_ativa`]" />
                        </div>
                        <!-- Recorrência Diferente (meses) -->
                        <div v-if="lancamento.tipo_recorrencia === 'diferente'">
                            <InputLabel for="recorrencia_diferente_meses" value="Recorrência (em meses)" />
                            <TextInput
                                id="recorrencia_diferente_meses"
                                v-model="lancamento.recorrencia_diferente_meses"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.recorrencia_diferente_meses`]" />  
                        </div>
                        <!-- Fim da Recorrência -->
                        <div v-if="lancamento.tipo_recorrencia != 'none'">
                            <InputLabel for="fim_da_recorrencia" value="Fim da recorrência" />
                            <TextInput
                                id="fim_da_recorrencia"
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
                                id="esta_ativa"
                                v-model="lancamento.esta_ativa"
                                type="checkbox" 
                                class="lancamento-checkbox h-5 w-5  border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm m-1 block "
                            />
                            <InputLabel for="esta_ativa" value="Está ativa? " />
                            <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.tipo_recorrencia`]" />
                        </div>
                        
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                        @click="addLancamento">
                        + Adicionar Lançamento
                    </button>

                    <button type="submit" :disabled="form.processing"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        Salvar Todos
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
