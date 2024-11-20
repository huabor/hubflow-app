<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useWindowScroll } from '@vueuse/core';
import { computed } from 'vue';

import HubflowAppsLogo from '@/Components/HubflowAppsLogo.vue';
import { Squares2X2Icon } from '@heroicons/vue/24/outline';

const { y } = useWindowScroll();

const isTop = computed(() => y.value === 0);
</script>

<template>
    <div
        class="fixed left-0 top-0 z-40 flex w-full items-center justify-center duration-300 ease-in-out"
        :class="{
            'bg-white/80 py-2 shadow-md backdrop-blur-sm dark:bg-gray-800/80':
                !isTop,
            'bg-transparent py-5 text-white': isTop,
        }"
    >
        <div class="w-full max-w-5xl">
            <div class="relative -mx-4 flex items-center justify-between">
                <div class="w-44 max-w-full pl-12 md:w-60">
                    <Link :href="route('home')" class="block w-full">
                        <HubflowAppsLogo :inverted="isTop" class="w-full" />
                    </Link>
                </div>

                <div class="flex items-center justify-end pr-12 lg:pr-0">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('app.index')"
                        class="flex items-center space-x-2 py-2 text-base font-medium duration-300 ease-in-out"
                        :class="{
                            'text-white hover:opacity-70': isTop,
                            'text-gray-800 hover:text-primary-400 dark:text-white':
                                !isTop,
                        }"
                    >
                        <span>Apps</span>
                        <Squares2X2Icon class="size-6" />
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('signin')"
                            class="rounded-lg px-6 py-2 text-base font-medium duration-300 ease-in-out"
                            :class="{
                                'bg-white bg-opacity-20 hover:bg-opacity-100 hover:text-primary-400':
                                    isTop,
                                'bg-primary-400 text-white hover:bg-primary-600 hover:shadow-md':
                                    !isTop,
                            }"
                        >
                            Get Started
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
