<script setup lang="ts">
import { onMounted, ref } from 'vue';

withDefaults(
    defineProps<{
        options: any[];
        id: string;
        error?: string;
        required?: boolean;
    }>(),
    {
        required: false,
        size: 'default',
    },
);

defineModel<string | number | null>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <select
        :id="id"
        :value="modelValue"
        :required="required"
        @change="
            $emit(
                'update:modelValue',
                ($event.target as HTMLInputElement).value,
            )
        "
        ref="input"
        placeholder=" "
        class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-400 focus:ring-primary-500 disabled:opacity-70 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-400 dark:focus:ring-primary-400"
    >
        <option value="" disabled>Choose</option>
        <option v-for="option in options" :value="option.key" :key="option.key">
            {{ option.value }}
        </option>
    </select>
</template>
