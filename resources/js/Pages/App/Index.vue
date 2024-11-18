<script setup lang="ts">
import { GiftIcon, MapPinIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { Head, Link } from '@inertiajs/vue3';
import { ref, Ref } from 'vue';

import { App, AvailableApp } from '@/types';

import AppIcon from '@/Components/AppIcon.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps<{
    apps: App[];
    available_apps: AvailableApp[];
}>();

const isModalVisible: Ref<boolean> = ref(false);
const showModal = () => (isModalVisible.value = true);
const closeModal = () => (isModalVisible.value = false);
</script>

<template>
    <Head title="Apps" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <div class="grid grid-cols-4 gap-4">
                        <button
                            @click="showModal"
                            class="flex flex-col items-center justify-center rounded-md border-2 border-dashed border-gray-800 bg-gray-100 p-8 text-gray-800 shadow-lg hover:border-primary-400 hover:text-primary-400 dark:border-gray-400 dark:bg-gray-600 dark:text-gray-400"
                        >
                            <PlusIcon class="size-8" />
                            <span>Create App</span>
                        </button>

                        <Link
                            v-for="app in apps"
                            :key="app.id"
                            :href="route('app.show', { app })"
                            class="flex flex-col items-center justify-center rounded-md border-2 border-dashed border-gray-800 bg-gray-100 p-8 text-gray-800 shadow-lg hover:border-primary-400 hover:text-primary-400 dark:border-gray-400 dark:bg-gray-600 dark:text-gray-400"
                        >
                            <GiftIcon
                                v-if="app.type === 'birthday_reminder'"
                                class="size-8"
                            />

                            <MapPinIcon
                                v-if="app.type === 'contact_cluster'"
                                class="size-8"
                            />

                            <span>{{ app.name }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="isModalVisible" @close="closeModal" max-width="4xl">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    Create App
                </h2>

                <div class="mt-6 grid grid-cols-3 gap-4">
                    <Link
                        v-for="app in available_apps"
                        :key="app.type"
                        :href="route('app.create', { type: app.type })"
                        class="flex flex-col items-center justify-center rounded-md border-2 border-dashed border-gray-800 bg-gray-100 p-8 text-gray-800 shadow-lg hover:border-primary-400 hover:text-primary-400 dark:border-gray-400 dark:bg-gray-600 dark:text-gray-400"
                    >
                        <AppIcon :type="app.type" />

                        <span>{{ app.name }}</span>
                    </Link>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
