<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, watch } from 'vue';
// import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    appointments: Object,
    filters: Object,
});

// Add this debugging console.log
console.log('Received appointments:', props.appointments);


const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const date = ref(props.filters?.date || '');

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString();
  } catch (e) {
    return '';
  }
};

const statusOptions = [
    { value: '', label: 'All Statuses' },
    { value: 'Pending', label: 'Pending' },
    { value: 'Approved', label: 'Approved' },
    { value: 'Completed', label: 'Completed' },
    { value: 'Cancelled', label: 'Cancelled' },
    { value: 'No-Show', label: 'No-Show' }
];

const submit = () => {
    let params = {};
    if (search.value) params.search = search.value;
    if (status.value) params.status = status.value;
    if (date.value) params.date = date.value;

    window.location.href = route('admin.appointments.index', params);
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    date.value = '';
    submit();
};

const formatTime = (time: string) => {
    if (!time) return '';
    try {
        const [hours, minutes] = time.split(':');
        if (!hours || !minutes) return '';

        const formattedTime = new Date();
        formattedTime.setHours(parseInt(hours), parseInt(minutes));
        return formattedTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return '';
    }
};

const getStatusClass = (status: string) => {
    if (!status) return 'bg-gray-100 text-gray-800';

    const statusClasses = {
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Approved': 'bg-blue-100 text-blue-800',
        'Completed': 'bg-green-100 text-green-800',
        'Cancelled': 'bg-red-100 text-red-800',
        'No-Show': 'bg-gray-100 text-gray-800'
    };

    return statusClasses[status] || 'bg-gray-100 text-gray-800';
};


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Appointment',
        href: '/admin/Appointments',
    },
];

// Get services list as a string
const getServicesList = (services: any) => {
    if (!services || !Array.isArray(services) || services.length === 0) return 'No services';
    return services.map(service => service?.name || 'Unnamed service').join(', ');
};
</script>

<template>
    <Head title="Appointments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Appointments</h2>
                <Link :href="route('admin.appointments.create')" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md font-medium">
                    Create Appointment
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Filters -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-grow">
                                <input
                                    type="text"
                                    v-model="search"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-300"
                                    placeholder="Search by name, email or phone..."
                                />
                            </div>
                            <div class="md:w-48">
                                <select
                                    v-model="status"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="md:w-48">
                                <input
                                    type="date"
                                    v-model="date"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-300"
                                />
                            </div>
                            <div class="flex gap-2">
                                <button
                                    @click="submit"
                                    class="px-4 py-2 bg-primary text-black rounded-md hover:bg-primary-dark"
                                >
                                    Filter
                                </button>
                                <button
                                    v-if="search || status || date"
                                    @click="clearFilters"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600"
                                >
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Appointments Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Customer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date & Time
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Services
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="appointment in props.appointments?.data || []" :key="appointment?.id || Math.random()" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        #{{ appointment?.id || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <div>{{ appointment?.customer_name || 'N/A' }}</div>
                                        <div class="text-xs text-gray-400">{{ appointment?.customer_phone || 'No phone' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <div>{{ formatDate(appointment?.appointment_date) }}</div>
                                        <div class="text-xs">{{ formatTime(appointment?.appointment_time) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 max-w-xs truncate">
                                        {{ getServicesList(appointment?.services) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[getStatusClass(appointment?.status), 'px-2 py-1 text-xs rounded-full']">
                                            {{ appointment?.status || 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <div class="flex space-x-2">
                                            <Link
                                                v-if="appointment?.id"
                                                :href="route('admin.appointments.show', appointment.id)"
                                                class="text-primary hover:text-primary-dark"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>

                                            <Link
                                                v-if="appointment?.id && appointment?.can_convert_to_invoice"
                                                :href="route('admin.appointments.convert.invoice', appointment.id)"
                                                method="post"
                                                as="button"
                                                class="text-blue-600 hover:text-blue-800"
                                                title="Convert to Invoice"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </Link>

                                            <Link
                                                v-if="appointment?.id && appointment?.invoice_id"
                                                :href="route('admin.invoice.show', appointment.invoice_id)"
                                                class="text-green-600 hover:text-green-800"
                                                title="View Invoice"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="!props.appointments?.data || props.appointments.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No appointments found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <!-- <div class="px-6 py-4">
                        <Pagination :links="appointments.meta.links" />
                    </div> -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>
