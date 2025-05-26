<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

const props = defineProps({
    lancamentos: Array,
})
</script>

<template>
    <AppLayout title="Lançamentos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>
        {{ lancamentos }}
        
    </AppLayout>
</template>