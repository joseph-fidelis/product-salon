<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    staffCommissions: {
        type: Array,
        default: () => []
    },
    dailyCommissions: {
        type: Array,
        default: () => []
    },
    serviceCommissions: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    totals: {
        type: Object,
        default: () => ({
            paid: 0,
            pending: 0,
            total: 0
        })
    }
});

// State for filters
const dateFrom = ref(props.filters?.date_from || '');
const dateTo = ref(props.filters?.date_to || '');

// Format currency
const formatCurrency = (amount) => {
    return `₦${parseFloat(amount || 0).toFixed(2)}`;
};

// Apply date filters
const applyFilters = () => {
    let params = {};
    if (dateFrom.value) params.date_from = dateFrom.value;
    if (dateTo.value) params.date_to = dateTo.value;

    router.get(route('commission.statistics'), params);
};

// Reset filters
const resetFilters = () => {
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
};

// Prepare data for charts with null checks
const dailyChartData = computed(() => {
    if (!props.dailyCommissions) return [];

    return props.dailyCommissions.map(item => ({
        date: new Date(item?.day || new Date()).toLocaleDateString(),
        amount: parseFloat(item?.total_amount || 0),
        count: item?.count || 0
    }));
});

const serviceChartData = computed(() => {
    if (!props.serviceCommissions) return [];

    return props.serviceCommissions.map(item => ({
        name: item?.name || 'Unknown',
        amount: parseFloat(item?.total_amount || 0),
        count: item?.count || 0
    }));
});

// Safe getters for totals with null checks
const getPaidTotal = computed(() => props.totals?.paid || 0);
const getPendingTotal = computed(() => props.totals?.pending || 0);
const getTotalAmount = computed(() => props.totals?.total || 0);
</script>

<template>
    <Head title="Commission Statistics" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Commission Statistics</h2>
                <div class="flex gap-2">
                    <Link :href="route('commission.index')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md font-medium">
                        Back to All Commissions
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Date Range</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
                                <input
                                    type="date"
                                    v-model="dateFrom"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
                                <input
                                    type="date"
                                    v-model="dateTo"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                />
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <button
                                @click="applyFilters"
                                class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md font-medium"
                            >
                                Apply Filters
                            </button>
                            <button
                                @click="resetFilters"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md font-medium"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-green-600 dark:text-green-400 mb-1">Paid Commissions</h3>
                            <div class="text-2xl font-bold">{{ formatCurrency(getPaidTotal) }}</div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-yellow-600 dark:text-yellow-400 mb-1">Pending Commissions</h3>
                            <div class="text-2xl font-bold">{{ formatCurrency(getPendingTotal) }}</div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-blue-600 dark:text-blue-400 mb-1">Total Commissions</h3>
                            <div class="text-2xl font-bold">{{ formatCurrency(getTotalAmount) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Charts</h3>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            Charts have been removed from this view to avoid syntax issues. For chart visualizations,
                            consider implementing them separately or using a Vue-compatible charting library.
                        </p>
                    </div>
                </div>

                <!-- Staff Commissions Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Staff Commission Summary</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Staff Member
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Invoices
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Paid Amount
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Pending Amount
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Total Amount
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="staff in staffCommissions || []" :key="staff?.id || index" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ staff?.first_name || '' }} {{ staff?.last_name || '' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ staff?.invoice_count || 0 }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                                {{ formatCurrency(staff?.paid_amount || 0) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                                {{ formatCurrency(staff?.pending_amount || 0) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ formatCurrency(parseFloat(staff?.paid_amount || 0) + parseFloat(staff?.pending_amount || 0)) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <Link
                                                v-if="staff?.id"
                                                :href="route('commission.staff.summary', staff.id)"
                                                class="text-primary hover:text-primary-dark"
                                            >
                                                View Details
                                            </Link>
                                        </td>
                                    </tr>

                                    <!-- Empty State -->
                                    <tr v-if="!staffCommissions || staffCommissions.length === 0">
                                        <td colspan="6" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                            No staff commission data found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Service Commissions Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Top Services by Commission</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Service
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Count
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Total Commission
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Average Commission
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="service in serviceCommissions || []" :key="service?.id || index" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ service?.name || 'Unknown' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ service?.count || 0 }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ formatCurrency(service?.total_amount || 0) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ formatCurrency((service?.total_amount || 0) / (service?.count || 1)) }}
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Empty State -->
                                    <tr v-if="!serviceCommissions || serviceCommissions.length === 0">
                                        <td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                            No service commission data found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
