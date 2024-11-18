<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, Ref, ref } from 'vue';

import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { HubspotToken } from '@/types';
import { TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps<{
    tokens: HubspotToken[];
}>();

const tokensRef: Ref<HubspotToken[]> = ref([]);
const openedWindow: Ref<Window | null> = ref(null);

onMounted(() => {
    tokensRef.value = props.tokens;
    if (window.opener !== null) {
        window.opener.postMessage(
            JSON.stringify({
                hubspotTokens: props.tokens,
            }),
            window.location.origin,
        );
    }

    if (window.opener === null) {
        window.addEventListener(
            'message',
            (event) => {
                // Do we trust the sender of this message? (might be
                // different from what we originally opened, for example).
                if (event.origin !== window.location.origin) return;

                const data = JSON.parse(event.data);
                if (data.hubspotTokens !== undefined) {
                    tokensRef.value = data.hubspotTokens;
                }

                if (openedWindow.value !== null) {
                    openedWindow.value.close();
                }
            },
            false,
        );
    }
});

const openConnectModal = () => {
    openedWindow.value = window.open(
        route('oauth.hubspot.redirect'),
        'Hubflow Apps - Connect',
        'resizeable,scrollbars,height=800,width=768',
    );
};

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
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <div class="mb-4 flex justify-end">
                        <PrimaryButton @click="openConnectModal">
                            Connect
                        </PrimaryButton>
                    </div>
                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left text-sm text-gray-500 dark:text-gray-400"
                        >
                            <thead
                                class="bg-gray-50 text-xs uppercase text-gray-900 dark:bg-gray-700 dark:text-gray-400"
                            >
                                <tr>
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
                                    v-for="token in tokensRef"
                                    v-bind:key="token.id"
                                    class="border-b dark:border-gray-700"
                                >
                                    <td class="px-4 py-3">
                                        <div class="text-base">
                                            {{ token.email }}
                                        </div>
                                        <div class="font-normal text-gray-500">
                                            {{ token.hubspot_user_id }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-base">
                                            {{ token.hub_domain }}
                                        </div>
                                        <div class="font-normal text-gray-500">
                                            {{ token.hub_id }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            @click="() => deleteToken(token)"
                                            class="inline-flex items-center rounded-lg p-0.5 text-center text-sm font-medium text-gray-500 hover:text-gray-800 focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
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
