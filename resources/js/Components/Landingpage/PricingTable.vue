<script setup lang="ts">
import { CheckIcon } from '@heroicons/vue/24/outline';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const plans = usePage().props.system.plans;

const essentialPlan = computed(() => plans['essential-monthly']);
const enterprisePlan = computed(() => plans['enterprise-monthly']);
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
                        {{ essentialPlan.name }}
                    </h3>
                    <p class="mt-4 flex items-baseline gap-x-2">
                        <span
                            class="text-5xl font-semibold tracking-tight text-gray-800"
                        >
                            {{ essentialPlan.amount }} €
                        </span>
                        <span class="text-base text-gray-600">/month</span>
                    </p>
                    <p class="mt-6 text-pretty text-base/7 text-gray-600">
                        {{ essentialPlan.description }}
                    </p>

                    <ul
                        role="list"
                        class="mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10"
                    >
                        <li
                            class="flex items-center gap-x-2"
                            v-for="(feature, i) in essentialPlan.features"
                            :key="i"
                        >
                            <CheckIcon class="size-6 text-primary-400" />
                            {{ feature }}
                        </li>
                    </ul>

                    <a
                        href="#"
                        class="mt-8 block rounded-lg px-3.5 py-2.5 text-center font-semibold text-primary-400 ring-1 ring-inset ring-primary-200 hover:ring-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400 sm:mt-10"
                    >
                        Get started today
                    </a>
                </div>

                <div
                    class="relative rounded-3xl bg-gray-800 p-8 shadow-2xl ring-1 ring-gray-800/10 sm:p-10 dark:bg-gray-950 dark:ring-gray-950/10"
                >
                    <h3 class="text-base/7 font-semibold text-primary-400">
                        {{ enterprisePlan.name }}
                    </h3>
                    <p class="mt-4 flex items-baseline gap-x-2">
                        <span
                            class="text-5xl font-semibold tracking-tight text-white"
                        >
                            {{ enterprisePlan.amount }} €
                        </span>
                        <span class="text-base text-gray-400">/month</span>
                    </p>
                    <p class="mt-6 text-pretty text-base/7 text-gray-400">
                        {{ enterprisePlan.description }}
                    </p>
                    <ul
                        role="list"
                        class="mt-8 space-y-3 text-sm/6 text-gray-400 sm:mt-10"
                    >
                        <li
                            class="flex items-center gap-x-2"
                            v-for="(feature, i) in enterprisePlan.features"
                            :key="i"
                        >
                            <CheckIcon class="size-6 text-primary-400" />
                            {{ feature }}
                        </li>
                    </ul>
                    <a
                        href="#"
                        class="mt-8 block rounded-lg bg-primary-400 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm duration-300 ease-in-out hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400 sm:mt-10"
                    >
                        Get started today
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
