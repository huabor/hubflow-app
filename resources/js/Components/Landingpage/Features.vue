<script setup lang="ts">
import { GiftIcon, MapPinIcon } from '@heroicons/vue/24/outline';
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
                            Apps
                        </span>
                        <h2
                            class="mt-2 text-balance text-3xl font-semibold tracking-tight text-gray-800 sm:text-5xl dark:text-white"
                        >
                            Boost Your Workflow Choose the right plan for you
                        </h2>
                        <p
                            class="mx-auto mt-6 max-w-2xl text-pretty text-center text-lg text-gray-600 sm:text-lg/8 dark:text-gray-400"
                        >
                            Browse our collection of HubFlow apps, purpose-built
                            to drive efficiency across your team. Simplify
                            processes, save time, and boost your productivity
                            with solutions that integrate seamlessly into your
                            HubSpot platform.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid-cols-2 gap-12 sm:grid">
                <div
                    class="group mb-12 flex flex-col items-center text-center sm:items-start sm:text-left"
                >
                    <div
                        class="relative z-10 mb-10 flex h-[70px] w-[70px] items-center justify-center rounded-[14px] bg-primary-400"
                    >
                        <span
                            class="absolute left-0 top-0 -z-[1] mb-8 flex h-[70px] w-[70px] rotate-[25deg] items-center justify-center rounded-[14px] bg-primary-400 bg-opacity-20 duration-300 group-hover:rotate-45"
                        ></span>

                        <MapPinIcon class="size-10 text-white" />
                    </div>

                    <h4
                        class="mb-3 text-xl font-bold text-gray-800 dark:text-white"
                    >
                        Contact Cluster
                    </h4>
                    <p class="text- text-gray-600 dark:text-gray-400">
                        Visualize your HubSpot contacts on an interactive map,
                        clustered by location for a quick overview of where your
                        clients are based. Easily identify nearby clients when
                        planning appointments, maximizing your time and
                        enhancing relationship-building opportunities.
                    </p>
                </div>

                <div
                    class="group mb-12 flex flex-col items-center text-center sm:items-start sm:text-left"
                >
                    <div
                        class="relative z-10 mb-10 flex h-[70px] w-[70px] items-center justify-center rounded-[14px] bg-primary-400"
                    >
                        <span
                            class="absolute left-0 top-0 -z-[1] mb-8 flex h-[70px] w-[70px] rotate-[25deg] items-center justify-center rounded-[14px] bg-primary-400 bg-opacity-20 duration-300 group-hover:rotate-45"
                        ></span>

                        <GiftIcon class="size-10 text-white" />
                    </div>

                    <h4
                        class="mb-3 text-xl font-bold text-gray-800 dark:text-white"
                    >
                        Birthday Reminder
                    </h4>
                    <p class="text-balance text-gray-600 dark:text-gray-400">
                        Keep track of client birthdays effortlessly with our
                        Birthday Reminder for HubSpot. Automatically get
                        notified of upcoming birthdays, helping you personalize
                        your outreach, strengthen relationships, and make every
                        client feel valued on their special day.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
