<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import SelectInput from '@/Components/SelectInput.vue'
import Messages from '@/Components/Messages.vue'
import Modal from '@/Components/Modal.vue'
import { useForm, router } from '@inertiajs/vue3'

// Props vindas do backend
const props = defineProps({
    categorias: Array,
    orcamentos: Array,
})

const form = useForm({
    nome: '',
    tipo: 'despesa',
})

const editForm = useForm({
    id: null,
    nome: '',
    tipo: 'despesa',
})

const deleteForm = useForm({})

// Estados dos modais
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const categoriaToDelete = ref(null)

// Ordenação
const ordenacao = ref('nome') // 'nome', 'tipo'

// Categorias organizadas
const categoriasOrganizadas = computed(() => {
    const todasCategorias = props.categorias || []

    // Separar categorias
    let categoriasUsuario = todasCategorias.filter(cat => cat.user_id)
    let categoriasGerais = todasCategorias.filter(cat => !cat.user_id)

    // Função de ordenação
    const ordenarCategorias = (cats) => {
        return cats.sort((a, b) => {
            if (ordenacao.value === 'tipo') {
                if (a.tipo !== b.tipo) {
                    return a.tipo.localeCompare(b.tipo)
                }
                return a.nome.localeCompare(b.nome)
            }
            return a.nome.localeCompare(b.nome)
        })
    }

    return {
        usuario: ordenarCategorias([...categoriasUsuario]),
        gerais: ordenarCategorias([...categoriasGerais]),
        total: todasCategorias.length
    }
})

// Função para submit de nova categoria
function submit() {
    form.post('/categorias', {
        onSuccess: () => form.reset()
    })
}

// Função para abrir modal de edição
function openEditModal(categoria) {
    editForm.id = categoria.id
    editForm.nome = categoria.nome
    editForm.tipo = categoria.tipo
    showEditModal.value = true
}

// Função para salvar edição
function salvarEdicao() {
    editForm.put(`/categorias/${editForm.id}`, {
        onSuccess: () => {
            showEditModal.value = false
            editForm.reset()
        }
    })
}

// Função para abrir modal de exclusão
function openDeleteModal(categoria) {
    categoriaToDelete.value = categoria
    showDeleteModal.value = true
}

// Função para excluir categoria
function excluirCategoria() {
    deleteForm.delete(`/categorias/${categoriaToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false
            categoriaToDelete.value = null
        }
    })
}

// Função para fechar modais
function fecharModais() {
    showEditModal.value = false
    showDeleteModal.value = false
    categoriaToDelete.value = null
    editForm.reset()
}
</script>

<template>
    <AppLayout title="Categorias">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
                Categorias
            </h2>
        </template>

        <Messages />

        <div class="max-w-4xl mx-auto py-8 px-4 grid md:grid-cols-3 gap-10">

            <!-- Listagem de categorias -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-4 md:col-span-2">

                <!-- Header com ordenação -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-slate-200">Suas Categorias</h3>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-medium px-3 py-1 rounded-full">
                            {{ categoriasOrganizadas.total }} total
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-slate-400">Ordenar por:</span>
                        <select v-model="ordenacao"
                            class="text-sm border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="nome">Nome</option>
                            <option value="tipo">Tipo</option>
                        </select>
                    </div>
                </div>

                <!-- Minhas Categorias -->
                <div v-if="categoriasOrganizadas.usuario.length > 0"
                    class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-100 dark:border-blue-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-200">Minhas Categorias</h4>
                        <span class="bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-200 text-xs font-medium px-2 py-1 rounded-full">
                            {{ categoriasOrganizadas.usuario.length }}
                        </span>
                    </div>

                    <div class="grid gap-3">
                        <div v-for="categoria in categoriasOrganizadas.usuario" :key="categoria.id"
                            class="bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-slate-600 hover:shadow-md transition-all duration-200">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <span :class="[
                                            'w-3 h-3 rounded-full',
                                            categoria.tipo === 'despesa' ? 'bg-red-500' : 'bg-green-500'
                                        ]"></span>
                                        <span class="font-medium text-gray-900 dark:text-slate-200">{{ categoria.nome }}</span>
                                    </div>
                                    <span :class="[
                                        'text-xs font-medium px-2 py-1 rounded-full uppercase tracking-wide',
                                        categoria.tipo === 'despesa'
                                            ? 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300'
                                            : 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                                    ]">
                                        {{ categoria.tipo }}
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="openEditModal(categoria)" 
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 p-1 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button @click="openDeleteModal(categoria)" 
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-1 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categorias Gerais -->
                <div v-if="categoriasOrganizadas.gerais.length > 0"
                    class="bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-slate-300">Categorias Existentes</h4>
                        <span class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium px-2 py-1 rounded-full">
                            {{ categoriasOrganizadas.gerais.length }}
                        </span>
                    </div>

                    <div class="grid gap-3">
                        <div v-for="categoria in categoriasOrganizadas.gerais" :key="categoria.id"
                            class="bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-slate-600">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <span :class="[
                                            'w-3 h-3 rounded-full',
                                            categoria.tipo === 'despesa' ? 'bg-red-500' : 'bg-green-500'
                                        ]"></span>
                                        <span class="font-medium text-gray-700 dark:text-slate-300">{{ categoria.nome }}</span>
                                    </div>
                                    <span :class="[
                                        'text-xs font-medium px-2 py-1 rounded-full uppercase tracking-wide',
                                        categoria.tipo === 'despesa'
                                            ? 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300'
                                            : 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                                    ]">
                                        {{ categoria.tipo }}
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensagem quando não há categorias -->
                <div v-if="categoriasOrganizadas.total === 0"
                    class="bg-white dark:bg-slate-700 rounded-2xl p-12 text-center border-2 border-dashed border-gray-200 dark:border-slate-600">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-slate-200 mb-2">Nenhuma categoria encontrada</h3>
                    <p class="text-gray-500 dark:text-slate-400">Comece criando sua primeira categoria</p>
                </div>
            </div>

            <!-- Formulário -->
            <div >
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
                    <h3 class="font-bold mb-4 text-gray-700 dark:text-slate-300">Criar Nova Categoria</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="nome" value="Nome" :required="true" />
                            <TextInput id="nome" type="text" v-model="form.nome" class="mt-1 block w-full" step="0.01"
                                required />
                            <InputError class="mt-2" :message="form.errors.nome" />
                        </div>
                        <div>

                            <InputLabel for="tipo" value="Tipo" :required="true" />
                            <SelectInput v-model="form.tipo">
                                <option value="despesa">Despesa</option>
                                <option value="receita">Receita</option>
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors.tipo" />
                        </div>
                        <button type="submit" :disabled="form.processing"
                            class="w-full bg-green-600 dark:bg-green-700 text-white py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition">
                            Cadastrar Categoria
                        </button>
                    </form>
                </div>
            </div>

        </div>


        <!-- Modal de Edição -->
        <Modal :show="showEditModal" @close="fecharModais">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4 dark:text-slate-200">Editar Categoria</h3>
                <form @submit.prevent="salvarEdicao" class="space-y-4">
                    <div>
                        <InputLabel for="edit_nome" value="Nome" :required="true" />
                        <TextInput id="edit_nome" type="text" v-model="editForm.nome" class="mt-1 block w-full"
                            required />
                        <InputError class="mt-2" :message="editForm.errors.nome" />
                    </div>
                    <div>
                        <InputLabel for="edit_tipo" value="Tipo" :required="true" />
                        <SelectInput v-model="editForm.tipo">
                            <option value="despesa">Despesa</option>
                            <option value="receita">Receita</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="editForm.errors.tipo" />
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="fecharModais"
                            class="px-4 py-2 bg-gray-300 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-400 dark:hover:bg-slate-500 transition">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="editForm.processing"
                            class="px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de Confirmação de Exclusão -->
        <Modal :show="showDeleteModal" @close="fecharModais">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4 text-red-600 dark:text-red-400">Confirmar Exclusão</h3>
                <p class="mb-6 dark:text-slate-300">
                    Tem certeza que deseja excluir a categoria
                    <strong class="dark:text-slate-100">"{{ categoriaToDelete?.nome }}"</strong>?
                    Esta ação não pode ser desfeita.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="fecharModais"
                        class="px-4 py-2 bg-gray-300 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-400 dark:hover:bg-slate-500 transition">
                        Cancelar
                    </button>
                    <button @click="excluirCategoria" :disabled="deleteForm.processing"
                        class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded-lg hover:bg-red-700 dark:hover:bg-red-800 transition">
                        Excluir Categoria
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>