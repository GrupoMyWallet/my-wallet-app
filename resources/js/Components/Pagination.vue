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
      
      <span v-for="link in pagination.links" :key="link.label + String(link.url)">
        
        <span
          v-if="!link.url"
          class="px-3 py-1 border border-gray-300 rounded text-gray-400 dark:text-slate-700 cursor-default select-none"
          v-html="link.label"
        ></span>
  
       
        <Link
          v-else
          :href="link.url"
          class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 dark:hover:bg-slate-900 transition"
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
  