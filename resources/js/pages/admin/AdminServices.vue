<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { Service } from '@/types/service';
import DataTable from '@/components/data-table/DataTable.vue';
import ServiceDialog from '@/components/dialog/ServiceDialog.vue';
import { ref } from 'vue';
import { get } from '@/components/data-table/ServiceColumn';
import type { BreadcrumbItem } from '@/types';
import { getServiceColumns } from '@/components/data-table/ServiceColumn';
import { router } from '@inertiajs/vue3'

const props = defineProps<{
    services: {
        data: Service[];
    } | null;
}>();

const dialogOpen = ref(false);

const selectedService = ref<Service | null>(null)

const columns = getServiceColumns({
    onEdit: handleEdit,
    onDelete: handleDelete,
})

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Services', href: '/admin-services' },
];

const highestPriceService = props.services?.data.reduce((maxService, currentService) => {
    return parseFloat(currentService.price) > parseFloat(maxService.price) ? currentService : maxService;
}, props.services?.data[0]);

const lowestPriceService = props.services?.data.reduce((minService, currentService) => {
    return parseFloat(currentService.price) < parseFloat(minService.price) ? currentService : minService;
}, props.services?.data[0]);



function handleEdit(service: Service) {
    selectedService.value = service
    dialogOpen.value = true
}

function handleDelete(service: Service) {
    router.delete(route('admin.services.destroy', service.id), {
      preserveScroll: true,
      onSuccess: () => {
        console.log('Service deleted successfully');
      },
      onError: (errors) => {
        // toast.success('Error deleting service');
        console.log('Service failed');
      }
    });
}

</script>

<template>

    <Head title="Services" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Analytics Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Card 1 -->
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex items-center justify-center h-full w-full flex-col space-y-4">
                        <h1 class="text-4xl font-semibold">{{ props.services?.data.length || 0 }}</h1>
                        <h1 class="text-xl">Total Services</h1>
                    </div>
                </div>
                <!-- Card 2 -->
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex items-center justify-center h-full w-full flex-col space-y-4">
                        <h1 class="text-4xl font-semibold">₦ {{ highestPriceService ? highestPriceService.price :
                            '0.00'}}</h1>
                        <h1 class="text-xl">Most Expensive</h1>
                    </div>
                </div>
                <!-- Card 3 -->
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex items-center justify-center h-full w-full flex-col space-y-4">
                        <h1 class="text-4xl font-semibold">₦ {{ lowestPriceService ? lowestPriceService.price : "0.00"
                            }}</h1>
                        <h1 class="text-xl">Cheapest</h1>
                    </div>
                </div>
            </div>

            <!-- Add Button -->
            <div class="flex justify-end mt-4">
                <button @click="dialogOpen = true"
                    class="border border-sidebar-border/70 dark:border-sidebar-border text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 transition duration-200">
                    + Add Service
                </button>
            </div>

            <!-- Table Section -->
            <div class="relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <DataTable :columns="columns" :data="services?.data || []" @edit="handleEdit" @delete="handleDelete" />
            </div>
        </div>

        <!-- Dialog -->
        <ServiceDialog :open="dialogOpen" @close="dialogOpen = false" :service="selectedService" />
    </AppLayout>
</template>
