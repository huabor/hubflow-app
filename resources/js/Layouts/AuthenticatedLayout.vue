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

const resetNotification = () => {
    const pageProps = usePage().props;
    if (pageProps.flash && pageProps.flash?.notification !== null) {
        pageProps.flash.notification = null;
    }
};

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-950">
            <nav
                class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('app.index')">
                                    <HubflowAppsLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                </Link>
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
                                        }}</span
                                    >

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
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-primary-400 focus:outline-none dark:text-gray-400"
                                            >
                                                {{
                                                    $page.props.auth.user?.name
                                                }}

                                                <ChevronDownIcon
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                />
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
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
                                            :href="route('hubspot.token.index')"
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

                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            <ArrowLeftStartOnRectangleIcon
                                                class="size-5"
                                            />
                                            <span>Log Out</span>
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow dark:bg-gray-800"
                v-if="$slots.header"
            >
                <div
                    class="mx-auto flex max-w-7xl items-center justify-between px-4 py-6 sm:px-6 lg:px-8"
                >
                    <h2
                        class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                    >
                        <slot name="header" />
                    </h2>

                    <Link
                        :href="route('app.index')"
                        class="flex items-center space-x-2 text-lg text-gray-800 hover:text-primary-400 dark:text-gray-200"
                    >
                        <span>All Apps</span>
                        <Squares2X2Icon class="size-6" />
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
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
