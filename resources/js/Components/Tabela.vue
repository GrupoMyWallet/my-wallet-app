<script setup>
import { computed } from 'vue';
import get from 'lodash/get'; // ou crie sua própria função getNested

// Props: agora recebe data *e* fields
const props = defineProps({
  data:   { type: Array, default: () => [] },
  fields: { type: Array, default: () => [] },
});

// Função genérica de formatação
function formatValue(key, value) {
  if (value == null) return '-';

  const lower = key.toLowerCase();

  if (lower.includes('data') || lower.includes('date')) {
    return new Date(value).toLocaleDateString('pt-BR');
  }
  if (lower.includes('valor') && typeof value === 'number') {
    return new Intl.NumberFormat('pt-BR',
      { style: 'currency', currency: 'BRL' }
    ).format(value);
  }

  if (typeof value === 'string') {
    const str = value.trim();
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  if (typeof value === 'boolean') {
    return value ? '✔️' : '✖️';
  }
  return value;
}

const rows = computed(() => props.data);
</script>

<template>
  <div class="overflow-x-auto rounded-lg shadow-lg">
    <table class="min-w-full bg-white">
      <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
        <tr>
          <th
            v-for="field in fields"
            :key="field.key"
            class="px-4 py-2 text-left"
          >
            {{ field.label }}
          </th>
          <th class="px-4 py-2 text-left">Ações</th>
        </tr>
      </thead>
      <tbody class="text-gray-700 text-sm">
        <tr
          v-for="(row, idx) in rows"
          :key="idx"
          class="odd:bg-white even:bg-gray-50 hover:bg-gray-100"
        >
          <td
            v-for="field in fields"
            :key="field.key"
            class="px-4 py-2"
          >
            <!-- slot para custom cell, se não houver usa formatValue -->
            <slot
              :name="`cell:${field.key}`"
              :row="row"
              :value="formatValue(field.key, get(row, field.key))"
            >
              {{ formatValue(field.key, get(row, field.key)) }}
            </slot>
          </td>
          <td class="px-4 py-2 space-x-2">
            <!-- slot para custom actions -->
            <slot name="actions" :row="row">
              <button
                @click="$emit('edit', row)"
                class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
              >
                ✏️
              </button>
              <button
                @click="$emit('delete', row)"
                class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
              >
                🗑️
              </button>
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
