<script setup>
import { computed, ref } from "vue";
import get from "lodash/get";

const props = defineProps({
    data: { type: Array, default: () => [] },
    fields: { type: Array, default: () => [] },
});

const sortField = ref('');
const sortDirection = ref('asc');

function formatValue(key, value) {
    if (value == null) return "-";
    const lower = key.toLowerCase();
    if (lower.includes("valor")) {
        return new Intl.NumberFormat("pt-BR", {
            style: "currency",
            currency: "BRL",
        }).format(value);
    }
    if (typeof value === "string") {
        const str = value.trim();
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    if (typeof value === "boolean") {
        return value ? "✔️" : "✖️";
    }
    return value;
}

function sortBy(field) {
    if (sortField.value === field.key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field.key;
        sortDirection.value = 'asc';
    }
}

const sortedItems = computed(() => {
    if (!sortField.value) return props.data;
    
    return [...props.data].sort((a, b) => {
        const aValue = get(a, sortField.value);
        const bValue = get(b, sortField.value);
        
        let comparison = 0;
        
        if (aValue === null || aValue === undefined) return 1;
        if (bValue === null || bValue === undefined) return -1;
        
        if (typeof aValue === 'string' && typeof bValue === 'string') {
            comparison = aValue.localeCompare(bValue);
        } else if (typeof aValue === 'number' && typeof bValue === 'number') {
            comparison = aValue - bValue;
        } else {
            comparison = String(aValue).localeCompare(String(bValue));
        }
        
        return sortDirection.value === 'asc' ? comparison : -comparison;
    });
});
</script>

<template>
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                    <tr>
                        <th
                            v-for="field in fields"
                            :key="field.key"
                            class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-slate-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors select-none"
                            @click="sortBy(field)"
                        >
                            <div class="flex items-center gap-2">
                                {{ field.label }}
                                <div class="flex flex-col">
                                    <svg 
                                        class="w-3 h-3 text-gray-400 dark:text-slate-500"
                                        :class="{ 'text-blue-600 dark:text-blue-400': sortField === field.key && sortDirection === 'asc' }"
                                        fill="currentColor" 
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                    </svg>
                                    <svg 
                                        class="w-3 h-3 text-gray-400 dark:text-slate-500 -mt-1"
                                        :class="{ 'text-blue-600 dark:text-blue-400': sortField === field.key && sortDirection === 'desc' }"
                                        fill="currentColor" 
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-slate-300 w-32">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    <tr
                        v-for="(item, index) in sortedItems"
                        :key="index"
                        class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors"
                    >
                        <td
                            v-for="field in fields"
                            :key="field.key"
                            class="px-4 py-3 text-sm text-gray-900 dark:text-slate-300"
                        >
                            <slot
                                :name="`cell:${field.key}`"
                                :item="item"
                                :value="formatValue(field.key, get(item, field.key))"
                            >
                                {{ formatValue(field.key, get(item, field.key)) }}
                            </slot>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button
                                    @click="$emit('edit', item)"
                                    class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors"
                                    title="Editar"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button
                                    @click="$emit('delete', item)"
                                    class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors"
                                    title="Excluir"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!sortedItems.length">
                        <td :colspan="fields.length + 1" class="px-4 py-8 text-center text-gray-500 dark:text-slate-400">
                            Nenhum registro encontrado.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>