<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

import '@kalisio/leaflet.donutcluster/src/Leaflet.DonutCluster.css';
import '@kalisio/leaflet.donutcluster/src/Leaflet.DonutCluster.js';
import L from 'leaflet';
import 'leaflet.markercluster/dist/leaflet.markercluster';
import 'leaflet/dist/leaflet.css';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps<{
    companies: any[];
}>();

onMounted(() => {
    const map = L.map('map', {
        center: L.latLng(46.6995607, 11.6331693),
        zoom: 14,
    });

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {}).addTo(map);

    const markers = L.DonutCluster(
        {
            chunkedLoading: true,
        },
        {
            key: 'title',
            // arcColorDict: {
            //     A: 'red',
            //     B: 'blue',
            //     C: 'yellow',
            //     D: 'black',
            // },
        },
    );

    var types = ['A', 'B', 'C', 'D'];

    map.addLayer(markers);

    props.companies.forEach((company) => {
        console.log(company);
        const marker = L.marker(company.coordinates.coordinates, {
            title: company.industry_sector,
        });

        marker.bindPopup(`
            <div class="flex flex-col">
                <span class="font-semibold">${company.name}</span>
                <span class="text-small">${company.industry_sector}</span>
                <span>${company.address}, ${company.city}, ${company.zip} - ${company.country}</span>
                <a href="${company.deep_link}" target="_blank">Open Hubspot</a>
            </div>
        `);
        markers.addLayer(marker);
    });

    map.addLayer(markers);

    // const key = 'YOUR_MAPTILER_API_KEY_HERE';

    // L.tileLayer(
    //     `https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=${key}`,
    //     {
    //         //style URL
    //         tileSize: 512,
    //         zoomOffset: -1,
    //         minZoom: 1,
    //         attribution:
    //             '\u003ca href="https://www.maptiler.com/copyright/" target="_blank"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href="https://www.openstreetmap.org/copyright" target="_blank"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e',
    //         crossOrigin: true,
    //     },
    // ).addTo(map);
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-slate-800 dark:text-slate-200"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-slate-800"
                >
                    <div class="p-6 text-slate-900 dark:text-slate-100">
                        You're logged in!
                    </div>

                    <div id="map" class="h-[600px]"></div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
