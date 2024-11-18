<script setup lang="ts">
import { ArrowPathIcon } from '@heroicons/vue/24/outline';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, Ref } from 'vue';

// @ts-ignore
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet/dist/leaflet.css';

import { App } from '@/types';
import Map from './Partials/Map.vue';

const props = defineProps<{
    app: App;
    token: string;
    company: any;
}>();

const loading: Ref<boolean> = ref(false);
const companies: Ref<any[]> = ref([]);

onMounted(async () => {
    loading.value = true;

    const res = await axios.get(
        route('crm-card.contact-cluster', {
            app: props.app,
            token: props.token,
        }),
    );
    companies.value = res.data;

    loading.value = false;
});
</script>

<template>
    <Head title="Contact Cluster - CRM Card" />

    <div class="h-screen w-screen">
        <div class="relative h-full w-full">
            <div class="relative h-full w-full overflow-hidden rounded-3xl">
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
</template>
