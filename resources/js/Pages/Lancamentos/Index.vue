<script setup>

import { ref, reactive } from 'vue'
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Tabela from '@/Components/Tabela.vue'
import Modal from '@/Components/Modal.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import Messages from '@/Components/Messages.vue'
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    lancamentos: Object,
    categorias: Array,
})

const form = useForm({
    item: {},
});

const showModalDelete = ref(false)
const showModalEdit = ref(false)

function closeModal(modal) {
    
    if (modal === 'edit') {
        showModalEdit.value = false
    }
    if (modal === 'delete') {
        showModalDelete.value = false
    }

    nextTick(() => {
        form.item = {};
    });
}

function openModal(modalRef, lancamento) {
    form.item = { ...lancamento }
    modalRef.value = true
}

function onEdit(lancamento) {
    openModal(showModalEdit, lancamento)
}

function onDelete(lancamento) {
    openModal(showModalDelete, lancamento)
}

const fields = [
    { key: 'tipo', label: 'Tipo' },
    { key: 'descricao', label: 'Descrição' },
    { key: 'categoria.nome', label: 'Categoria' },
    { key: 'valor', label: 'Valor (R$)' },
    { key: 'data', label: 'Data Cadastro' },
    { key: 'tipo_recorrencia', label: 'Recorrência' },
];

const submit = () => {
    form.put(route('lancamentos.update', form.item, form.item.id), {
        preserveScroll: true,
        onSuccess: () => closeModal('edit'),
    });
};

const deleteItem = () => {
    form.delete(route('lancamentos.destroy', form.item.id), {
        preserveScroll: true,
        onSuccess: () => closeModal('delete'),
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

        <Messages/>

        <div class="p-6">
            <div class="flex justify-end mb-4">
                <Link :href="route('lancamentos.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Novo Lançamento
                </Link>
            </div>
            <Tabela :data="lancamentos.data" :fields="fields" @delete="onDelete" @edit="onEdit" />

            <Pagination :pagination="lancamentos" />
        </div>

        <!-- MODAL DE EDIÇÃO -->
        <Modal :show="showModalEdit" @close="() => closeModal('edit')">
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
                                <InputLabel for="tipo" value="Tipo" :required="true" />
                                <select v-model="form.item.tipo" id="tipo"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    required>
                                    <option value="" disabled>Selecione...</option>
                                    <option value="despesa">Despesa</option>
                                    <option value="receita">Receita</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.tipo" />
                            </div>
                            <!-- Valor -->
                            <div>
                                <InputLabel for="valor" value="Valor" :required="true" />
                                <TextInput id="valor" v-mask-decimal type="number" inputmode="numeric"
                                    placeholder="0.00" v-model="form.item.valor" class="mt-1 block w-full" step="0.01"
                                    required />
                                <InputError class="mt-2" :message="form.errors.valor" />
                            </div>
                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <InputLabel for="descricao" value="Descrição" :required="true" />
                                <TextInput id="descricao" v-model="form.item.descricao" type="text"
                                    class="mt-1 block w-full" required />
                                <InputError class="mt-2" :message="form.errors.descricao" />
                            </div>
                            <!-- Categoria -->
                            <div>
                                <InputLabel for="categoria" value="Categoria" :required="true" />
                                <SelectInput id="categoria" v-model="form.item.categoria_id"
                                    required>
                                    <option value="">Sem categoria</option>
                                    <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">{{
                                        categoria.nome }}
                                    </option>
                                </SelectInput>
                                <InputError class="mt-2" :message="form.errors.categoria_id" />
                            </div>
                            <!-- Data -->
                            <div>
                                <InputLabel for="data" value="Data" :required="true" />
                                <TextInput id="data" v-model="form.item.data" v-maska="'##/##/####'"
                                    class="mt-1 block w-full" required />
                                <InputError class="mt-2" :message="form.errors.data" />
                            </div>
                            <!-- Tipo de Recorrência -->
                            <div>
                                <InputLabel for="tipo_recorrencia" value="Tipo da recorrência" :required="true" />
                                <SelectInput 
                                    v-model="form.item.tipo_recorrencia"
                                    required>
                                    <option value="nenhuma">Sem Recorrência</option>
                                    <option value="mensal">Mensal</option>
                                    <option value="anual">Anual</option>
                                    <option value="diferente">Outro (Personalizado)</option>
                                </SelectInput>
                                <InputError class="mt-2" :message="form.errors[`lancamentos.${index}.tipo_recorrencia`]" />
                            </div>
                            <!-- Recorrência Diferente (meses) -->
                            <div v-if="form.item.tipo_recorrencia === 'diferente'">
                                <InputLabel for="recorrencia_diferente_meses" value="Recorrência (em meses)" />
                                <TextInput id="recorrencia_diferente_meses"
                                    v-model="form.item.recorrencia_diferente_meses" type="number" min="1"
                                    class="mt-1 block w-full" />
                                <InputError class="mt-2" :message="form.errors.recorrencia_diferente_meses" />
                            </div>
                            <!-- Fim da Recorrência -->
                            <div v-if="form.item.tipo_recorrencia !== 'nenhuma'">
                                <InputLabel for="fim_da_recorrencia" value="Fim da Recorrência" />
                                <TextInput id="fim_da_recorrencia" v-model="form.item.fim_da_recorrencia"
                                    v-maska="'##/##/####'" class="mt-1 block w-full" />
                                <InputError class="mt-2" :message="form.errors.fim_da_recorrencia" />
                            </div>
                            <!-- Esta Ativa -->
                            <div class="flex items-center mt-4">
                                <input id="esta_ativa" v-model="form.item.esta_ativa" type="checkbox"
                                    class="form.item-checkbox h-5 w-5  border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm m-1 block " />
                                <InputLabel for="esta_ativa" value="Está ativa? " />
                                <InputError class="mt-2" :message="form.errors.esta_ativa" />

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

        <Modal :show="showModalDelete" @close="closeModal('delete')">
            <template #default>
                <div class="flex flex-col items-center px-4 py-6">
                    <!-- Ícone de alerta -->
                    <div class="bg-red-100 rounded-full p-3 mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <!-- Título -->
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                        Confirmar exclusão
                    </h3>
                    <!-- Mensagem -->
                    <p class="text-gray-600 dark:text-gray-300 text-center mb-6">
                        Tem certeza que deseja excluir
                        <span class="font-semibold text-red-700">{{ itemToDelete?.nome || 'este item' }}</span>?
                        <br>
                        Essa ação não poderá ser desfeita.
                    </p>
                    <!-- Botões -->
                    <div class="flex gap-4 mt-2">
                        <button
                            class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-semibold shadow transition"
                            @click="deleteItem">
                            Excluir
                        </button>
                        <button
                            class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-5 py-2 rounded-lg font-semibold shadow transition"
                            @click="closeModal('delete')">
                            Cancelar
                        </button>
                    </div>
                </div>
            </template>
        </Modal>


    </AppLayout>
</template>