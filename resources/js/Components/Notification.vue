<script lang="ts" setup>
import {
    CheckIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { usePage } from '@inertiajs/vue3';

const resetNotification = () => {
    const pageProps = usePage().props;
    if (pageProps.flash && pageProps.flash?.notification !== null) {
        pageProps.flash.notification = null;
    }
};
</script>

<template>
    <div class="fixed bottom-5 right-5 z-[1000]">
        <div
            v-if="$page?.props?.flash?.notification"
            id="toast"
            class="mb-4 flex w-full max-w-xs items-center rounded-xl bg-white p-4 text-gray-500 shadow dark:bg-gray-800 dark:text-gray-400"
        >
            <div
                v-if="$page?.props?.flash?.notification?.type === 'success'"
                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-500 dark:bg-green-800 dark:text-green-200"
            >
                <CheckIcon class="size-6" />
            </div>
            <div
                v-if="$page?.props?.flash?.notification?.type === 'error'"
                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-500 dark:bg-red-800 dark:text-red-200"
            >
                <XMarkIcon class="size-6" />
            </div>
            <div
                v-if="$page?.props?.flash?.notification?.type === 'warning'"
                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-500 dark:bg-orange-700 dark:text-orange-200"
            >
                <ExclamationTriangleIcon class="size-6" />
            </div>
            <div class="mx-3 text-sm font-normal">
                {{ $page?.props?.flash?.notification?.message }}
            </div>
            <button
                type="button"
                class="-mx-1.5 -my-1.5 ml-auto inline-flex h-8 w-8 items-center justify-center rounded-xl p-1.5 text-gray-400 hover:text-primary-400 focus:ring-2 focus:ring-gray-300 dark:text-gray-500"
                @click="resetNotification()"
            >
                <XMarkIcon class="size-6" />
            </button>
        </div>
    </div>
</template>
