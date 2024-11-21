<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Credit, Order } from '@/types';
import InvoiceList from './Partials/InvoiceList.vue';
import UpdateBillingInformationForm from './Partials/UpdateBillingInformationForm.vue';

defineProps<{
    credit: Credit;
    orders: Order[];
}>();
</script>

<template>
    <Head title="Billing" />

    <AuthenticatedLayout>
        <template #header> Billing </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-xl sm:p-8 dark:bg-gray-800"
                >
                    <div class="flex items-center justify-between">
                        <h3
                            class="flex items-center gap-2 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200"
                        >
                            Manage your subscription
                        </h3>

                        <Link
                            class="ml-6 inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
                            :href="route('billing.choose-subscription')"
                        >
                            <template v-if="$page.props.auth.subscription">
                                Change plan
                            </template>
                            <template v-else> Subscribe </template>
                        </Link>
                    </div>
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-xl sm:p-8 dark:bg-gray-800"
                >
                    <UpdateBillingInformationForm
                        class="max-w-xl"
                        :credit="credit"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-xl sm:p-8 dark:bg-gray-800"
                >
                    <InvoiceList class="max-w-xl" :orders="orders" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
