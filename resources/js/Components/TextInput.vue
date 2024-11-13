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
    <input
        class="rounded-md border-slate-300 shadow-sm focus:border-primary-400 focus:ring-primary-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300"
        v-model="model"
        ref="input"
    />
</template>
