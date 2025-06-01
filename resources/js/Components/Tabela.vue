<script setup>
import { computed } from "vue";
import get from "lodash/get";

const props = defineProps({
    data: { type: Array, default: () => [] },
    fields: { type: Array, default: () => [] },
});

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

const itens = computed(() => props.data);
</script>

<template>
    <div class="overflow-x-auto rounded-xl shadow-lg bg-gradient-to-b from-blue-50 to-white border border-blue-100">
        <table class="min-w-full text-sm text-gray-800">
            <thead>
                <tr>
                    <th
                        v-for="field in fields"
                        :key="field.key"
                        class="px-6 py-3 bg-blue-600 text-white sticky top-0 z-10 text-left font-semibold tracking-wide first:rounded-tl-xl last:rounded-tr-xl shadow-sm"
                    >
                        {{ field.label }}
                    </th>
                    <th class="px-6 py-3 bg-blue-600 text-white sticky top-0 z-10 text-left font-semibold tracking-wide last:rounded-tr-xl shadow-sm">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(item, index) in itens"
                    :key="index"
                    class="border-b border-blue-100 even:bg-blue-50/40 hover:bg-blue-100/70 transition"
                >
                    <td
                        v-for="field in fields"
                        :key="field.key"
                        class="px-6 py-3 whitespace-nowrap"
                    >
                        <slot
                            :name="`cell:${field.key}`"
                            :item="item"
                            :value="formatValue(field.key, get(item, field.key))"
                        >
                            {{ formatValue(field.key, get(item, field.key)) }}
                        </slot>
                    </td>
                    <td class="px-6 py-3 flex gap-2 items-center">
                        <button
                            @click="$emit('edit', item)"
                            class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 text-white font-medium rounded-lg shadow hover:bg-blue-600 transition focus:outline-none focus:ring-2 focus:ring-blue-200"
                            title="Editar"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M9 13.5V21h7.5l6.364-6.364a2.5 2.5 0 0 0 0-3.536l-7.5-7.5a2.5 2.5 0 0 0-3.536 0L2.5 9.5a2.5 2.5 0 0 0 0 3.536l6.364 6.364z"/>
                            </svg>
                            <span class="hidden md:inline">Editar</span>
                        </button>
                        <button
                            @click="$emit('delete', item)"
                            class="inline-flex items-center gap-1 px-3 py-1 bg-red-500 text-white font-medium rounded-lg shadow hover:bg-red-600 transition focus:outline-none focus:ring-2 focus:ring-red-200"
                            title="Excluir"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="hidden md:inline">Excluir</span>
                        </button>
                    </td>
                </tr>
                <tr v-if="!itens.length">
                    <td :colspan="fields.length + 1" class="px-6 py-10 text-center text-blue-500 font-semibold bg-blue-50 rounded-b-xl">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
