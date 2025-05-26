<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Welcome from '@/Components/Welcome.vue';
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    categorias: Array,
    user: Object,
})

const form = useForm({
    user_id: props.user.id,
    tipo: 'despesa',
    valor: '',
    descricao: '',
    categoria_id: '',
    date: '',
    tipo_recorrencia: '',
    recorrencia_diferente_meses: '',
    fim_da_recorrencia: '',
    esta_ativa: false,
})

function submit() {
    form.post('/lancamentos')
}

</script>

<template>
    <AppLayout title="Lançamentos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Criar lançamento
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label for="tipo" class="block font-semibold">Tipo</label>
                            <select v-model="form.tipo" class="border rounded w-full">
                                <option value="receita">Receita</option>
                                <option value="despesa">Despesa</option>
                            </select>
                            <p v-if="form.errors.tipo" class="text-red-600">{{ form.errors.tipo }}</p>
                        </div>

                        <div>
                            <label class="block font-semibold">Valor</label>
                            <input v-model="form.valor" type="number" step="0.01" class="border rounded w-full" />
                            <p v-if="form.errors.valor" class="text-red-600">{{ form.errors.valor }}</p>
                        </div>

                        <div>
                            <label class="block font-semibold">Descrição</label>
                            <input v-model="form.descricao" type="text" class="border rounded w-full" />
                            <p v-if="form.errors.descricao" class="text-red-600">{{ form.errors.descricao }}</p>
                        </div>

                        <div>
                            <label class="block font-semibold">Categoria</label>
                            <select v-model="form.categoria_id" class="border rounded w-full">
                                <option value="">Selecione uma categoria</option>
                                <option v-for="categoria in props.categorias" :key="categoria.id" :value="categoria.id">
                                    {{ categoria.nome }}
                                </option>
                            </select>
                            <p v-if="form.errors.categoria_id" class="text-red-600">{{ form.errors.categoria_id }}</p>
                        </div>


                        <div>
                            <label class="block font-semibold">Data</label>
                            <input v-model="form.date" type="date" class="border rounded w-full" />
                            <p v-if="form.errors.date" class="text-red-600">{{ form.errors.date }}</p>
                        </div>

                        <div>
                            <label class="block font-semibold">Tipo de Recorrência</label>
                            <select v-model="form.tipo_recorrencia" class="border rounded w-full">
                                <option value="">Nenhuma</option>
                                <option value="mensal">Mensal</option>
                                <option value="anual">Anual</option>
                                <option value="diferente">Diferente</option>
                            </select>
                            <p v-if="form.errors.tipo_recorrencia" class="text-red-600">{{ form.errors.tipo_recorrencia
                            }}
                            </p>
                        </div>

                        <div v-if="form.tipo_recorrencia === 'diferente'">
                            <label class="block font-semibold">Recorrência Diferente (em meses)</label>
                            <input v-model="form.recorrencia_diferente_meses" type="number"
                                class="border rounded w-full" />
                            <p v-if="form.errors.recorrencia_diferente_meses" class="text-red-600">{{
                                form.errors.recorrencia_diferente_meses }}</p>
                        </div>

                        <div>
                            <label class="block font-semibold">Fim da Recorrência</label>
                            <input v-model="form.fim_da_recorrencia" type="date" class="border rounded w-full" />
                            <p v-if="form.errors.fim_da_recorrencia" class="text-red-600">{{
                                form.errors.fim_da_recorrencia
                            }}
                            </p>
                        </div>

                        <div class="flex items-center">
                            <input v-model="form.esta_ativa" type="checkbox" id="ativa" class="mr-2" />
                            <label for="ativa" class="font-semibold">Está ativa</label>
                            <p v-if="form.errors.esta_ativa" class="text-red-600">{{ form.errors.esta_ativa }}</p>
                        </div>

                        <PrimaryButton type="submit" :disabled="form.processing"
                            class="px-4 py-2 bg-blue-600 text-black rounded hover:bg-blue-700 disabled:opacity-50">
                            Salvar
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>

</template>