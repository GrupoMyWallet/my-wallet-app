<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Tabela from '@/Components/Tabela.vue'

const props = defineProps({
    lancamentos: Array,
})

const editarItem = (item) => console.log('Editar:', item)
const excluirItem = (item) => console.log('Excluir:', item)
</script>

<template>
    <AppLayout title="Lançamentos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lançamentos
            </h2>
        </template>

        <div class="p-6">
            

            <Tabela
            :data="lancamentos"
            @edit="editarItem"
            @delete="excluirItem"
            />
        </div>
    </AppLayout>
</template>