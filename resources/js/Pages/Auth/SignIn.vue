<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

import HubflowAppsLogo from '@/Components/HubflowAppsLogo.vue';
</script>

<template>
    <Head title="Signin" />

    <div
        class="h-screen w-screen bg-gradient-to-tr from-primary-400/80 to-primary-400"
    >
        <div
            class="mx-auto grid h-screen w-screen max-w-7xl grid-cols-2 gap-8 p-20 pb-0"
        >
            <div class="flex flex-col items-start justify-center pb-20">
                <h1 class="mt-12 text-7xl font-bold text-white">Hey!</h1>
                <h2 class="mt-8 text-xl font-semibold text-white">
                    Join HubFlow Apps and boost your Productivity!
                </h2>
                <p class="mt-8 font-semibold text-gray-200">
                    Experience seamless integration and productivity-boosting
                    features with apps that simplify your HubSpot workflows and
                    optimize your business processes.
                </p>
            </div>

            <div
                class="flex h-full w-full flex-col items-center justify-between rounded-t-3xl bg-white px-16 py-24 shadow-2xl"
            >
                <HubflowAppsLogo class="h-16" dark />

                <div class="flex flex-col items-center justify-center">
                    <a
                        :href="route('oauth.hubspot.redirect')"
                        class="mb-2 me-2 inline-flex items-center rounded-xl bg-[#ff7a59] px-8 py-4 text-center text-xl font-medium text-white hover:bg-[#ff7a59]/90 focus:outline-none focus:ring-4 focus:ring-[#ff7a59]/50 dark:focus:ring-[#ff7a59]/55"
                    >
                        <svg
                            class="me-2 size-8"
                            viewBox="6.20856283 .64498824 244.26943717 251.24701176"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                        >
                            <path
                                d="m191.385 85.694v-29.506a22.722 22.722 0 0 0 13.101-20.48v-.677c0-12.549-10.173-22.722-22.721-22.722h-.678c-12.549 0-22.722 10.173-22.722 22.722v.677a22.722 22.722 0 0 0 13.101 20.48v29.506a64.342 64.342 0 0 0 -30.594 13.47l-80.922-63.03c.577-2.083.878-4.225.912-6.375a25.6 25.6 0 1 0 -25.633 25.55 25.323 25.323 0 0 0 12.607-3.43l79.685 62.007c-14.65 22.131-14.258 50.974.987 72.7l-24.236 24.243c-1.96-.626-4-.959-6.057-.987-11.607.01-21.01 9.423-21.007 21.03.003 11.606 9.412 21.014 21.018 21.017 11.607.003 21.02-9.4 21.03-21.007a20.747 20.747 0 0 0 -.988-6.056l23.976-23.985c21.423 16.492 50.846 17.913 73.759 3.562 22.912-14.352 34.475-41.446 28.985-67.918-5.49-26.473-26.873-46.734-53.603-50.792m-9.938 97.044a33.17 33.17 0 1 1 0-66.316c17.85.625 32 15.272 32.01 33.134.008 17.86-14.127 32.522-31.977 33.165"
                            />
                        </svg>
                        Sign in with HubSpot
                    </a>
                </div>

                <div class="h-16 w-full">
                    <p class="font-sm text-gray-600">
                        By clicking "Sign in with HubSpot", you accept our
                        <Link
                            :href="route('tos')"
                            class="text-primary-400 hover:text-primary-600"
                        >
                            Terms</Link
                        >.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
