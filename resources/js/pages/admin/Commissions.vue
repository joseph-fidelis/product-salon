<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    commissions: Object,
    filters: Object,
    pagination: Object,
    staff: Array,
});

// State for filters
const staffId = ref(props.filters?.staff_id || '');
const status = ref(props.filters?.status || '');
const dateFrom = ref(props.filters?.date_from || '');
const dateTo = ref(props.filters?.date_to || '');

// State for bulk actions
const selectedCommissions = ref([]);
const selectAll = ref(false);
const bulkAction = ref('');

// Status options
const statusOptions = [
    { value: '', label: 'All Statuses' },
    { value: 'Pending', label: 'Pending' },
    { value: 'Paid', label: 'Paid' },
];

// Format currency
const formatCurrency = (amount) => {
    return `₦${parseFloat(amount || 0).toFixed(2)}`;
};

// Format date
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString();
};

// Submit filters
const applyFilters = () => {
    let params = {};
    if (staffId.value) params.staff_id = staffId.value;
    if (status.value) params.status = status.value;
    if (dateFrom.value) params.date_from = dateFrom.value;
    if (dateTo.value) params.date_to = dateTo.value;

    router.get(route('commission.index'), params);
};

// Reset filters
const resetFilters = () => {
    staffId.value = '';
    status.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
};

// Update commission status
const updateStatus = (commissionId, newStatus) => {
    if (confirm(`Are you sure you want to mark this commission as ${newStatus}?`)) {
        router.put(route('commission.update.status', commissionId), {
            status: newStatus
        });
    }
};

// Toggle select all commissions
watch(selectAll, (value) => {
    if (value) {
        selectedCommissions.value = props.commissions.data.map(commission => commission.id);
    } else {
        selectedCommissions.value = [];
    }
});

// Handle bulk action submission
const submitBulkAction = () => {
    if (!bulkAction.value) {
        alert('Please select an action');
        return;
    }

    if (selectedCommissions.value.length === 0) {
        alert('Please select at least one commission');
        return;
    }

    if (confirm(`Are you sure you want to mark ${selectedCommissions.value.length} commissions as ${bulkAction.value}?`)) {
        router.post(route('commission.batch.update'), {
            commission_ids: selectedCommissions.value,
            status: bulkAction.value
        });
    }
};

// Calculate totals
const selectedTotal = computed(() => {
    let total = 0;
    props.commissions.data.forEach(commission => {
        if (selectedCommissions.value.includes(commission.id)) {
            total += parseFloat(commission.amount);
        }
    });
    return total;
});

const pendingTotal = computed(() => {
    return props.commissions.data
        .filter(commission => commission.status === 'Pending')
        .reduce((sum, commission) => sum + parseFloat(commission.amount), 0);
});

const paidTotal = computed(() => {
    return props.commissions.data
        .filter(commission => commission.status === 'Paid')
        .reduce((sum, commission) => sum + parseFloat(commission.amount), 0);
});

const totalAmount = computed(() => {
    return props.commissions.data
        .reduce((sum, commission) => sum + parseFloat(commission.amount), 0);
});
</script>

<template>
    <Head title="Commissions" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Commissions</h2>
                <div class="flex gap-2">
                    <Link :href="route('commission.statistics')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        View Statistics
                    </Link>
                    <button
                        @click="submitBulkAction"
                        class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md font-medium"
                        :disabled="selectedCommissions.length === 0 || !bulkAction"
                    >
                        Update Selected
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Filters</h3>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Staff Member</label>
                                <select
                                    v-model="staffId"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                >
                                    <option value="">All Staff</option>
                                    <option v-for="member in staff" :key="member.id" :value="member.id">{{ member.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select
                                    v-model="status"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                >
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                                <input
                                    type="date"
                                    v-model="dateFrom"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
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
                            <div class="text-2xl font-bold">{{ formatCurrency(paidTotal) }}</div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-yellow-600 dark:text-yellow-400 mb-1">Pending Commissions</h3>
                            <div class="text-2xl font-bold">{{ formatCurrency(pendingTotal) }}</div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-blue-600 dark:text-blue-400 mb-1">Total Commissions</h3>
                            <div class="text-2xl font-bold">{{ formatCurrency(totalAmount) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="selectAll"
                                    class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-700"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Select All</span>
                            </label>

                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Selected: {{ selectedCommissions.length }} ({{ formatCurrency(selectedTotal) }})
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <select
                                v-model="bulkAction"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                            >
                                <option value="">Bulk Actions</option>
                                <option value="Paid">Mark as Paid</option>
                                <option value="Pending">Mark as Pending</option>
                            </select>

                            <button
                                @click="submitBulkAction"
                                class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded-md text-sm"
                                :disabled="selectedCommissions.length === 0 || !bulkAction"
                            >
                                Apply
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Commissions Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        <span class="sr-only">Select</span>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Invoice
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Staff Member
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Service
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Customer
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="commission in commissions.data" :key="commission.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            v-model="selectedCommissions"
                                            :value="commission.id"
                                            class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-700"
                                        />
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div v-if="commission.invoice_id" class="text-sm text-gray-900 dark:text-gray-100">
                                            <Link
                                                :href="route('admin.invoice.show', commission.invoice_id)"
                                                class="text-primary hover:text-primary-dark"
                                            >
                                                #{{ commission.invoice_number }}
                                            </Link>
                                        </div>
                                        <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                                            Manual Entry
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <Link
                                            :href="route('commission.staff.summary', commission.staff_id)"
                                            class="text-sm text-gray-900 dark:text-gray-100 hover:text-primary"
                                        >
                                            {{ commission.staff_name }}
                                        </Link>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ commission.service_name }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ commission.customer_name }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(commission.amount) }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ formatDate(commission.date) }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span
                                            :class="commission.status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                            class="px-2 py-1 text-xs rounded-full"
                                        >
                                            {{ commission.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <button
                                                v-if="commission.status === 'Pending'"
                                                @click="updateStatus(commission.id, 'Paid')"
                                                class="text-green-600 hover:text-green-900"
                                                title="Mark as Paid"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                            <button
                                                v-if="commission.status === 'Paid'"
                                                @click="updateStatus(commission.id, 'Pending')"
                                                class="text-yellow-600 hover:text-yellow-900"
                                                title="Mark as Pending"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="!commissions.data || commissions.data.length === 0">
                                    <td colspan="9" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No commissions found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="pagination && pagination.links && pagination.links.length > 3" class="px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total }} results
                            </div>

                            <div class="flex gap-1">
                                <Link
                                    v-for="(link, i) in pagination.links"
                                    :key="i"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-1 rounded-md text-sm',
                                        link.active
                                            ? 'bg-primary text-white'
                                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                                        link.url === null ? 'cursor-not-allowed opacity-50' : ''
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
