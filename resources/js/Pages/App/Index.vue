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
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <div class="grid grid-cols-4 gap-4">
                        <template v-for="app in available_apps" :key="app.type">
                            <Link
                                v-if="
                                    planDetails.enabled_apps.includes(app.type)
                                "
                                :href="route('app.show', { type: app.type })"
                                class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-800 bg-gray-100 p-8 text-gray-800 shadow-lg hover:border-primary-400 hover:text-primary-400 dark:border-gray-400 dark:bg-gray-600 dark:text-gray-400"
                            >
                                <AppIcon :type="app.type" />

                                <span>{{ app.name }}</span>
                            </Link>
                            <div
                                v-else
                                class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-800 bg-gray-100 p-8 text-gray-800 opacity-70 shadow-lg dark:border-gray-400 dark:bg-gray-600 dark:text-gray-400"
                            >
                                <AppIcon :type="app.type" />

                                <span>{{ app.name }}</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
