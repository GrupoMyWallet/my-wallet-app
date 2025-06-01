<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import SelectInput from '@/Components/SelectInput.vue'
import Messages from '@/Components/Messages.vue'
import { useForm } from '@inertiajs/vue3'

// Props vindas do backend (categorias: todas as categorias para listar)
defineProps({
    categorias: Array,
    orcamentos: Array,
    // Adapte se vier userId ou info extra
})

const form = useForm({
    nome: '',
    tipo: 'despesa', // padrão
})

// Função para submit
function submit() {
    form.post('/categorias', {
        onSuccess: () => form.reset()
    })
}
</script>

<template>
    <AppLayout title="Lançamentos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Categorias
            </h2>
        </template>

        <div class="max-w-4xl mx-auto py-8 px-4 grid md:grid-cols-3 gap-10">

            <!-- Listagem de categorias -->
            <div class="md:col-span-2 space-y-4">
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                        <span class="text-blue-600">Minhas Categorias</span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ categorias.length }}</span>
                    </h3>
                    <ul class="divide-y divide-gray-200">
                        <li v-for="categoria in categorias" :key="categoria.id"
                            class="flex justify-between items-center py-2">
                            <div class="flex gap-2 items-center">
                                <span :class="[
                                    'w-2.5 h-2.5 rounded-full inline-block',
                                    categoria.tipo === 'despesa' ? 'bg-red-500' : 'bg-green-500'
                                ]"></span>
                                <span class="font-medium">{{ categoria.nome }}</span>
                                <span v-if="!categoria.user_id"
                                    class="text-xs bg-gray-200 text-gray-500 rounded px-2 py-0.5 ml-2">Geral</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <a v-if="categoria.tipo === 'despesa'" @click.prevent="cadastrarOrcamento" href="#"
                                    class="inline-block text-sm text-green-700 hover:underline hover:text-green-800 transition"
                                    :disabled="form.processing">
                                    + Cadastrar orçamento
                                </a>
                                <span class="uppercase text-xs text-gray-500">{{ categoria.tipo }}</span>


                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Formulário -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-bold mb-4 text-gray-700">Criar Nova Categoria</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="nome" value="Nome" :required="true" />
                        <TextInput  
                            id="nome" 
                            type="text" 
                            v-model="form.nome" 
                            class="mt-1 block w-full" 
                            step="0.01"
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
                        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                        Cadastrar Categoria
                    </button>
                </form>
            </div>
        </div>
        <div class="max-w-4xl mx-auto py-8 px-4">
            <h3 class="text-lg font-bold mb-3">Orçamentos</h3>
            <ul class="bg-white rounded-xl shadow divide-y divide-gray-200">
                <li v-for="categoria in categorias" :key="categoria.id" class="flex justify-between items-center p-4">
                    <div>
                        <span class="font-medium">{{ categoria.nome }}</span>
                    </div>
                    <template v-if="categoria.orcamento">

                        <span class="font-semibold">R$ {{ parseFloat(categoria.orcamento).toFixed(2) }}</span>
                    </template>
                    <template v-else>

                        <span class="font-semibold">Sem orçamento</span>
                    </template>
                </li>

            </ul>
        </div>
    </AppLayout>
</template>
