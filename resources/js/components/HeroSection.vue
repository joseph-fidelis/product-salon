<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <!-- Hero Section -->
    <div class="relative w-[100vw] h-[80vh] -mt-6 -ml-[50vw] left-1/2">
        <!-- Background Image -->
        <img src="/images/header-background-image.png" alt="Salon Background"
            class="absolute inset-0 w-full h-full object-cover" />

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/60 dark:bg-black/70"></div>

        <!-- Content -->
        <div class="relative h-full w-full flex flex-col items-center justify-center text-center px-6 max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                WELCOME TO AVERETTE
                
            </h1>
            <h2 class="text-red-500 text-4xl md:text-5xl lg:text-6xl font-bold mb-4">UNISEX SALON</h2>
            <p class="text-lg text-gray-200 mb-8 max-w-2xl my-8">
                Uyo's #1 Destination For Style, Confidence, And Self-Expression.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <Link :href="route('book-appointment')"
                    class="px-8 py-3 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-300">
                Book Now
                </Link>

                <Link :href="route('services')"
                    class="px-8 py-3 border-2 border-white text-white rounded-md hover:bg-white/10 transition-colors duration-300">
                View Services
                </Link>
            </div>
        </div>
    </div>
    
</template>