<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import { fc } from '@/Helpers/FormatCurrency';
import { Credit } from '@/types';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';

defineProps<{
    credit: Credit;
}>();

const hub = usePage().props.auth.user.selected_hub;

const form = useForm({
    extra_billing_information: hub?.extra_billing_information ?? '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Billing Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's billing information.
            </p>
        </header>

        <div
            v-if="credit.value > 0"
            class="mt-4 text-gray-500 dark:text-gray-300"
        >
            You have a Credit of
            <span class="text-primary-400">{{ fc(credit.value / 100) }}</span>
            left. The Credit is automatically used for the next Payments.
        </div>

        <form
            @submit.prevent="form.patch(route('billing.update'))"
            class="mt-4 space-y-4"
        >
            <div>
                <div
                    v-if="$page.props.auth.subscription"
                    class="inline-flex items-center font-medium text-gray-500 dark:text-gray-300"
                >
                    Your Plan:
                    <span class="ml-1 leading-4 text-primary-400">
                        {{
                            $page.props.system.plans[
                                $page.props.auth.subscription.plan
                            ].name
                        }}
                    </span>
                    <Link
                        class="ml-6 inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
                        :href="route('billing.choose-subscription')"
                    >
                        <template v-if="$page.props.auth.on_grace_period">
                            Resume Plan
                        </template>
                        <template v-else>Change Plan</template>
                    </Link>
                </div>

                <span
                    v-if="$page.props.auth.on_grace_period"
                    class="mt-2 flex items-center gap-1 text-base font-medium leading-4 text-red-600"
                >
                    <ExclamationTriangleIcon class="size-6" />
                    Plan ends on
                    {{
                        dayjs($page.props.auth.subscription?.ends_at).format(
                            'YYYY-MM-DD HH:mm',
                        )
                    }}</span
                >
            </div>
            <div>
                <InputLabel
                    for="extra_billing_information"
                    value="Extra Billing Information"
                />

                <TextArea
                    id="extra_billing_information"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.extra_billing_information"
                    autofocus
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.extra_billing_information"
                />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

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
        </form>
    </section>
</template>
