<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({

  pagination: {
    type: Object,
    required: true,
  },
});
</script>

<template>
    <!-- Só exibe a paginação se houver mais de uma página -->
    <div v-if="pagination.last_page > 1" class="flex justify-center items-center space-x-1 mt-4">
      <!-- Itera em cada item de links gerado pelo Laravel -->
      <span v-for="link in pagination.links" :key="link.label + String(link.url)">
        <!-- Se não houver URL (por exemplo o "..." ou página ativa sem link), exibe um <span> desabilitado -->
        <span
          v-if="!link.url"
          class="px-3 py-1 border border-gray-300 rounded text-gray-400 cursor-default select-none"
          v-html="link.label"
        ></span>
  
        <!-- Caso haja URL, usa o <Link> do Inertia para navegar -->
        <Link
          v-else
          :href="link.url"
          class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 transition"
          :class="link.active
            ? 'bg-blue-500 text-white border-blue-500'
            : 'bg-white text-gray-700'"
          v-html="link.label"
        />
      </span>
    </div>
  </template>
  

  
  <style scoped>
  /* Opcional: você pode ajustar margens, cores ou fontes aqui se quiser */
  </style>
  