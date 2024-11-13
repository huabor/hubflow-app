<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import CTASection from '@/Components/Landingpage/CTASection.vue';
import Features from '@/Components/Landingpage/Features.vue';
import Footer from '@/Components/Landingpage/Footer.vue';
import Hero from '@/Components/Landingpage/Hero.vue';
import Navbar from '@/Components/Landingpage/Navbar.vue';
import PricingTable from '@/Components/Landingpage/PricingTable.vue';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="Welcome" />

    <Navbar />

    <Hero />

    <Features />

    <CTASection />

    <PricingTable />

    <Footer />
</template>
