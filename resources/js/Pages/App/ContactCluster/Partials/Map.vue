<script setup lang="ts">
import {
    ArrowDownIcon,
    ArrowUpIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { computed, onMounted, Ref, ref, watch } from 'vue';

import L, {
    Circle,
    divIcon,
    LatLng,
    Marker,
    MarkerOptions,
    Routing,
} from 'leaflet';
// @ts-ignore
import 'leaflet-routing-machine';
// @ts-ignore
import { MarkerClusterGroup } from 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet/dist/leaflet.css';
//https://leaflet.github.io/Leaflet.heat/dist/leaflet-heat.js

import { App, HubspotObject } from '@/types';
import { ContactCluster } from '../../../../types/index';
import Popup from './Popup.vue';

const map: Ref<any> = ref();
const markers: Ref<MarkerClusterGroup> = ref();

const props = defineProps<{
    app: App;
    contactCluster: ContactCluster[];
    object?: HubspotObject;
}>();

const showTeleportContent = ref(false);
const selectedObject: Ref<HubspotObject | undefined> = ref(undefined);
const selectedObjectIsWaypoint = computed(
    () =>
        waypoints.value.findIndex((c) => c.id === selectedObject.value?.id) >
        -1,
);

const waypoints: Ref<HubspotObject[]> = ref([]);
const routeSummary: Ref<{
    distance: number;
    h: number;
    m: number;
} | null> = ref(null);
const routingControl: Ref<Routing.Control | null> = ref(null);

onMounted(async () => {
    const appLink = route('app.show', {
        type: props.app.type,
    });

    let lat = 46.6995607;
    let lng = 11.6331693;
    let circle;
    if (props.object?.coordinates) {
        lat = props.object.coordinates.x;
        lng = props.object.coordinates.y;

        circle = new Circle(new LatLng(lat, lng), {
            radius: 10000,
            className: 'fill-primary-400 stroke-primary-400',
            weight: 2,
            fillOpacity: 0.2,
        });
    }

    map.value = L.map('map', {
        center: L.latLng(lat, lng),
        zoom: 10,
    });
    map.value.attributionControl.addAttribution(
        `Powered by <a href="${appLink}" target="_blank">Hubflow Apps</a>`,
    );

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {}).addTo(map.value);

    const plan = new L.Routing.Plan([], {
        draggableWaypoints: false,
        addWaypoints: false,
        routeWhileDragging: false,
        createMarker: (waypointIndex, waypoint) => {
            const divIconPlan = divIcon({
                html: `<span class="flex items-center justify-center text-gray-800 h-full w-full">${getWaypointChar(waypointIndex)}</span>`,
                className:
                    'bg-green-400 border-2 border-green-600 rounded-full',
                iconAnchor: [12, 12],
                iconSize: [24, 24],
            });

            let markerOptions: MarkerOptions & {
                id: number;
            } = {
                icon: divIconPlan,
                id: parseInt(waypoint.name ?? '0'),
            };

            const marker = L.marker(waypoint.latLng, markerOptions);

            marker.on('click', (e: L.LeafletMouseEvent) =>
                bindMarkerPopup(marker, e),
            );

            return marker;
        },
    });

    routingControl.value = L.Routing.control({
        routeWhileDragging: false,
        addWaypoints: false,
        show: false,
        plan: plan,
    }).addTo(map.value);

    routingControl.value.on('routesfound', (e: Routing.RoutingResultEvent) => {
        if (e.routes[0].summary !== undefined) {
            const totalTime = e.routes[0].summary.totalTime;
            const h = Math.floor(totalTime / 3600);
            const m = Math.floor((totalTime % 3600) / 60);
            routeSummary.value = {
                h,
                m,
                distance: e.routes[0].summary.totalDistance,
            };
        }
    });

    if (circle !== undefined) {
        circle.addTo(map.value);
    }

    const iconDivDefault = divIcon({
        className: 'bg-blue-400 border-2 border-blue-600 rounded-full',
        iconAnchor: [6, 6],
        iconSize: [12, 12],
    });
    Marker.prototype.options.icon = iconDivDefault;
});

watch(
    () => props.contactCluster,
    () => {
        updateMap();
    },
    { deep: true },
);

const updateMap = () => {
    if (map.value === undefined) return;

    if (markers.value !== undefined) map.value.removeLayer(markers.value);

    // const markers = new MarkerClusterGroup();
    markers.value = new MarkerClusterGroup();
    props.contactCluster.forEach((cluster) => {
        const markerHtmlStyles = `border: 2px solid ${cluster.color};background-color: ${cluster.color}B3;`;

        cluster.resolved_objects.forEach((object) => {
            let markerOptions: MarkerOptions & {
                id: number;
            } = {
                id: object.id,
                title: object.properties.name,
            };

            if (object.id === props.object?.id) {
                markerOptions.icon = divIcon({
                    html: `<div style="${markerHtmlStyles}" class="h-full w-full rounded-full"></div>`,
                    className: `rounded-full`,
                    iconAnchor: [8, 8],
                    iconSize: [16, 16],
                });
            } else {
                markerOptions.icon = divIcon({
                    html: `<div style="${markerHtmlStyles}" class="h-full w-full rounded-full"></div>`,
                    className: `rounded-full`,
                    iconAnchor: [6, 6],
                    iconSize: [12, 12],
                });
            }

            const marker = L.marker(
                [object.coordinates.x, object.coordinates.y],
                markerOptions,
            );

            marker.on('click', (e: L.LeafletMouseEvent) =>
                bindMarkerPopup(marker, e),
            );

            markers.value.addLayer(marker);
        });
    });

    map.value.addLayer(markers.value);
};

const bindMarkerPopup = (marker: L.Marker, e: L.LeafletMouseEvent) => {
    marker.unbindPopup();

    setTimeout(() => {
        const objectId = e.sourceTarget.options.id;

        selectedObject.value = props.contactCluster
            .flatMap((cc) => cc.resolved_objects)
            .find((o) => o.id === objectId);

        marker.bindPopup('', {
            className: 'w-64 h-80',
            autoPan: true,
            closeButton: false, // Hide native Leafet Popup close button; alternatively can use CSS
            closeOnEscapeKey: true,
        });

        setTimeout(() => {
            marker.openPopup();
            showTeleportContent.value = true;
        }, 100);

        marker.getPopup()?.on('remove', () => {
            marker.unbindPopup();
            showTeleportContent.value = false;
        });
    }, 100);
};

const addWaypoint = (company: HubspotObject) => {
    waypoints.value.push(company);

    setWaypoints();
};

const moveWaypoint = (from: number, to: number) => {
    if (from < 0 || to >= waypoints.value.length) return;

    waypoints.value.splice(to, 0, waypoints.value.splice(from, 1)[0]);
    setWaypoints();
};

const removeWaypoint = (company: HubspotObject) => {
    const index = waypoints.value.findIndex((c) => c.id === company.id);

    if (index === -1) return;

    waypoints.value.splice(index, 1);
    setWaypoints();
};

const setWaypoints = () => {
    routingControl.value?.getPlan().setWaypoints(
        waypoints.value.map((w) => {
            return {
                latLng: new LatLng(w.coordinates.x, w.coordinates.y),
                name: `${w.id}`,
            };
        }),
    );
};

const getWaypointChar = (number: number) => String.fromCharCode(number + 65);
</script>

<template>
    <div id="map" class="h-full w-full rounded-xl bg-yellow-400"></div>

    <div ref="popupcard" id="popupcard" v-if="showTeleportContent">
        <Teleport :to="'.leaflet-popup-content'">
            <Popup
                v-if="selectedObject !== undefined"
                :object="selectedObject"
                :is-waypoint="selectedObjectIsWaypoint"
                @waypoint:add="addWaypoint"
                @waypoint:remove="removeWaypoint"
            />
        </Teleport>
    </div>

    <div
        v-if="routeSummary !== null"
        class="absolute right-4 top-4 z-[999] space-y-2 rounded-xl bg-white p-4"
    >
        <div v-if="routeSummary" class="flex items-center">
            {{ waypoints.length }} Stationen in
            <span class="mx-1 text-lg text-primary-400">
                <template v-if="routeSummary.h > 0"
                    >{{ routeSummary.h }}h</template
                >
                {{ routeSummary.m }}min
            </span>
            <span>({{ Math.round(routeSummary.distance / 1000) }} km)</span>
        </div>
        <div
            class="flex items-center justify-between gap-4"
            v-for="(waypoint, i) in waypoints"
            :key="waypoint.id"
        >
            <span>
                {{ getWaypointChar(i) }} -
                {{ waypoint.properties.name }}
            </span>

            <div class="grid grid-cols-3 gap-1">
                <button
                    v-if="i > 0"
                    @click="moveWaypoint(i, i - 1)"
                    class="col-start-1 inline-flex items-center rounded-xl p-px text-center text-sm font-medium text-gray-500 hover:text-gray-800 focus:outline-none disabled:opacity-60"
                >
                    <ArrowUpIcon class="size-5" />
                </button>

                <button
                    v-if="i + 1 < waypoints.length"
                    @click="moveWaypoint(i, i + 1)"
                    class="col-start-2 inline-flex items-center rounded-xl p-px text-center text-sm font-medium text-gray-500 hover:text-gray-800 focus:outline-none disabled:opacity-60"
                >
                    <ArrowDownIcon class="size-5" />
                </button>

                <button
                    @click="removeWaypoint(waypoint)"
                    class="col-start-3 inline-flex items-center rounded-xl p-px text-center text-sm font-medium text-gray-500 hover:text-gray-800 focus:outline-none disabled:opacity-60"
                >
                    <XMarkIcon class="size-5" />
                </button>
            </div>
        </div>
    </div>
</template>

<style>
#map .leaflet-bottom a {
    @apply text-primary-400 hover:underline;
}

#map
    .leaflet-bottom.leaflet-right
    .leaflet-control-attribution.leaflet-control {
    padding-right: 16px;
}

#map .leaflet-popup-content-wrapper {
    padding: 0;
    width: 100%;
    height: 100%;
}

#map .leaflet-popup-content {
    margin: 0;
    width: 100% !important;
    height: 100% !important;
}

#map .leaflet-routing-container {
    display: none !important;
}
</style>
