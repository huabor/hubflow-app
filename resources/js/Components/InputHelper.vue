<script setup lang="ts">
defineProps<{
    value?: string;
}>();
</script>

<template>
    <span class="block text-xs text-gray-500 dark:text-gray-400">
        <slot />
    </span>
</template>
