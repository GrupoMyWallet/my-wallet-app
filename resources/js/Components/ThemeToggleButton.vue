<script setup>
import { useDark, useToggle } from '@vueuse/core'

const isDark = useDark()
const toggleDark = useToggle(isDark)

defineProps({
    type:{
        type: String,
        default: "button"
    }
})
</script>

<template>
    <button id="theme-toggle" @click="toggleDark()" :type="type" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
        <i inline-block align-middle i="dark:carbon-moon carbon-sun" />
        <span class="ml-2">{{ isDark ? 'Dark' : 'Light' }}</span>
    </button>
</template>
