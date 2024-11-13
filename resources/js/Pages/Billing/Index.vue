<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

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
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Billing
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdateBillingInformationForm
                        class="max-w-xl"
                        :credit="credit"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <InvoiceList class="max-w-xl" :orders="orders" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
