<script setup lang="ts">
import { ArrowPathIcon, PencilIcon } from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, Ref } from 'vue';

import { App, ContactCluster, HubspotObject, HubspotObjectType } from '@/types';

import AppIcon from '@/Components/AppIcon.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CrmCardLayout from '@/Layouts/CrmCardLayout.vue';
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue';
import dayjs from 'dayjs';
import ClusterConfigurationForm from './Partials/ClusterConfigurationForm.vue';
import Map from './Partials/Map.vue';

const props = withDefaults(
    defineProps<{
        crmCard?: boolean;
        app: App;
        object_types: HubspotObjectType[];
        contact_cluster: ContactCluster[];
        object?: HubspotObject;
    }>(),
    {
        crmCard: false,
    },
);

const showClusterModal: Ref<boolean> = ref(false);
const selectedContactCluster: Ref<ContactCluster | undefined> = ref();

const visibleContactCluster: Ref<ContactCluster[]> = ref([]);

const editCluster = (cluster: ContactCluster) => {
    selectedContactCluster.value = cluster;
    showClusterModal.value = true;
};

const refreshCluster = (cluster: ContactCluster) => {
    useForm({}).get(
        route('app.contact-cluster.refresh', {
            app: props.app,
            cluster: cluster,
        }),
    );
};

const toggleClusterOnMap = async (cluster: ContactCluster) => {
    const clusterIndex = visibleContactCluster.value.findIndex(
        (c) => c.id === cluster.id,
    );

    if (clusterIndex > -1) {
        visibleContactCluster.value.splice(clusterIndex, 1);
    } else {
        if (cluster.resolved_objects === undefined) {
            const res = await axios.get(
                route('app.contact-cluster', { cluster: cluster }),
            );

            cluster.resolved_objects = res.data;
        }

        visibleContactCluster.value.push(cluster);
    }
};

const closeClusterModal = () => {
    selectedContactCluster.value = undefined;
    showClusterModal.value = false;
};
</script>

<template>
    <Head title="Apps" />

    <component :is="crmCard ? CrmCardLayout : AuthenticatedLayout">
        <template #header>
            <AppIcon :type="app.type" />
            {{ app.name }}
        </template>

        <div
            class="h-[calc(100vh_-_4rem)]"
            :class="{
                '!h-screen': crmCard,
            }"
        >
            <div class="relative h-full w-full p-12">
                <div
                    class="relative flex h-full w-full gap-8 overflow-hidden rounded-xl"
                >
                    <div
                        class="flex h-full w-80 flex-col justify-between rounded-xl bg-white text-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                        <div class="divide-y divide-gray-100 overflow-auto">
                            <h3
                                class="flex items-center gap-2 p-4 text-lg leading-tight text-gray-800 dark:text-white"
                            >
                                Cluster
                            </h3>

                            <div class="divide-y divide-gray-100">
                                <div
                                    v-for="cluster in contact_cluster"
                                    :key="cluster.id"
                                    class="space-y-2 p-4"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center">
                                            <div
                                                class="size-6 rounded-xl border-2"
                                                :style="`background-color: ${cluster.color}B3; border-color: ${cluster.color};`"
                                            ></div>
                                            <span class="ms-2">
                                                {{ cluster.name }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <ArrowPathIcon
                                                class="size-4 cursor-pointer text-gray-600 hover:text-primary-400"
                                                @click="refreshCluster(cluster)"
                                            />
                                            <PencilIcon
                                                class="size-4 cursor-pointer text-gray-600 hover:text-primary-400"
                                                @click="editCluster(cluster)"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span
                                            class="flex items-center rounded-xl bg-gray-200 px-2 py-1 text-sm text-gray-800 dark:bg-gray-600 dark:text-white"
                                        >
                                            {{
                                                object_types.find(
                                                    (ot) =>
                                                        ot.type ===
                                                        cluster.type,
                                                )?.name
                                            }}
                                        </span>
                                        <span class="text-right text-sm">
                                            Resolved
                                            {{ cluster.resolved_objects_count }}
                                            / {{ cluster.objects_count }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span class="mt-1 text-sm font-normal">
                                            <template
                                                v-if="
                                                    cluster.refreshed_at !==
                                                    null
                                                "
                                            >
                                                Refreshed at
                                                {{
                                                    dayjs(
                                                        cluster.refreshed_at,
                                                    ).format(
                                                        'YYYY-MM-DD HH:mm:ss',
                                                    )
                                                }}
                                            </template>
                                        </span>
                                    </div>
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center">
                                            <SwitchGroup>
                                                <div
                                                    class="flex items-center text-sm"
                                                >
                                                    <SwitchLabel class="mr-2">
                                                        Show on Map
                                                    </SwitchLabel>
                                                    <Switch
                                                        :defaultChecked="false"
                                                        v-slot="{ checked }"
                                                        @update:model-value="
                                                            toggleClusterOnMap(
                                                                cluster,
                                                            )
                                                        "
                                                        as="template"
                                                    >
                                                        <button
                                                            :class="
                                                                checked
                                                                    ? 'bg-blue-600'
                                                                    : 'bg-gray-200'
                                                            "
                                                            class="relative inline-flex h-6 w-11 items-center rounded-full"
                                                        >
                                                            <span
                                                                :class="
                                                                    checked
                                                                        ? 'translate-x-6'
                                                                        : 'translate-x-1'
                                                                "
                                                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                            />
                                                        </button>
                                                    </Switch>
                                                </div>
                                            </SwitchGroup>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <PrimaryButton
                                class="w-full"
                                @click="showClusterModal = true"
                            >
                                Create new Cluster
                            </PrimaryButton>
                        </div>
                    </div>

                    <Map
                        :app="app"
                        :contact-cluster="visibleContactCluster"
                        :object="object"
                    />
                </div>
            </div>
        </div>

        <Modal
            :show="showClusterModal"
            max-width="4xl"
            @close="closeClusterModal"
        >
            <ClusterConfigurationForm
                :app="app"
                :contact-cluster="selectedContactCluster"
                :object-types="object_types"
                @close="closeClusterModal"
            />
        </Modal>
    </component>
</template>
