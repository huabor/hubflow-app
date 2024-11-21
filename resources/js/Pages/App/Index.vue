<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';

import { App, AvailableApp } from '@/types';

import AppIcon from '@/Components/AppIcon.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps<{
    apps: App[];
    available_apps: AvailableApp[];
}>();

const planDetails = usePage().props.auth.plan_details;
</script>

<template>
    <Head title="Apps" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="grid grid-cols-4 gap-8">
                    <template v-for="app in available_apps" :key="app.type">
                        <Link
                            v-if="planDetails.enabled_apps.includes(app.type)"
                            :href="route('app.show', { type: app.type })"
                            class="flex flex-col items-center justify-center gap-2 rounded-xl border border-primary-400 bg-white p-8 text-primary-400 shadow-xl transition-all hover:bg-primary-400 hover:text-white dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-primary-600"
                        >
                            <AppIcon :type="app.type" :size="12" />

                            <span class="text-xl">{{ app.name }}</span>
                        </Link>

                        <div
                            v-else
                            class="flex flex-col items-center justify-center gap-2 rounded-xl border border-primary-400 bg-white p-8 text-primary-400 opacity-70 shadow-xl dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400"
                        >
                            <AppIcon :type="app.type" :size="12" />

                            <span>{{ app.name }}</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
