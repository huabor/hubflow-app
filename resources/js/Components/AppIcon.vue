<script setup lang="ts">
import { GiftIcon, MapPinIcon } from '@heroicons/vue/24/outline';

import { AppType } from '@/types';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        type: AppType;
        size?: number;
    }>(),
    {
        size: 8,
    },
);

const sizeClass = computed(() => `size-${props.size}`);
</script>

<template>
    <GiftIcon v-if="type === 'birthday_reminder'" :class="sizeClass" />

    <MapPinIcon v-if="type === 'contact_cluster'" :class="sizeClass" />
</template>
