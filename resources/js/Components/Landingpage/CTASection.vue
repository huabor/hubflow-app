<script setup lang="ts">
import { Squares2X2Icon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section
        class="relative z-10 flex justify-center overflow-hidden bg-primary-400 py-20 lg:py-[115px]"
    >
        <div class="w-full max-w-5xl">
            <div class="relative overflow-hidden">
                <div class="-mx-4 flex flex-wrap items-stretch">
                    <div class="w-full px-4">
                        <div class="mx-auto max-w-[570px] text-center">
                            <h2
                                class="mb-4 flex flex-col text-3xl font-bold text-white md:text-[38px] md:leading-[1.44]"
                            >
                                <span>What Are You Waiting For?</span>
                                <span
                                    class="text-3xl font-normal md:text-[40px]"
                                >
                                    Get Started Now
                                </span>
                            </h2>

                            <p
                                class="mx-auto mb-6 max-w-[515px] text-base leading-[1.5] text-white"
                            >
                                Explore a variety of apps designed to boost your
                                efficiency and streamline your workflow.
                            </p>

                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('app.index')"
                                class="inline-flex items-center space-x-2 rounded-md bg-white px-6 py-2 text-base font-medium text-gray-800 duration-300 ease-in-out hover:text-primary-400 hover:shadow-md"
                            >
                                <span>Apps</span>
                                <Squares2X2Icon class="size-6" />
                            </Link>

                            <Link
                                v-else
                                :href="route('register')"
                                class="inline-flex items-center space-x-2 rounded-md bg-white px-6 py-2 text-base font-medium text-gray-800 duration-300 ease-in-out hover:text-primary-400 hover:shadow-md"
                            >
                                Start using HubFlow Apps
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <span class="absolute left-0 top-0 -z-10">
                <svg
                    width="495"
                    height="470"
                    viewBox="0 0 495 470"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="55"
                        cy="442"
                        r="138"
                        stroke="white"
                        stroke-opacity="0.04"
                        stroke-width="50"
                    ></circle>
                    <circle
                        cx="446"
                        r="39"
                        stroke="white"
                        stroke-opacity="0.04"
                        stroke-width="20"
                    ></circle>
                    <path
                        d="M245.406 137.609L233.985 94.9852L276.609 106.406L245.406 137.609Z"
                        stroke="white"
                        stroke-opacity="0.08"
                        stroke-width="12"
                    ></path>
                </svg>
            </span>
            <span class="absolute bottom-0 right-0 -z-10">
                <svg
                    width="493"
                    height="470"
                    viewBox="0 0 493 470"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <circle
                        cx="462"
                        cy="5"
                        r="138"
                        stroke="white"
                        stroke-opacity="0.04"
                        stroke-width="50"
                    ></circle>
                    <circle
                        cx="49"
                        cy="470"
                        r="39"
                        stroke="white"
                        stroke-opacity="0.04"
                        stroke-width="20"
                    ></circle>
                    <path
                        d="M222.393 226.701L272.808 213.192L259.299 263.607L222.393 226.701Z"
                        stroke="white"
                        stroke-opacity="0.06"
                        stroke-width="13"
                    ></path>
                </svg>
            </span>
        </div>
    </section>
</template>
