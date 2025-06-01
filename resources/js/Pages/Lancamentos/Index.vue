<script setup>

import { ref, computed } from 'vue'
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Tabela from '@/Components/Tabela.vue'
import Modal from '@/Components/Modal.vue'



const props = defineProps({
    lancamentos: Array,
    categorias: Array,
})

function enviandoIndex(index) {

    const dados = JSON.parse(JSON.stringify(props.lancamentos))
    form.item = dados[index];
}

const form = useForm({
    item: {},
});

const showModal = ref(false)
const editRow = ref({})

function closeModal() {

    showModal.value = false
    form.item = {}
}

function onEdit(lancamento) {
    // assign direto ou com clone leve
    form.item = { ...lancamento }
    showModal.value = true
}

function onDelete(lancamento) {
    // tratamento de exclusão…
}


const fields = [
    { key: 'tipo', label: 'Tipo' },
    { key: 'descricao', label: 'Descrição' },
    { key: 'categoria.nome', label: 'Categoria' },
    { key: 'valor', label: 'Valor (R$)' },
    { key: 'data', label: 'Data Cadastro' },
];

const submit = () => {
    form.put(route('lancamentos.update', form.item, form.item.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

</script>

<template>
    <AppLayout title="Lançamentos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lançamentos
            </h2>
        </template>


        <div class="p-6">
            <div class="flex justify-end mb-4">
                <Link :href="route('lancamentos.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Novo Lançamento
                </Link>
            </div>
            <Tabela :data="lancamentos" :fields="fields" @delete="onDelete" @edit="onEdit" />
        </div>

        <!-- MODAL DE EDIÇÃO -->
        <Modal :show="showModal" @close="closeModal">
            <template #default>
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-2.036A9 9 0 1112 3a9 9 0 014.732 3.732zm0 0L21 3" />
                        </svg>
                        Editar Lançamento
                    </h2>
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Tipo -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Tipo <span
                                        class="text-red-500">*</span></label>
                                <select v-model="form.item.tipo"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    required>
                                    <option value="" disabled>Selecione...</option>
                                    <option value="despesa">Despesa</option>
                                    <option value="receita">Receita</option>
                                </select>
                                <span v-if="form.errors.tipo" class="text-red-500 text-sm">{{ form.errors.tipo }}</span>
                            </div>
                            <!-- Valor -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Valor <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.item.valor" type="number" v-mask-decimal
                                placeholder="0.00" step="0.01"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    required>
                                <span v-if="form.errors.valor" class="text-red-500 text-sm">{{ form.errors.valor
                                }}</span>
                            </div>
                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 font-medium mb-1">Descrição <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.item.descricao" type="text"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    maxlength="255" required>
                                <span v-if="form.errors.descricao" class="text-red-500 text-sm">{{ form.errors.descricao
                                }}</span>
                            </div>
                            <!-- Categoria -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Categoria</label>
                                <select v-model="form.item.categoria_id"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">Sem categoria</option>
                                    <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">{{ categoria.nome }}
                                    </option>
                                </select>
                                <span v-if="form.errors.categoria_id" class="text-red-500 text-sm">{{
                                    form.errors.categoria_id }}</span>
                            </div>
                            <!-- Data -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Data <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.item.date" type="date"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    required>
                                <span v-if="form.errors.date" class="text-red-500 text-sm">{{ form.errors.date }}</span>
                            </div>
                            <!-- Tipo de Recorrência -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Tipo de Recorrência <span
                                        class="text-red-500">*</span></label>
                                <select v-model="form.item.tipo_recorrencia"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    required>
                                    <option value="none">Sem Recorrência</option>
                                    <option value="mensal">Mensal</option>
                                    <option value="anual">Anual</option>
                                    <option value="diferente">Outro (Personalizado)</option>
                                </select>
                                <span v-if="form.errors.tipo_recorrencia" class="text-red-500 text-sm">{{
                                    form.errors.tipo_recorrencia }}</span>
                            </div>
                            <!-- Recorrência Diferente (meses) -->
                            <div v-if="form.item.tipo_recorrencia === 'diferente'">
                                <label class="block text-gray-700 font-medium mb-1">Recorrência (em meses)</label>
                                <input v-model="form.item.recorrencia_diferente_meses" type="number" min="1"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <span v-if="form.errors.recorrencia_diferente_meses" class="text-red-500 text-sm">{{
                                    form.errors.recorrencia_diferente_meses }}</span>
                            </div>
                            <!-- Fim da Recorrência -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Fim da Recorrência</label>
                                <input v-model="form.item.fim_da_recorrencia" type="date"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <span v-if="form.errors.fim_da_recorrencia" class="text-red-500 text-sm">{{
                                    form.errors.fim_da_recorrencia }}</span>
                            </div>
                            <!-- Esta Ativa -->
                            <div class="flex items-center mt-4">
                                <input id="esta_ativa" v-model="form.item.esta_ativa" type="checkbox" true-value="1"
                                    false-value="0"
                                    class="form.item-checkbox h-5 w-5 text-blue-600 transition focus:ring-blue-500">
                                <label for="esta_ativa" class="ml-2 text-gray-700 font-medium">Lançamento Ativo</label>
                                <span v-if="form.errors.esta_ativa" class="text-red-500 text-sm ml-2">{{
                                    form.errors.esta_ativa }}</span>
                            </div>
                        </div>
                        <!-- Botões -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" @click="closeModal"
                                class="px-5 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300 font-semibold transition">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="form.processing"
                                class="px-6 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>


    </AppLayout>
</template>