<script setup>
import { usePage } from '@inertiajs/vue3';

</script>

<template>
    <div v-if="$page.props.flash.error" class="bg-red-100 text-black-800 px-4 py-2 rounded mb-4">
        {{ $page.props.flash.error }}
    </div>
    <div v-if="$page.props.flash.success" class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash.message" class="alert">
        {{ $page.props.flash.message }}
    </div>
</template>