<script setup>

const props = defineProps({
    color: {
        type: String,
        default: 'text-black-900 dark:text-white',
    },
});

</script>

<template>
    <span :class="`text-2xl sm:text-3xl font-black tracking-tight select-none ${props.color} dark:text-white`">
        <span class="text-[#FF2D20]">My</span>Wallet
    </span>
</template>