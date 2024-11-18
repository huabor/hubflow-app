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

        <div class="h-[calc(100vh_-_9rem)]">
            <div class="relative h-full w-full p-12">
                <div class="relative h-full w-full overflow-hidden rounded-lg">
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
