<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import HubflowAppsLogo from '@/Components/HubflowAppsLogo.vue';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-950"
    >
        <div>
            <Link href="/">
                <HubflowAppsLogo
                    class="h-20 w-20 fill-current text-gray-500"
                />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-xl dark:bg-gray-800"
        >
            <slot />
        </div>
    </div>
</template>
