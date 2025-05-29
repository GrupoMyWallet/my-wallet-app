<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Array,
    required: true,
    default: () => [],
  }
})

const emit = defineEmits(['edit', 'delete'])

// Gera os headers filtrando os campos ignorados
const headers = computed(() => {
  if (props.data.length === 0) return []
  return Object.keys(props.data[0]).filter(
    (key) => !key.toLowerCase().match(/(id|at)$/)
  )
})

const handleEdit = (item) => emit('edit', item)
const handleDelete = (item) => emit('delete', item)
</script>

<template>
  <div class="overflow-x-auto rounded-2xl shadow-md">
    <table class="min-w-full text-sm text-left text-gray-700 bg-white">
      <thead class="text-xs uppercase bg-gray-100 text-gray-600">
        <tr>
          <th v-for="header in headers" :key="header" class="px-6 py-4 capitalize">
            {{ header }}
          </th>
          <th class="px-6 py-4">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, rowIndex) in data" :key="rowIndex" class="border-b hover:bg-gray-50">
          <td v-for="header in headers" :key="header" class="px-6 py-4">
            {{ row[header] }}
          </td>
          <td class="px-6 py-4 flex space-x-2">
            <button
              class="px-3 py-1 text-white bg-blue-500 rounded hover:bg-blue-600 transition"
              @click="handleEdit(row)"
            >
              Editar
            </button>
            <button
              class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-600 transition"
              @click="handleDelete(row)"
            >
              Excluir
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
