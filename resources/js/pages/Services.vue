<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import type { Service } from '@/types/service';
import { publicColumn } from '@/components/data-table/ServiceColumn';
import DataTable from '@/components/data-table/DataTable.vue';


const props = defineProps<{
  services: {
    data: Service[]; 
  } | null;
}>();

</script>

<template>
    <HomeLayout>
        <!-- Your welcome page content here -->
        <div class="w-full">
            <div class="w-full relative h-64 mb-8 -mt-6 -mx-6 lg:-mx-8">
                <!-- Background Image -->
                <img src="/images/header-background-image.png" alt="About Header"
                    class="absolute inset-0 w-full h-full object-cover" />
                <!-- Dark Overlay -->
                <div class="absolute inset-0 bg-black/60 dark:bg-black/70"></div>

                <!-- Content -->
                <div class="relative h-full flex flex-col items-center justify-center py-8">
                    <h2 class="text-3xl font-bold mb-4 text-white">
                        Salon Services
                    </h2>
                    <p class="text-xl font-sm mb-4 text-white">A Service for every need </p>
                </div>
            </div>
             <!-- Content Section -->
             <div class="max-w-6xl mx-auto px-6 py-12 text-center">
                <h1 class="text-4xl font-bold mb-6">
                    <span class="text-gray-700 dark:text-gray-300">OUR </span>
                    <span class="text-red-500 dark:text-red-400">SERVICES</span>
                </h1>

                <!-- Services Table -->
                <DataTable :columns="publicColumn" :data="services?.data || []" />
            </div>


        </div>
    </HomeLayout>
</template>