<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { HubspotToken } from '@/types';
import { TrashIcon } from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    tokens: HubspotToken[];
}>();

const deleteToken = (token: HubspotToken) => {
    if (confirm('Are you sure you want to delete this token?')) {
        useForm({}).delete(route('hubspot.token.delete', { token: token.id }), {
            preserveScroll: true,
            onSuccess: () => {},
            onError: () => {},
            onFinish: () => {},
        });
    }
};
</script>

<template>
    <Head title="Hubspot Tokens" />

    <AuthenticatedLayout>
        <template #header> Hubspot Tokens </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-slate-800"
                >
                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left text-sm text-slate-500 dark:text-slate-400"
                        >
                            <thead
                                class="bg-slate-50 text-xs uppercase text-slate-900 dark:bg-slate-700 dark:text-slate-400"
                            >
                                <tr>
                                    <th scope="col" class="px-4 py-3">#</th>
                                    <th scope="col" class="px-4 py-3">User</th>
                                    <th scope="col" class="px-4 py-3">
                                        Account
                                    </th>
                                    <th scope="col" class="px-4 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="token in tokens"
                                    v-bind:key="token.id"
                                    class="border-b dark:border-slate-700"
                                >
                                    <td class="px-4 py-3">
                                        {{ token.id }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-base">
                                            {{ token.email }}
                                        </div>
                                        <div class="font-normal text-slate-500">
                                            {{ token.hubspot_user_id }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-base">
                                            {{ token.hub_domain }}
                                        </div>
                                        <div class="font-normal text-slate-500">
                                            {{ token.hub_id }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            @click="() => deleteToken(token)"
                                            class="inline-flex items-center rounded-lg p-0.5 text-center text-sm font-medium text-slate-500 hover:text-slate-800 focus:outline-none dark:text-slate-400 dark:hover:text-slate-100"
                                            type="button"
                                        >
                                            <TrashIcon class="size-5" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
