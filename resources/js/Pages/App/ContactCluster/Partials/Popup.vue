<script setup lang="ts">
import { HubspotObject } from '@/types';
import {
    ArrowTopRightOnSquareIcon,
    BuildingOfficeIcon,
    PlusIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';

const emit = defineEmits(['waypoint:add', 'waypoint:remove']);

defineProps<{
    object: HubspotObject;
    isWaypoint: boolean;
}>();
</script>

<template>
    <div class="h-full overflow-auto rounded-xl bg-white text-sm">
        <div
            class="sticky top-0 flex items-center justify-between gap-2 bg-gray-100 p-2 shadow"
        >
            <div class="flex items-center justify-between gap-2">
                <BuildingOfficeIcon class="size-5" />
                <span class="font-semibold">{{ object.properties.name }}</span>
            </div>

            <a
                :href="object.deep_link"
                class="!text-primary-400 hover:!text-primary-600"
                target="_blank"
                title="Open in HubSpot"
            >
                <ArrowTopRightOnSquareIcon class="size-5" />
            </a>
        </div>

        <div class="space-y-2 bg-white">
            <div class="space-y-px p-1.5">
                <span
                    class="inline-block rounded p-px px-0.5"
                    :class="{ 'bg-yellow-400': !object.properties.address }"
                >
                    {{ object.properties.address ?? 'NULL' }}
                </span>

                <br />

                <span
                    class="inline-block rounded p-px px-0.5"
                    :class="{ 'bg-yellow-400': !object.properties.city }"
                >
                    {{ object.properties.city ?? 'NULL' }}
                </span>
                -
                <span
                    class="inline-block rounded p-px px-0.5"
                    :class="{ 'bg-yellow-400': !object.properties.zip }"
                >
                    {{ object.properties.zip ?? 'NULL' }}
                </span>

                <br />

                <span
                    class="inline-block rounded p-px px-0.5"
                    :class="{ 'bg-yellow-400': !object.properties.country }"
                >
                    {{ object.properties.country ?? 'NULL' }}
                </span>
            </div>

            <div>
                <h3 class="px-2 text-xs shadow-sm">Actions</h3>
                <div class="">
                    <a
                        :href="object.deep_link"
                        class="flex items-center justify-between border-b px-2 py-3 !text-primary-400 hover:bg-gray-50 hover:!text-primary-600"
                        target="_blank"
                        title="Open in HubSpot"
                    >
                        Open in HubSpot

                        <ArrowTopRightOnSquareIcon class="size-5" />
                    </a>

                    <button
                        v-if="isWaypoint"
                        @click="emit('waypoint:remove', object)"
                        class="flex w-full items-center justify-between border-b px-2 py-3 !text-primary-400 hover:bg-gray-50 hover:!text-primary-600"
                    >
                        Remove Waypoint

                        <XMarkIcon class="size-5" />
                    </button>

                    <button
                        v-else
                        @click="emit('waypoint:add', object)"
                        class="flex w-full items-center justify-between border-b px-2 py-3 !text-primary-400 hover:bg-gray-50 hover:!text-primary-600"
                    >
                        Add Waypoint
                        <PlusIcon class="size-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
