<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

import {
    ArrowLeftStartOnRectangleIcon,
    BanknotesIcon,
    CheckIcon,
    ChevronDownIcon,
    ExclamationTriangleIcon,
    KeyIcon,
    MoonIcon,
    Squares2X2Icon,
    SunIcon,
    UserIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import HubflowAppsLogo from '@/Components/HubflowAppsLogo.vue';
import { useDark, useToggle } from '@vueuse/core';
import dayjs from 'dayjs';
import { computed } from 'vue';

const resetNotification = () => {
    const pageProps = usePage().props;
    if (pageProps.flash && pageProps.flash?.notification !== null) {
        pageProps.flash.notification = null;
    }
};

const user = computed(() => usePage().props.auth.user);

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-950">
            <nav
                class="h-16 border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex items-center gap-8">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('app.index')">
                                    <HubflowAppsLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                </Link>
                            </div>

                            <div v-if="$slots.header">
                                <h2
                                    class="flex items-center gap-2 rounded-xl bg-gray-200 p-2 text-lg leading-tight text-gray-800 dark:bg-gray-600 dark:text-white"
                                >
                                    <slot name="header" />
                                </h2>
                            </div>
                        </div>

                        <div class="ms-6 flex items-center">
                            <div
                                class="relative ms-3"
                                v-if="$page.props.auth.subscription"
                            >
                                <div
                                    class="inline-flex items-center px-3 py-2 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    <span
                                        v-if="$page.props.auth.on_grace_period"
                                        class="ml-2 flex items-center gap-1 text-base font-medium leading-4 text-red-600"
                                    >
                                        <ExclamationTriangleIcon
                                            class="size-6"
                                        />
                                        Plan ends on
                                        {{
                                            dayjs(
                                                $page.props.auth.subscription
                                                    .ends_at,
                                            ).format('YYYY-MM-DD HH:mm')
                                        }}
                                    </span>

                                    <span v-else>
                                        Your Plan:
                                        <span
                                            class="ml-1 font-medium leading-4 text-primary-400"
                                        >
                                            {{
                                                $page.props.system.plans[
                                                    $page.props.auth
                                                        .subscription.plan
                                                ].name
                                            }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="relative ms-3">
                                <Link
                                    :href="route('app.index')"
                                    class="flex items-center rounded-full pe-1 text-sm font-medium text-gray-800 hover:text-primary-600 md:me-0 dark:text-white dark:hover:text-primary-500"
                                >
                                    <Squares2X2Icon class="me-2 size-8" />
                                    Apps
                                </Link>
                            </div>

                            <div class="relative ms-6">
                                <Dropdown align="right" width="56">
                                    <template #trigger>
                                        <button
                                            class="flex items-center rounded-full pe-1 text-sm font-medium text-gray-800 hover:text-primary-600 md:me-0 dark:text-white dark:hover:text-primary-500"
                                            type="button"
                                        >
                                            <img
                                                class="me-2 h-8 w-8 rounded-full"
                                                :src="user?.avatar"
                                                :alt="user?.firstname"
                                            />

                                            {{ user?.firstname }}

                                            <ChevronDownIcon
                                                class="ms-3 h-4 w-4"
                                            />
                                        </button>
                                    </template>

                                    <template #content>
                                        <div
                                            class="px-4 py-3 text-sm text-gray-900 dark:text-white"
                                        >
                                            <div class="font-medium">
                                                {{ user.firstname }}
                                                {{ user.lastname }}
                                            </div>
                                            <div class="truncate">
                                                {{ user.email }}
                                            </div>
                                        </div>

                                        <div
                                            class="px-4 py-3 text-sm text-gray-900 dark:text-white"
                                            v-if="user.selected_hub"
                                        >
                                            <div class="truncate font-medium">
                                                {{ user.selected_hub.domain }}
                                            </div>
                                            <div class="truncate">
                                                {{ user.selected_hub.hub_id }}
                                            </div>
                                        </div>

                                        <div
                                            class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                        >
                                            <button
                                                type="button"
                                                class="flex w-full items-center space-x-2 px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:text-primary-400 focus:text-primary-400 focus:outline-none dark:text-gray-300"
                                                @click="toggleDark()"
                                            >
                                                <template v-if="isDark">
                                                    <SunIcon class="size-5" />
                                                    <span>Light</span>
                                                </template>
                                                <template v-else>
                                                    <MoonIcon class="size-5" />
                                                    <span>Dark</span>
                                                </template>
                                            </button>

                                            <DropdownLink
                                                :href="
                                                    route('hubspot.token.index')
                                                "
                                            >
                                                <KeyIcon class="size-5" />
                                                <span>Token</span>
                                            </DropdownLink>

                                            <DropdownLink
                                                :href="route('profile.edit')"
                                            >
                                                <UserIcon class="size-5" />
                                                <span>Profile</span>
                                            </DropdownLink>

                                            <DropdownLink
                                                :href="route('billing.index')"
                                            >
                                                <BanknotesIcon class="size-5" />
                                                <span>Billing</span>
                                            </DropdownLink>
                                        </div>

                                        <div class="py-2">
                                            <DropdownLink
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                            >
                                                <ArrowLeftStartOnRectangleIcon
                                                    class="size-5"
                                                />
                                                <span>Logout</span>
                                            </DropdownLink>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <main>
                <slot />
            </main>
        </div>
    </div>

    <div class="fixed bottom-5 right-5">
        <div
            v-if="$page?.props?.flash?.notification"
            id="toast"
            class="mb-4 flex w-full max-w-xs items-center rounded-lg bg-white p-4 text-gray-500 shadow dark:bg-gray-800 dark:text-gray-400"
        >
            <div
                v-if="$page?.props?.flash?.notification?.type === 'success'"
                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-500 dark:bg-green-800 dark:text-green-200"
            >
                <CheckIcon class="size-6" />
            </div>
            <div
                v-if="$page?.props?.flash?.notification?.type === 'error'"
                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-500 dark:bg-red-800 dark:text-red-200"
            >
                <XMarkIcon class="size-6" />
            </div>
            <div
                v-if="$page?.props?.flash?.notification?.type === 'warning'"
                class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-500 dark:bg-orange-700 dark:text-orange-200"
            >
                <ExclamationTriangleIcon class="size-6" />
            </div>
            <div class="mx-3 text-sm font-normal">
                {{ $page?.props?.flash?.notification?.message }}
            </div>
            <button
                type="button"
                class="-mx-1.5 -my-1.5 ml-auto inline-flex h-8 w-8 items-center justify-center rounded-lg p-1.5 text-gray-400 hover:text-primary-400 focus:ring-2 focus:ring-gray-300 dark:text-gray-500"
                @click="resetNotification()"
            >
                <XMarkIcon class="size-6" />
            </button>
        </div>
    </div>
</template>
