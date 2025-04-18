<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Footer from '@/Components/Footer.vue';
import CallToAction from '@/Components/CallToAction.vue';
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue';
</script>

<template>

    <Head title="Home">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex flex-col min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <!-- Header -->
        <header class="w-full px-6 py-4">
            <nav class="flex items-center justify-between max-w-7xl mx-auto mb-8">

                <div class="flex items-center gap-2">
                    <img src="/images/icon.png" alt="Averette Salon Logo" class="w-48 h-8">
                    <!-- <span class="text-gray-700 dark:text-gray-700">AVERETTE </span>
                    <span class="text-red-500 dark:text-red-400">SALON</span> -->
                </div>
                <div class="flex items-center gap-4">
                    <Link :href="route('home')"
                        class="inline-block rounded-sm px-5 py-1.5 text-sm leading-normal hover:text-red-500 dark:hover:text-red-400 transition-colors duration-300">
                    Home
                    </Link>
                    <Link :href="route('services')"
                        class="inline-block rounded-sm px-5 py-1.5 text-sm leading-normal hover:text-red-500 dark:hover:text-red-400 transition-colors duration-300">
                    Service
                    </Link>
                    <Link :href="route('about')"
                        class="inline-block rounded-sm px-5 py-1.5 text-sm leading-normal hover:text-red-500 dark:hover:text-red-400 transition-colors duration-300">
                    About
                    </Link>
                    <Link :href="route('contact')"
                        class="inline-block rounded-sm px-5 py-1.5 text-sm leading-normal hover:text-red-500 dark:hover:text-red-400 transition-colors duration-300">
                    Contact
                    </Link>
                    <Link :href="route('booking.form')"
                        class="inline-block px-6 py-3 text-sm font-medium bg-red-500 text-white rounded-sm hover:bg-red-600 transition-colors">
                    Book Appointment
                    </Link>
                    <!-- <ThemeSwitcher class="ml-4" /> -->
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="flex-grow w-full">
            <slot></slot>
        </main>

        <!-- Call to Action -->
        <CallToAction />

        <!-- Footer -->
        <Footer />
    </div>
</template>
