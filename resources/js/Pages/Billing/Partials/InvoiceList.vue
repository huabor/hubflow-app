<script setup lang="ts">
import { DocumentArrowDownIcon } from '@heroicons/vue/24/outline';
import dayjs from 'dayjs';

import { fc } from '@/Helpers/FormatCurrency';
import { Order } from '@/types';

defineProps<{
    orders?: Order[];
}>();
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                Invoices
            </h2>
        </header>

        <div class="overflow-x-auto">
            <table
                class="w-full text-left text-sm text-slate-500 dark:text-slate-400"
            >
                <thead
                    class="bg-slate-50 text-xs uppercase text-slate-900 dark:bg-slate-700 dark:text-slate-400"
                >
                    <tr>
                        <th scope="col" class="px-4 py-3">#</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3">Amount</th>
                        <th scope="col" class="px-4 py-3">Date</th>
                        <th scope="col" class="px-4 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="order in orders"
                        v-bind:key="order.id"
                        class="border-b dark:border-slate-700"
                    >
                        <td class="px-4 py-3">
                            {{ order.number }}
                        </td>
                        <td class="px-4 py-3">
                            {{ order.mollie_payment_status }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{ fc(order.total / 100) }}
                        </td>
                        <td class="px-4 py-3">
                            {{
                                dayjs(order.processed_at).format(
                                    'YYYY-MM-DD HH:mm',
                                )
                            }}
                        </td>
                        <td class="px-4 py-3">
                            <a
                                :href="
                                    route('billing.download-invoice', order.id)
                                "
                                class="inline-flex items-center rounded-lg p-0.5 text-center text-sm font-medium text-slate-500 hover:text-slate-800 focus:outline-none dark:text-slate-400 dark:hover:text-slate-100"
                            >
                                <DocumentArrowDownIcon class="size-5" />
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
