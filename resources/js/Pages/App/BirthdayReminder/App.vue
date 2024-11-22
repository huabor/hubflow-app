<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { OnClickOutside } from '@vueuse/components';
import { useDebounceFn } from '@vueuse/core';
import { computed, onMounted, onUnmounted, ref } from 'vue';

import { App, BirthdayReminderConfiguration } from '@/types';

import AppIcon from '@/Components/AppIcon.vue';
import InputError from '@/Components/InputError.vue';
import InputHelper from '@/Components/InputHelper.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';

interface Property {
    name: string;
    label: string;
}

const props = defineProps<{
    app: App;
    contact_properties: Property[];
}>();

const windowHeight = window.innerHeight;
const comboBoxHeight = computed(() => comboBoxRef.value?.clientHeight ?? 0);

const defaultProperties = [
    'firstname',
    'lastname',
    'date_of_birth',
    'associatedcompanyid',
    'hubspot_owner_id',
];

const comboBoxRef = ref<HTMLDivElement | null>(null);
const comboBoxInputRef = ref<{ input: HTMLInputElement } | null>(null);
const comboBoxInputTop = ref(0);
const comboboxVisible = ref(false);
const query = ref('');
const filteredProperties = computed(() =>
    props.contact_properties.filter(
        (property) =>
            property.label.toLowerCase().includes(query.value) &&
            !defaultProperties.includes(property.name) &&
            !form.properties.includes(property.name),
    ),
);
const form = useForm<BirthdayReminderConfiguration>({
    enabled: false,
    property: '',
    receiver: 'contact_owner',
    receiver_emails: '',
    send_reminder_before: 0,
    properties: [],
});

onUnmounted(() => {
    window.addEventListener('scroll', () => handleScroll());
});

onMounted(() => {
    if (comboBoxInputRef.value !== null)
        comboBoxInputTop.value =
            comboBoxInputRef.value?.input?.getBoundingClientRect().bottom;
    window.addEventListener('scroll', () => handleScroll());

    const configuration = props.app
        .configuration as BirthdayReminderConfiguration;

    if (configuration.property !== undefined)
        form.property = configuration.property;
    else {
        if (
            props.contact_properties.find((p) => p.name === 'date_of_birth') !==
            undefined
        )
            form.property = 'date_of_birth';
    }

    if (configuration.enabled !== undefined)
        form.enabled = configuration.enabled;
    if (configuration.receiver !== undefined)
        form.receiver = configuration.receiver;
    if (configuration.receiver_emails !== undefined)
        form.receiver_emails = configuration.receiver_emails ?? '';
    if (configuration.send_reminder_before !== undefined)
        form.send_reminder_before = configuration.send_reminder_before;
    if (configuration.properties !== undefined)
        form.properties = configuration.properties;
    form.properties = [];
});

const handleScroll = useDebounceFn(
    () => {
        if (comboBoxInputRef.value !== null)
            comboBoxInputTop.value =
                comboBoxInputRef.value?.input?.getBoundingClientRect().bottom;
    },
    50,
    {
        maxWait: 100,
    },
);

const openCombobox = () => {
    comboboxVisible.value = true;
    if (comboBoxInputRef.value !== null)
        comboBoxInputTop.value =
            comboBoxInputRef.value?.input?.getBoundingClientRect().bottom;
};

const closeCombobox = () => {
    comboboxVisible.value = false;
    query.value = '';
};

const addProperty = (property: Property) => {
    form.properties.push(property.name);
    closeCombobox();
};

const removeProperty = (property: string) => {
    const index = form.properties.findIndex((p) => p === property);
    if (index > -1) {
        form.properties.splice(index, 1);
    }
};

const submit = () => {
    form.post(
        route('app.birthday-reminder.store', {
            app: props.app,
        }),
    );
};
</script>

<template>
    <Head title="Apps" />

    <AuthenticatedLayout>
        <template #header>
            <AppIcon :type="app.type" />
            {{ app.name }}
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-xl sm:p-8 dark:bg-gray-800"
                >
                    <div class="flex items-center justify-between">
                        <span class="dark:text-white">
                            A daily reminder for the contact owner or a
                            configured email, notifying about upcoming birthdays
                            based on the
                            <strong>date_of_birth</strong> property.
                        </span>

                        <div>
                            <label
                                class="inline-flex cursor-pointer items-center"
                            >
                                <input
                                    type="checkbox"
                                    v-model="form.enabled"
                                    class="peer hidden"
                                />
                                <div
                                    class="peer relative h-7 w-14 rounded-full bg-gray-200 after:absolute after:left-0.5 after:top-0.5 after:size-6 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-400 peer-checked:after:left-[calc(100%_-_1.625rem)] peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-primary-800"
                                ></div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 max-w-xl space-y-6">
                        <div>
                            <InputLabel>Send Mail to</InputLabel>

                            <ul
                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white text-sm font-medium text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <li
                                    class="w-full rounded-t-xl border-b border-gray-200 p-4 dark:border-gray-600"
                                >
                                    <div class="flex items-center">
                                        <input
                                            id="contact-owner"
                                            type="radio"
                                            v-model="form.receiver"
                                            value="contact_owner"
                                            class="size-5 border-gray-300 bg-gray-100 text-primary-400 focus:ring-2 focus:ring-primary-300 dark:border-gray-500 dark:bg-gray-600 dark:ring-offset-gray-700 dark:focus:ring-primary-600 dark:focus:ring-offset-gray-700"
                                        />

                                        <InputLabel
                                            class="ms-2"
                                            for="contact-owner"
                                            value="Contact Owner"
                                        />
                                    </div>

                                    <div
                                        v-if="form.receiver === 'contact_owner'"
                                        class="ml-2 mt-4"
                                    >
                                        <InputLabel
                                            for="receiver_emails"
                                            value="Fallback Receiver E-Mail"
                                        />
                                        <InputHelper>
                                            This email, used for contacts
                                            without an assigned owner, is
                                            optional and can be left empty or
                                            include multiple addresses separated
                                            by commas.
                                        </InputHelper>

                                        <TextInput
                                            id="receiver_emails"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.receiver_emails"
                                            autofocus
                                            autocomplete="receiver_emails"
                                        />

                                        <InputError
                                            class="mt-2"
                                            :message="
                                                form.errors.receiver_emails
                                            "
                                        />
                                    </div>
                                </li>

                                <li
                                    class="w-full rounded-t-xl border-gray-200 p-4 dark:border-gray-600"
                                >
                                    <div class="flex items-center">
                                        <input
                                            id="email-receiver"
                                            type="radio"
                                            v-model="form.receiver"
                                            value="email_receiver"
                                            class="size-5 border-gray-300 bg-gray-100 text-primary-400 focus:ring-2 focus:ring-primary-300 dark:border-gray-500 dark:bg-gray-600 dark:ring-offset-gray-700 dark:focus:ring-primary-600 dark:focus:ring-offset-gray-700"
                                        />
                                        <InputLabel
                                            class="ms-2"
                                            for="email-receiver"
                                            value="E-Mail Receiver"
                                        />
                                    </div>

                                    <div
                                        v-if="
                                            form.receiver === 'email_receiver'
                                        "
                                        class="ml-2 mt-4"
                                    >
                                        <InputLabel
                                            for="receiver_emails"
                                            value="Fallback Receiver E-Mail"
                                        />
                                        <InputHelper>
                                            The email can include multiple
                                            addresses separated by commas.
                                        </InputHelper>

                                        <TextInput
                                            id="receiver_emails"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.receiver_emails"
                                            required
                                            autofocus
                                            autocomplete="receiver_emails"
                                        />

                                        <InputError
                                            class="mt-2"
                                            :message="
                                                form.errors.receiver_emails
                                            "
                                        />
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <InputLabel
                                for="send_reminder_before"
                                value="Birthday Reminder Timing (Days Before)"
                            />

                            <InputHelper>
                                Specify the number of days before the birthday
                                to send the reminder (minimum 1 day, maximum 21
                                days).
                            </InputHelper>

                            <TextInput
                                id="send_reminder_before"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.send_reminder_before"
                                required
                                min="0"
                                max="21"
                                autofocus
                                autocomplete="send_reminder_before"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.send_reminder_before"
                            />
                        </div>

                        <div>
                            <InputLabel value="HubSpot Properties" />

                            <InputHelper>
                                Properties in the reminder email: lastname,
                                firstname, date of birth, the owner and the
                                associated company. Add more if needed.
                            </InputHelper>

                            <OnClickOutside @trigger="closeCombobox">
                                <div class="relative">
                                    <TextInput
                                        ref="comboBoxInputRef"
                                        id="filter_properties"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="query"
                                        aria-autocomplete="false"
                                        autocomplete="off"
                                        @focus="openCombobox"
                                    />
                                    <div
                                        v-if="comboboxVisible"
                                        ref="comboBoxRef"
                                        class="absolute max-h-64 w-full divide-y overflow-auto rounded-xl border border-gray-300 bg-gray-50 text-gray-900 shadow-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        :class="{
                                            'top-full':
                                                windowHeight >=
                                                comboBoxHeight +
                                                    comboBoxInputTop,
                                            'bottom-full':
                                                windowHeight <
                                                comboBoxHeight +
                                                    comboBoxInputTop,
                                        }"
                                    >
                                        <template
                                            v-if="filteredProperties.length"
                                        >
                                            <div
                                                v-for="property in filteredProperties"
                                                :key="property.name"
                                                @click="addProperty(property)"
                                                class="cursor-pointer p-2 text-sm hover:bg-primary-400 hover:text-white"
                                            >
                                                {{ property.label }}
                                            </div>
                                        </template>
                                        <div v-else class="p-2 text-sm italic">
                                            No Properties found!
                                        </div>
                                    </div>
                                </div>
                            </OnClickOutside>

                            <div class="mt-2 flex flex-wrap gap-1">
                                <span
                                    v-for="property in form.properties"
                                    :key="property"
                                    class="inline-flex items-center rounded bg-primary-100 px-2 py-1 text-sm font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-300"
                                >
                                    {{
                                        contact_properties.find(
                                            (p) => p.name === property,
                                        )?.label
                                    }}
                                    <button
                                        type="button"
                                        @click="removeProperty(property)"
                                        class="ms-2 inline-flex items-center rounded-sm bg-transparent p-1 text-sm text-primary-400 hover:bg-primary-200 hover:text-primary-900 dark:hover:bg-primary-800 dark:hover:text-primary-300"
                                    >
                                        <XMarkIcon class="size-3" />
                                    </button>
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <PrimaryButton
                                @click="submit"
                                :disabled="form.processing"
                            >
                                Save
                            </PrimaryButton>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-if="form.recentlySuccessful"
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Saved.
                                </p>
                            </Transition>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
