<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <textarea
        rows="7"
        class="rounded-xl border-gray-300 shadow-sm focus:border-primary-400 focus:ring-primary-400 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-300"
        v-model="model"
        ref="input"
    />
</template>
