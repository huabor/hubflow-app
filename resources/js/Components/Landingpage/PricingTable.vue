<script setup lang="ts">
import { CheckIcon } from '@heroicons/vue/24/outline';
</script>

<template>
    <section
        class="flex justify-center px-4 pb-8 pt-20 sm:px-8 lg:pb-[70px] lg:pt-[120px] dark:bg-gray-800"
    >
        <div class="w-full max-w-5xl">
            <div class="-mx-4 flex flex-wrap">
                <div class="w-full px-4">
                    <div class="mx-auto mb-12 text-center lg:mb-[70px]">
                        <span
                            class="mb-2 block text-lg font-semibold text-primary-400"
                        >
                            Pricing
                        </span>
                        <h2
                            class="mt-2 text-balance text-3xl font-semibold tracking-tight text-gray-800 sm:text-5xl dark:text-white"
                        >
                            Choose the right plan for you
                        </h2>
                        <p
                            class="mx-auto mt-6 max-w-2xl text-pretty text-center text-lg text-gray-600 sm:text-lg/8 dark:text-gray-400"
                        >
                            Choose an affordable HubFlow Apps plan packed with
                            powerful apps to boost productivity, enhance
                            efficiency, and strengthen client relationships.
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="mx-auto grid max-w-lg grid-cols-1 items-center gap-y-6 sm:gap-y-0 lg:max-w-4xl lg:grid-cols-2"
            >
                <div
                    class="rounded-3xl rounded-t-3xl bg-white/60 p-8 ring-1 ring-gray-800/10 sm:mx-8 sm:rounded-b-none sm:p-10 lg:mx-0 lg:rounded-bl-3xl lg:rounded-tr-none dark:bg-white"
                >
                    <h3 class="text-base/7 font-semibold text-primary-400">
                        Hobby
                    </h3>
                    <p class="mt-4 flex items-baseline gap-x-2">
                        <span
                            class="text-5xl font-semibold tracking-tight text-gray-800"
                            >29 €</span
                        >
                        <span class="text-base text-gray-600">/month</span>
                    </p>
                    <p class="mt-6 text-pretty text-base/7 text-gray-600">
                        The perfect plan to get you started with HubFlow apps,
                        boosting your productivity and efficiency from day one.
                    </p>
                    <ul
                        role="list"
                        class="mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10"
                    >
                        <li class="flex items-center gap-x-2">
                            <CheckIcon class="size-6 text-primary-400" />
                            1 connected HubSpot Account
                        </li>
                        <li class="flex items-center gap-x-2">
                            <CheckIcon class="size-6 text-primary-400" />
                            3 Apps
                        </li>
                        <li class="flex items-center gap-x-2">
                            <CheckIcon class="size-6 text-primary-400" />
                            Up to 1,000 companies
                        </li>
                    </ul>
                    <a
                        href="#"
                        class="mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-primary-400 ring-1 ring-inset ring-primary-200 hover:ring-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400 sm:mt-10"
                        >Get started today</a
                    >
                </div>

                <div
                    class="relative rounded-3xl bg-gray-800 p-8 shadow-2xl ring-1 ring-gray-800/10 sm:p-10 dark:bg-gray-950 dark:ring-gray-950/10"
                >
                    <h3 class="text-base/7 font-semibold text-primary-400">
                        Enterprise
                    </h3>
                    <p class="mt-4 flex items-baseline gap-x-2">
                        <span
                            class="text-5xl font-semibold tracking-tight text-white"
                            >$99</span
                        >
                        <span class="text-base text-gray-400">/month</span>
                    </p>
                    <p class="mt-6 text-pretty text-base/7 text-gray-400">
                        The ideal plan for enterprises seeking advanced HubSpot
                        apps to maximize productivity, streamline operations,
                        and strengthen client relationships at scale.
                    </p>
                    <ul
                        role="list"
                        class="mt-8 space-y-3 text-sm/6 text-gray-400 sm:mt-10"
                    >
                        <li class="flex gap-x-3">
                            <CheckIcon class="size-6 text-primary-400" />
                            Unlimited connected HubSpot Accounts
                        </li>
                        <li class="flex gap-x-3">
                            <CheckIcon class="size-6 text-primary-400" />
                            Unlimited Apps
                        </li>
                        <li class="flex gap-x-3">
                            <CheckIcon class="size-6 text-primary-400" />
                            Unlimited Companies
                        </li>
                        <li class="flex gap-x-3">
                            <CheckIcon class="size-6 text-primary-400" />
                            All App Settings
                        </li>
                        <li class="flex gap-x-3">
                            <CheckIcon class="size-6 text-primary-400" />
                            Dedicated support representative
                        </li>
                    </ul>
                    <a
                        href="#"
                        class="mt-8 block rounded-md bg-primary-400 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm duration-300 ease-in-out hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400 sm:mt-10"
                        >Get started today</a
                    >
                </div>
            </div>
        </div>
    </section>
</template>
