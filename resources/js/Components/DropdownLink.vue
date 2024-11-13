<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    href: string;
}>();
</script>

<template>
    <Link
        :href="href"
        class="flex w-full items-center space-x-2 px-4 py-2 text-start text-sm leading-5 text-slate-700 transition duration-150 ease-in-out hover:text-primary-400 focus:text-primary-400 focus:outline-none dark:text-slate-300"
    >
        <slot />
    </Link>
</template>
