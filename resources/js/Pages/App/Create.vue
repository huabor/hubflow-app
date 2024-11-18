<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import { AvailableApp, HubspotToken } from '@/types';

import AppIcon from '@/Components/AppIcon.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Select from '@/Components/Select.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref, Ref } from 'vue';
import ContactClusterConfigurationForm from './ContactCluster/ConfigurationForm.vue';

const props = defineProps<{
    type: AvailableApp;
    tokens: HubspotToken[];
}>();

const step: Ref<'base' | 'configuration'> = ref('base');

const form = useForm({
    type: props.type.type,
    name: props.type.name,
    hubspot_token_id: 2, // @TODO change to null
    configuration: {},
});

const proceedWithConfiguration = async () => {
    form.post(route('app.validate-base-information'), {
        onSuccess: () => (step.value = 'configuration'),
    });
};

const selectedToken = computed(() =>
    props.tokens.find((t) => t.id === form.hubspot_token_id),
);

const tokenOptions = computed(() => {
    return props.tokens.map((t) => ({
        key: t.id,
        value: `${t.email} - ${t.hub_domain}`,
    }));
});
</script>

<template>
    <Head title="Apps" />

    <AuthenticatedLayout>
        <template #header>
            <AppIcon :type="type.type" />
            Create {{ type.name }}
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <div class="max-w-xl space-y-6">
                        <div>
                            <InputLabel for="name" value="Name" />

                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                :disabled="step !== 'base'"
                                autocomplete="name"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.name"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="hubspot_token_id"
                                value="Hubspot Token"
                            />

                            <Select
                                :options="tokenOptions"
                                id="hubspot_token_id"
                                class="mt-1 block w-full"
                                v-model="form.hubspot_token_id"
                                required
                                autofocus
                                :disabled="step !== 'base'"
                                autocomplete="hubspot_token_id"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.hubspot_token_id"
                            />
                        </div>

                        <div
                            class="flex items-center justify-end gap-4"
                            v-if="step === 'base'"
                        >
                            <PrimaryButton @click="proceedWithConfiguration">
                                Proceed with configuration
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <ContactClusterConfigurationForm
                        v-if="
                            step === 'configuration' &&
                            selectedToken !== undefined
                        "
                        :token="selectedToken"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
