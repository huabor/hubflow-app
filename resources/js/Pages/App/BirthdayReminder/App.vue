<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import { App } from '@/types';

import AppIcon from '@/Components/AppIcon.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps<{
    app: App;
}>();
</script>

<template>
    <Head title="Apps" />

    <AuthenticatedLayout>
        <template #header>
            <AppIcon :type="app.type" />
            {{ app.name }}
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-xl sm:p-8 dark:bg-gray-800"
                ></div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
