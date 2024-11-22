<script setup lang="ts">
import Notification from '@/Components/Notification.vue';
</script>

<template>
    <div
        class="h-screen w-screen overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-950"
    >
        <slot />
    </div>

    <Notification />
</template>
