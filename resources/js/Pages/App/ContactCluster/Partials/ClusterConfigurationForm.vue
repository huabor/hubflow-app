<script lang="ts" setup>
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    TransitionRoot,
} from '@headlessui/vue';
import {
    ArrowPathIcon,
    CheckIcon,
    ChevronUpDownIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { useForm } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import axios from 'axios';
import { computed, onMounted, ref, Ref, watch } from 'vue';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Select from '@/Components/Select.vue';
import TextInput from '@/Components/TextInput.vue';
import {
    App,
    ContactCluster,
    HubspotObjectType,
    HubspotSearchFilter,
} from '@/types';

const emit = defineEmits(['close']);
const props = defineProps<{
    app: App;
    objectTypes: HubspotObjectType[];
    contactCluster?: ContactCluster;
}>();

const form = useForm<{
    id: number | null;
    type: number;
    name: string;
    color: string;
    filter: HubspotSearchFilter[];
}>({
    id: null,
    type: 1,
    name: '',
    color: '#5E93D2',
    filter: [
        {
            propertyName: '',
            operator: 'EQ',
            value: '',
        },
    ],
});

const colors: string[] = [
    '#5E93D2', // Primary
    '#000000', // Black
    '#94A3B8', // Slate
    '#737373', // Neutral
    '#EF4444', // Red
    '#F97316', // Orange
    '#EAB308', // Yellow
    '#A3E635', // Lime
    '#4ADE80', // Green
    '#2DD4BF', // Teal
    '#60A5FA', // Blue
    '#818CF8', // Indigo
    '#A78BFA', // Violet
    '#E879F9', // Fuchsia
    '#FDA4AF', // Rose
    '#FFFFFF', // White
];

const operators: {
    key: string;
    value: string;
    hint?: string;
}[] = [
    {
        key: 'EQ',
        value: 'Equals',
    },
    {
        key: 'NEQ',
        value: 'Not Equals',
    },
    {
        key: 'HAS_PROPERTY',
        value: 'Has Property',
    },
    {
        key: 'NOT_HAS_PROPERTY',
        value: "Doesn't Has Property",
    },
    {
        key: 'CONTAINS_TOKEN',
        value: 'Contains a token (wildcard = *)',
    },
    {
        key: 'NOT_CONTAINS_TOKEN',
        value: "Doesn't contains a token (wildcard = *)",
    },
];

const companyCount: Ref<number> = ref(0);
const companyCountLoading: Ref<boolean> = ref(false);
const formReadyToSubmit: Ref<boolean> = ref(false);
const properties: Ref<
    {
        name: string;
        label: string;
    }[]
> = ref([]);
const query = ref('');

const filteredProperties = computed(() =>
    query.value === ''
        ? properties.value
        : properties.value.filter((property) =>
              property.label.toLowerCase().includes(query.value),
          ),
);

watch(
    () => form.filter,
    () => {
        formReadyToSubmit.value = false;
    },
    { deep: true },
);

onMounted(async () => {
    if (props.contactCluster !== undefined) {
        form.id = props.contactCluster.id;
        form.type = props.contactCluster.type;
        form.name = props.contactCluster.name;
        form.color = props.contactCluster.color;
        form.filter = props.contactCluster.filter;
    }
    const res = await axios.get(route('hubspot.api.company-property'));
    properties.value = res.data;
});

const updateFilterValue = useDebounceFn((value) => {
    query.value = value;
}, 300);

const addFilter = () => {
    if (form.filter.length < 5) {
        form.filter.push({
            propertyName: '',
            operator: '',
            value: '',
        });
    }
};

const removeFilter = (i: number) => {
    form.filter.splice(i, 1);
};

const loadCountByFilter = async () => {
    companyCountLoading.value = true;

    form.transform((d) => {
        return {
            filter: d.filter.map((filter) => {
                if (
                    ['HAS_PROPERTY', 'NOT_HAS_PROPERTY'].includes(
                        filter.operator,
                    )
                ) {
                    return {
                        propertyName: filter.propertyName,
                        operator: filter.operator,
                    };
                }

                return filter;
            }),
        };
    }).post(route('hubspot.api.company-search'), {
        onSuccess: (res) => {
            companyCount.value = res.props.flash?.data.count;
            formReadyToSubmit.value = true;
        },
        onFinish: () => (companyCountLoading.value = false),
    });
};

const submitForm = () => {
    form.transform((d) => {
        return {
            id: d.id,
            type: d.type,
            name: d.name,
            color: d.color,
            filter: d.filter.map((filter) => {
                if (
                    ['HAS_PROPERTY', 'NOT_HAS_PROPERTY'].includes(
                        filter.operator,
                    )
                ) {
                    return {
                        propertyName: filter.propertyName,
                        operator: filter.operator,
                    };
                }

                return filter;
            }),
        };
    }).post(
        route('app.contact-cluster.store', {
            app: props.app,
        }),
        {
            onSuccess: () => emit('close'),
            onFinish: () => {},
        },
    );
};
</script>

<template>
    <div class="flex flex-col space-y-4 p-4">
        <div class="grid grid-cols-4 gap-4">
            <InputLabel value="Object Type" class="col-span-4" />
            <div
                v-for="objectType in objectTypes"
                :key="objectType.type"
                @click="form.type = objectType.type"
                class="flex cursor-pointer items-center justify-center rounded-xl p-4 shadow-md dark:text-white"
                :class="{
                    'ring-2 ring-primary-400 ring-offset-1 ring-offset-transparent':
                        objectType.type === form.type,
                }"
            >
                {{ objectType.name }}
            </div>
        </div>

        <div>
            <InputLabel for="name" value="Name" />

            <TextInput
                id="name"
                type="text"
                class="mt-1 block w-full"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
            />

            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="grid grid-cols-8 gap-4">
            <InputLabel value="Marker Color" class="col-span-8" />
            <div
                v-for="color in colors"
                :key="color"
                @click="form.color = color"
                class="flex aspect-video cursor-pointer items-center justify-center rounded-xl shadow-md"
                :style="`background-color: ${color};`"
                :class="{
                    'ring-2 ring-primary-400 ring-offset-1 ring-offset-transparent':
                        color === form.color,
                }"
            ></div>
        </div>
    </div>

    <div class="relative">
        <div
            class="absolute inset-0 z-50 rounded-xl bg-gray-100/50 transition-all"
            v-if="companyCountLoading"
        ></div>

        <div class="flex flex-col space-y-4 p-4">
            <div v-for="(filter, i) in form.filter" :key="i" class="flex gap-2">
                <div class="grid w-full grid-cols-3 gap-2">
                    <div>
                        <InputLabel
                            :for="`filter-operator-${i}`"
                            value="Property"
                        />

                        <Combobox v-model="filter.propertyName">
                            <div class="relative mt-1">
                                <div
                                    class="relative w-full cursor-default overflow-hidden rounded-xl text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-primary-300 sm:text-sm"
                                    :class="{
                                        'ring-2 ring-red-500': (
                                            form.errors as any
                                        )[`filter.${i}.propertyName`],
                                    }"
                                >
                                    <ComboboxInput
                                        aria-autocomplete="false"
                                        class="block w-full rounded-xl border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-400 focus:ring-primary-500 disabled:opacity-70 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-400 dark:focus:ring-primary-400"
                                        :displayValue="
                                            () =>
                                                properties.find(
                                                    (p) =>
                                                        p.name ===
                                                        filter.propertyName,
                                                )?.label ?? ''
                                        "
                                        @change="
                                            ($event) =>
                                                updateFilterValue(
                                                    $event.target.value,
                                                )
                                        "
                                    />
                                    <ComboboxButton
                                        class="absolute inset-y-0 right-0 flex items-center pr-2"
                                    >
                                        <ChevronUpDownIcon
                                            class="h-5 w-5 text-gray-400"
                                            aria-hidden="true"
                                        />
                                    </ComboboxButton>
                                </div>

                                <TransitionRoot
                                    leave="transition ease-in duration-100"
                                    leaveFrom="opacity-100"
                                    leaveTo="opacity-0"
                                    @after-leave="query = ''"
                                >
                                    <ComboboxOptions
                                        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                                    >
                                        <div
                                            v-if="
                                                filteredProperties.length ===
                                                    0 && query !== ''
                                            "
                                            class="relative cursor-default select-none px-4 py-2 text-gray-700"
                                        >
                                            Nothing found.
                                        </div>

                                        <ComboboxOption
                                            v-for="property in filteredProperties"
                                            as="template"
                                            :key="property.name"
                                            :value="property.name"
                                            v-slot="{ selected, active }"
                                        >
                                            <li
                                                class="relative cursor-pointer select-none py-1 pl-10 pr-4"
                                                :class="{
                                                    'bg-primary-400 text-white':
                                                        active,
                                                    'text-gray-900': !active,
                                                }"
                                            >
                                                <span
                                                    class="block truncate"
                                                    :class="{
                                                        'font-medium': selected,
                                                        'font-normal':
                                                            !selected,
                                                    }"
                                                >
                                                    {{ property.label }}
                                                </span>
                                                <span
                                                    v-if="selected"
                                                    class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                    :class="{
                                                        'text-white': active,
                                                        'text-primary-400':
                                                            !active,
                                                    }"
                                                >
                                                    <CheckIcon
                                                        class="h-5 w-5"
                                                        aria-hidden="true"
                                                    />
                                                </span>
                                            </li>
                                        </ComboboxOption>
                                    </ComboboxOptions>
                                </TransitionRoot>
                            </div>
                        </Combobox>
                    </div>

                    <div>
                        <InputLabel
                            :for="`filter-operator-${i}`"
                            value="Operator"
                        />

                        <Select
                            :options="operators"
                            :id="`filter-operator-${i}`"
                            class="mt-1 block w-full"
                            :class="{
                                'ring-2 ring-red-500': (form.errors as any)[
                                    `filter.${i}.operator`
                                ],
                            }"
                            v-model="filter.operator"
                            required
                        />
                    </div>

                    <div
                        v-if="
                            !['HAS_PROPERTY', 'NOT_HAS_PROPERTY'].includes(
                                filter.operator,
                            )
                        "
                    >
                        <InputLabel :for="`filter-value-${i}`" value="Value" />

                        <TextInput
                            :for="`filter-id-${i}`"
                            type="text"
                            class="mt-1 block w-full"
                            :class="{
                                'ring-2 ring-red-500': (form.errors as any)[
                                    `filter.${i}.value`
                                ],
                            }"
                            v-model="filter.value"
                            required
                        />
                    </div>
                </div>

                <div class="flex gap-2 self-end">
                    <SecondaryButton
                        v-if="form.filter.length > 1"
                        @click="() => removeFilter(i)"
                    >
                        <TrashIcon class="size-5" />
                    </SecondaryButton>
                </div>
            </div>

            <div class="self-center text-center">
                <InputError class="mb-2" :message="form.errors.filter" />

                <SecondaryButton
                    :disabled="form.filter.length >= 5"
                    class="self-center"
                    @click="addFilter"
                >
                    Add Filter
                </SecondaryButton>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between p-4">
        <div>
            <SecondaryButton
                @click="loadCountByFilter"
                :disabled="companyCountLoading"
            >
                {{ companyCount }} Companies found
                <ArrowPathIcon
                    class="ms-2 size-5"
                    :class="{
                        'animate-spin': companyCountLoading,
                    }"
                />
            </SecondaryButton>
        </div>

        <PrimaryButton
            @click="submitForm"
            :disabled="companyCountLoading || !formReadyToSubmit"
        >
            Save and import
        </PrimaryButton>
    </div>
</template>
