<script setup lang="ts">
import { Squares2X2Icon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="relative flex h-screen justify-center overflow-hidden bg-gradient-to-tr from-primary-400/80 to-primary-400 pt-[120px] md:pt-[130px] lg:pt-[160px]"
    >
        <div class="flex h-full flex-col items-center justify-between px-4">
            <div class="mx-auto max-w-[780px] text-center">
                <h1
                    class="mb-6 text-3xl font-bold leading-snug text-white sm:text-4xl sm:leading-snug lg:text-5xl lg:leading-[1.2]"
                >
                    HubFlow Apps to Power Up Your Productivity
                </h1>
                <p
                    class="mx-auto mb-9 max-w-[600px] text-base font-medium text-white sm:text-lg sm:leading-[1.44]"
                >
                    Experience seamless integration and productivity-boosting
                    features with apps that simplify your HubSpot workflows and
                    optimize your business processes.
                </p>
                <div
                    class="mb-10 flex flex-wrap items-center justify-center gap-5"
                >
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('app.index')"
                        class="flex items-center space-x-2 rounded-md bg-white px-6 py-2 text-base font-medium text-gray-800 duration-300 ease-in-out hover:text-primary-400 hover:shadow-md"
                    >
                        <span>Apps</span>
                        <Squares2X2Icon class="size-6" />
                    </Link>

                    <Link
                        v-else
                        :href="route('register')"
                        class="flex items-center space-x-2 rounded-md bg-white px-6 py-2 text-base font-medium text-gray-800 duration-300 ease-in-out hover:text-primary-400 hover:shadow-md"
                    >
                        Get Started
                    </Link>
                </div>
            </div>

            <div class="relative z-10 mx-auto max-w-[845px]">
                <div class="mt-16">
                    <img
                        src="https://placehold.co/845x600/EEE/31343C"
                        alt="hero"
                        class="mx-auto max-w-full rounded-t-xl rounded-tr-xl"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
