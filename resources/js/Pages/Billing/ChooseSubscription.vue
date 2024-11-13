<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const plans = usePage().props.system.plans;

const currentSubscription = computed(() => {
    const subscription = usePage().props.auth.subscription;
    console.log(subscription);
    if (subscription) return plans[subscription.plan];

    return subscription;
});

const cancelSubscriptionForm = useForm({});
const cancelSubscription = () => {
    if (confirm('Are you sure you want cancel your subscription?')) {
        cancelSubscriptionForm.delete(route('billing.cancel'), {
            preserveScroll: true,
            onSuccess: () => {},
            onError: () => {},
            onFinish: () => {},
        });
    }
};

const resumeSubscriptionForm = useForm({});
const resumeSubscription = () => {
    resumeSubscriptionForm.patch(route('billing.resume'), {
        preserveScroll: true,
        onSuccess: () => {},
        onError: () => {},
        onFinish: () => {},
    });
};
</script>

<template>
    <Head title="Billing" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <div
                        class="mx-auto max-w-screen-xl px-4 py-8 lg:px-6 lg:py-16"
                    >
                        <div class="-mx-4 flex flex-wrap">
                            <div class="w-full px-4">
                                <div
                                    class="mx-auto mb-12 text-center lg:mb-[70px]"
                                >
                                    <span
                                        class="mb-2 block text-lg font-semibold text-primary-400"
                                    >
                                        Pricing
                                    </span>
                                    <h2
                                        class="mt-2 text-balance text-3xl font-semibold tracking-tight text-gray-800 sm:text-5xl dark:text-white"
                                    >
                                        Choose the right plan for you
                                    </h2>
                                    <p
                                        class="mx-auto mt-6 max-w-2xl text-pretty text-center text-lg text-gray-600 sm:text-lg/8 dark:text-gray-400"
                                    >
                                        Choose an affordable HubFlow Apps plan
                                        packed with powerful apps to boost
                                        productivity, enhance efficiency, and
                                        strengthen client relationships.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="space-y-8 sm:gap-6 lg:grid lg:grid-cols-2 lg:space-y-0 xl:gap-10"
                        >
                            <div
                                class="flex flex-col justify-between rounded-3xl bg-white/60 p-8 ring-1 ring-gray-800/10 sm:mx-8 sm:p-10 lg:mx-0 dark:bg-white"
                                :class="{
                                    'border-4 border-primary-400':
                                        plan.key === currentSubscription?.key,
                                }"
                                v-for="plan in plans"
                                :key="plan.key"
                            >
                                <div>
                                    <h3
                                        class="text-base/7 font-semibold text-primary-400"
                                    >
                                        {{ plan.name }}
                                    </h3>
                                    <p class="mt-4 flex items-baseline gap-x-2">
                                        <span
                                            class="text-5xl font-semibold tracking-tight text-gray-800"
                                            >{{ plan.amount }} €</span
                                        >
                                        <span class="text-base text-gray-600"
                                            >/month</span
                                        >
                                    </p>
                                    <p
                                        class="mt-6 text-pretty text-base/7 text-gray-600"
                                    >
                                        {{ plan.description }}
                                    </p>
                                    <ul
                                        role="list"
                                        class="mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10"
                                    >
                                        <li
                                            class="flex items-center gap-x-2"
                                            v-for="(
                                                feature, i
                                            ) in plan.features"
                                            :key="i"
                                        >
                                            <CheckIcon
                                                class="size-6 text-primary-400"
                                            />
                                            {{ feature }}
                                        </li>
                                    </ul>
                                </div>

                                <template v-if="currentSubscription !== null">
                                    <Link
                                        v-if="
                                            currentSubscription.key !== plan.key
                                        "
                                        :href="
                                            route('billing.switch-plan', {
                                                plan: plan.key,
                                            })
                                        "
                                        class="mt-8 block rounded-md px-3.5 py-2.5 text-center font-semibold text-primary-400 ring-1 ring-inset ring-primary-200 hover:ring-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400 sm:mt-10"
                                    >
                                        Switch Plan
                                    </Link>

                                    <DangerButton
                                        v-else-if="
                                            !$page.props.auth?.on_grace_period
                                        "
                                        class="mt-8 sm:mt-10"
                                        :class="{
                                            'opacity-25':
                                                cancelSubscriptionForm.processing,
                                        }"
                                        :disabled="
                                            cancelSubscriptionForm.processing
                                        "
                                        @click="cancelSubscription"
                                    >
                                        Cancel Subscription
                                    </DangerButton>

                                    <PrimaryButton
                                        v-else
                                        class="mt-8 sm:mt-10"
                                        :class="{
                                            'opacity-25':
                                                resumeSubscriptionForm.processing,
                                        }"
                                        :disabled="
                                            resumeSubscriptionForm.processing
                                        "
                                        @click="resumeSubscription"
                                    >
                                        Resume Subscription
                                    </PrimaryButton>
                                </template>
                                <Link
                                    v-else
                                    :href="
                                        route('billing.subscribe', {
                                            plan: plan.key,
                                        })
                                    "
                                    class="mt-8 block rounded-md px-3.5 py-2.5 text-center font-semibold text-primary-400 ring-1 ring-inset ring-primary-200 hover:ring-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400 sm:mt-10"
                                >
                                    Choose Plan
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
