<script setup>
import { onMounted, ref } from 'vue';




defineProps({
    modelValue: String,
    type: { // Define a nova propriedade 'type'
        type: String,
        default: 'text', // O valor padrão é 'text'
    },
});







defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :type="type"
    >
</template>