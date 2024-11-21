<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { onMounted, onUpdated, Ref, ref } from 'vue';

import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Hub, HubspotToken } from '@/types';
import { CheckIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps<{
    tokens: HubspotToken[];
}>();

const tokensRef: Ref<HubspotToken[]> = ref([]);
const openedWindow: Ref<Window | null> = ref(null);

onUpdated(() => {
    console.log(props);
    tokensRef.value = props.tokens;
});

onMounted(() => {
    tokensRef.value = props.tokens;
    if (window.opener !== null) {
        window.opener.postMessage(
            {
                closeModal: true,
            },

            window.location.origin,
        );
    }

    if (window.opener === null) {
        window.addEventListener(
            'message',
            (event) => {
                if (event.origin !== window.location.origin) return;

                const data = event.data;

                if (data.closeModal === true) {
                    router.visit(route('app.index'));
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
        route('oauth.hubspot.redirect', {
            state: 'popup',
        }),
        'Hubflow Apps - Connect',
        'resizeable,scrollbars,height=800,width=768',
    );
};

const selectHub = (hub: Hub) => {
    useForm({}).post(route('hubspot.select-hub', { hub }), {
        preserveScroll: true,
        onSuccess: () => {},
        onError: () => {},
        onFinish: () => {},
    });
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
                    class="bg-white p-4 shadow sm:rounded-xl sm:p-8 dark:bg-gray-800"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <h3
                            class="flex items-center gap-2 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200"
                        >
                            Select Hub or Connect to a new one
                        </h3>
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
                                            {{ token.hub?.domain }}
                                        </div>
                                        <div class="font-normal text-gray-500">
                                            {{ token.hub?.hub_id }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <SecondaryButton
                                                :disabled="
                                                    token.hub.id ===
                                                    $page.props.auth.user.hub_id
                                                "
                                                :class="{
                                                    '!border-green-400 !text-green-400 !opacity-100':
                                                        token.hub.id ===
                                                        $page.props.auth.user
                                                            .hub_id,
                                                }"
                                                @click="
                                                    () => selectHub(token.hub)
                                                "
                                            >
                                                <template
                                                    v-if="
                                                        token.hub.id ===
                                                        $page.props.auth.user
                                                            .hub_id
                                                    "
                                                >
                                                    Selected
                                                </template>
                                                <template v-else>
                                                    Select
                                                </template>
                                                <CheckIcon class="size-5" />
                                            </SecondaryButton>

                                            <button
                                                @click="
                                                    () => deleteToken(token)
                                                "
                                                class="inline-flex items-center rounded-xl p-0.5 text-center text-sm font-medium text-gray-500 hover:text-gray-800 focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                                                type="button"
                                            >
                                                <TrashIcon class="size-5" />
                                            </button>
                                        </div>
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
