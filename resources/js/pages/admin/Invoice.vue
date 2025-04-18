<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
    invoices: {
        type: Array,
        default: () => [] // Provide default empty array
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    pagination: {
        type: Object,
        default: () => ({})
    }
});

// Debug what data we're receiving with more detail
onMounted(() => {
    console.log('Invoices:', props.invoices);
    console.log('Pagination:', props.pagination);
    // Add more detailed debugging
    if (props.invoices && props.invoices.length > 0) {
        console.log('First invoice:', props.invoices[0]);
        console.log('Invoice keys:', Object.keys(props.invoices[0]));
    }
    // Check if data might be nested
    if (props.invoices && props.invoices.data) {
        console.log('Data is nested. First invoice:', props.invoices.data[0]);
    }
});

// Process invoices data - handle both array and paginated object formats
const processedInvoices = computed(() => {
    // Handle case where Laravel returns paginated data with a "data" property
    if (props.invoices && 'data' in props.invoices) {
        return props.invoices.data || [];
    }
    // Handle case where it's directly an array
    return props.invoices || [];
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Invoices',
        href: '/admin/invoice',
    },
];

const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

// Format currency with null safety
const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return '₦0.00';
    return `₦${parseFloat(amount).toFixed(2)}`;
};

// Format date with null safety
const formatDate = (dateString) => {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString();
    } catch (e) {
        return '';
    }
};

// Status badge color with null safety
const getStatusColor = (status) => {
    switch (status?.toLowerCase?.()) {
        case 'paid':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'overdue':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
};

// Submit search
const submitSearch = () => {
    window.location.href = `/admin/invoice?search=${searchQuery.value || ''}&status=${statusFilter.value || ''}`;
};

// Safe delete handler with null check
const handleDelete = (invoiceId: number) => {
    if (!invoiceId) {
        console.error('Cannot delete: Invoice ID is missing');
        return;
    }

    if (confirm('Are you sure you want to delete this invoice?')) {
        // Use the imported router instead of accessing window.$inertia
        router.delete(`/admin/invoice/${invoiceId}`);
    }
};
</script>

<template>
    <Head title="Invoices" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <h1 class="text-2xl font-semibold">Invoices</h1>
                <Link
                    href="/admin/invoice/generate"
                    class="flex items-center rounded-lg bg-primary px-4 py-2 text-black hover:bg-primary/90"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Create Invoice
                </Link>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 sm:flex-row sm:items-center">
                <div class="flex flex-1 items-center rounded-lg border border-sidebar-border/70 px-3 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search invoices..."
                        class="w-full border-0 bg-transparent outline-none"
                    />
                </div>

                <div class="flex w-full flex-wrap gap-2 sm:w-auto">
                    <select
                        v-model="statusFilter"
                        class="rounded-lg border border-sidebar-border/70 px-3 py-2"
                    >
                        <option value="">All Statuses</option>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Overdue">Overdue</option>
                    </select>

                    <button
                        @click="submitSearch"
                        class="rounded-lg bg-primary px-4 py-2 text-black hover:bg-primary/90"
                    >
                        Apply Filters
                    </button>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="rounded-xl border border-sidebar-border/70 p-4">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b text-left text-sm">
                            <tr>
                                <th class="whitespace-nowrap pb-4 pr-4">Invoice #</th>
                                <th class="whitespace-nowrap pb-4 pr-4">Customer</th>
                                <th class="whitespace-nowrap pb-4 pr-4">Date</th>
                                <th class="whitespace-nowrap pb-4 pr-4">Total</th>
                                <th class="whitespace-nowrap pb-4 pr-4">Status</th>
                                <th class="whitespace-nowrap pb-4 pr-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="invoice in processedInvoices" :key="invoice?.id" class="border-b">
                                <td class="whitespace-nowrap py-4 pr-4">#{{ invoice?.id || 'N/A' }}</td>
                                <td class="whitespace-nowrap py-4 pr-4">{{ invoice?.customer_name || 'N/A' }}</td>
                                <td class="whitespace-nowrap py-4 pr-4">{{ formatDate(invoice?.invoice_date) }}</td>
                                <td class="whitespace-nowrap py-4 pr-4">{{ formatCurrency(invoice?.total) }}</td>
                                <td class="whitespace-nowrap py-4 pr-4">
                                    <span :class="[getStatusColor(invoice?.status), 'rounded-full px-2 py-1 text-xs']">
                                        {{ invoice?.status || 'Unknown' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-4 pr-4">
                                    <div class="flex gap-2">
                                        <Link
                                            v-if="invoice?.id"
                                            :href="`/admin/invoice/${invoice.id}`"
                                            class="rounded-lg bg-gray-100 p-2 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700"
                                            title="View"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>

                                        <Link
                                            v-if="invoice?.id && invoice?.status !== 'Paid'"
                                            :href="`/admin/invoice/${invoice.id}/mark-paid`"
                                            method="put"
                                            as="button"
                                            class="rounded-lg bg-green-100 p-2 hover:bg-green-200 dark:bg-green-900 dark:hover:bg-green-800"
                                            title="Mark as Paid"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-800 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </Link>

                                        <button
                                            v-if="invoice?.id"
                                            @click="handleDelete(invoice.id)"
                                            class="rounded-lg bg-red-100 p-2 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800"
                                            title="Delete"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-800 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!processedInvoices || processedInvoices.length === 0">
                                <td colspan="6" class="py-4 text-center text-gray-500">No invoices found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="pagination?.links && pagination.links.length > 3" class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} results
                    </div>
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, i) in pagination.links"
                            :key="i"
                            :href="link?.url || '#'"
                            v-html="link?.label || ''"
                            class="flex h-8 min-w-[2rem] items-center justify-center rounded-md px-2 text-sm"
                            :class="{
                                'bg-primary text-white': link?.active,
                                'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700': !link?.active,
                                'opacity-50 cursor-not-allowed': !link?.url
                            }"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
