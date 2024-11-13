<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    apps: App[];
}>();

const deleteApp = (app: App) => {
    if (confirm('Are you sure you want to delete this token?')) {
        useForm({}).delete(route('app.delete', { app: app.id }), {
            preserveScroll: true,
            onSuccess: () => {},
            onError: () => {},
            onFinish: () => {},
        });
    }
};
</script>

<template>
    <Head title="Apps" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    {{ apps }}
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
