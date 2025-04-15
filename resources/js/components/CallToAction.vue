<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
<div class="relative w-full  mx-auto mt-8 overflow-hidden rounded-lg">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0">
        <img 
            src="/../../images/call-to-action-background.jpg" 
            alt="Background" 
            class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>
    </div>

    <!-- Content -->
    <div class="relative px-12 py-12">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left">
                <h1 class="text-4xl md:text-3xl font-bold text-white">
                    Book your online Appointment
                </h1>
            </div>
            <div class="flex justify-center">
                <Link :href="route('book-appointment')"
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-red-500 text-white rounded-sm hover:bg-red-600 transition-colors">
                    Book Appointment
                    
                </Link>
            </div>
        </div>
    </div>
</div>
</template>