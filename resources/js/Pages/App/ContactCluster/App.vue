<script setup lang="ts">
import { ArrowPathIcon } from '@heroicons/vue/24/outline';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, Ref } from 'vue';

// @ts-ignore
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet/dist/leaflet.css';

import { App } from '@/types';

import AppIcon from '@/Components/AppIcon.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Map from './Partials/Map.vue';

const props = defineProps<{
    app: App;
    company?: any;
}>();

const loading: Ref<boolean> = ref(false);
const companies: Ref<any[]> = ref([]);

onMounted(async () => {
    loading.value = true;

    const res = await axios.get(
        route('app.contact-cluster', { app: props.app }),
    );
    companies.value = res.data;

    loading.value = false;
});
</script>

<template>
    <Head title="Apps" />

    <AuthenticatedLayout>
        <template #header>
            <AppIcon :type="app.type" />
            {{ app.name }}
        </template>

        <div class="h-[calc(100vh_-_4rem)]">
            <div class="relative h-full w-full p-12">
                <div
                    class="relative flex h-full w-full gap-8 overflow-hidden rounded-lg"
                >
                    <div
                        class="flex h-full w-80 flex-col justify-between rounded-xl bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-white"
                    >
                        <div class="overflow-auto p-4">
                            <h3 class="flex items-center gap-2 text-lg leading-tight text-gray-800">Cluster</h3>
                            <div
                                class="overflow-auto"
                                v-for="(configuration, i) in app.configuration"
                                :key="i"
                            >
                                {{ configuration }}
                            </div>
                        </div>

                        <div class="p-4">
                            <PrimaryButton class="w-full"
                                >Create new Cluster</PrimaryButton
                            >
                        </div>
                    </div>

                    <Map
                        v-if="!loading"
                        :app="app"
                        :companies="companies"
                        :company="company"
                    />

                    <div
                        v-if="loading"
                        class="absolute inset-0 z-[1050] flex items-center justify-center bg-gray-200/50"
                    >
                        <ArrowPathIcon
                            class="size-16 animate-[spin_1.5s_linear_infinite] text-white"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
