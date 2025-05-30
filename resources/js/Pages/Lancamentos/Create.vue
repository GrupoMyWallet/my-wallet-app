<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Welcome from "@/Components/Welcome.vue";
import { useForm } from "@inertiajs/vue3";

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
                            <label class="block text-sm font-medium mb-1">Tipo</label>
                            <select v-model="lancamento.tipo" class="w-full border rounded px-3 py-2">
                                <option value="">Selecione</option>
                                <option value="receita">Receita</option>
                                <option value="despesa">Despesa</option>
                            </select>
                            <p v-if="form.errors[`lancamentos.${index}.tipo`]" class="text-sm text-red-600 mt-1">
                                {{ form.errors[`lancamentos.${index}.tipo`] }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Valor</label>
                            <input v-model="lancamento.valor" v-mask-decimal
                            placeholder="0.00" type="number" step="0.01"
                                class="w-full border rounded px-3 py-2" />
                            <p v-if="form.errors[`lancamentos.${index}.valor`]" class="text-sm text-red-600 mt-1">
                                {{ form.errors[`lancamentos.${index}.valor`] }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Descrição</label>
                            <input v-model="lancamento.descricao" type="text" class="w-full border rounded px-3 py-2" />
                            <p v-if="
                                form.errors[
                                `lancamentos.${index}.descricao`
                                ]
                            " class="text-sm text-red-600 mt-1">
                                {{
                                    form.errors[
                                    `lancamentos.${index}.descricao`
                                    ]
                                }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Categoria</label>
                            <select v-model="lancamento.categoria_id" class="w-full border rounded px-3 py-2">
                                <option value="">Selecione</option>
                                <option v-for="cat in props.categorias" :key="cat.id" :value="cat.id">
                                    {{ cat.nome }}
                                </option>
                            </select>
                            <p v-if="
                                form.errors[
                                `lancamentos.${index}.categoria_id`
                                ]
                            " class="text-sm text-red-600 mt-1">
                                {{
                                    form.errors[
                                    `lancamentos.${index}.categoria_id`
                                    ]
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Data</label>
                            <input v-model="lancamento.data" type="text" v-mask-date placeholder="dd/mm/aaaa"
                                class="w-full border rounded px-3 py-2" />
                            <p v-if="form.errors[`lancamentos.${index}.data`]" class="text-sm text-red-600 mt-1">
                                {{ form.errors[`lancamentos.${index}.data`] }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Tipo de Recorrência</label>
                            <select v-model="lancamento.tipo_recorrencia" class="w-full border rounded px-3 py-2">
                                <option value="none">Nenhuma</option>
                                <option value="mensal">Mensal</option>
                                <option value="anual">Anual</option>
                                <option value="diferente">Diferente</option>
                            </select>
                            <p v-if="
                                form.errors[
                                `lancamentos.${index}.tipo_recorrencia`
                                ]
                            " class="text-sm text-red-600 mt-1">
                                {{
                                    form.errors[
                                    `lancamentos.${index}.tipo_recorrencia`
                                    ]
                                }}
                            </p>
                        </div>

                        <div v-if="lancamento.tipo_recorrencia === 'diferente'">
                            <label class="block text-sm font-medium mb-1">Recorrência Diferente (meses)</label>
                            <input v-model="lancamento.recorrencia_diferente_meses" type="number"
                                class="w-full border rounded px-3 py-2" />
                            <p v-if="
                                form.errors[
                                `lancamentos.${index}.recorrencia_diferente_meses`
                                ]
                            " class="text-sm text-red-600 mt-1">
                                {{
                                    form.errors[
                                    `lancamentos.${index}.recorrencia_diferente_meses`
                                    ]
                                }}
                            </p>
                        </div>

                        <div v-if="
                            lancamento.tipo_recorrencia &&
                            lancamento.tipo_recorrencia !== ''
                        ">
                            <label class="block text-sm font-medium mb-1">Fim da Recorrência</label>
                            <input v-model="lancamento.fim_da_recorrencia" type="date"
                                class="w-full border rounded px-3 py-2" />
                            <p v-if="
                                form.errors[
                                `lancamentos.${index}.fim_da_recorrencia`
                                ]
                            " class="text-sm text-red-600 mt-1">
                                {{
                                    form.errors[
                                    `lancamentos.${index}.fim_da_recorrencia`
                                    ]
                                }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 md:col-span-2 lg:col-span-1 mt-2">
                            <input v-model="lancamento.esta_ativa" type="checkbox" :id="`ativa-${index}`"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded" />
                            <label :for="`ativa-${index}`" class="text-sm font-medium">Está ativa</label>
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
