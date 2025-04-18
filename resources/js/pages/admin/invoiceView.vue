<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    invoice: {
        type: Object,
        required: true
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Invoice',
        href: '/admin/invoice',
    },
    {
        title: `Invoice #${props.invoice.id}`,
        href: `/admin/invoice/${props.invoice.id}`,
    },
];

const printInvoice = () => {
    window.print();
};

// Mark invoice as paid
const markAsPaid = () => {
    if (confirm('Are you sure you want to mark this invoice as paid?')) {
        router.put(`/admin/invoice/${props.invoice.id}/mark-paid`, {}, {
            onSuccess: () => {
                // This will be handled by the controller redirect
            }
        });
    }
};

// Delete invoice
const deleteInvoice = () => {
    if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        router.delete(`/admin/invoice/${props.invoice.id}`, {
            onSuccess: () => {
                // This will be handled by the controller redirect
            }
        });
    }
};

// Format date (YYYY-MM-DD to DD/MM/YYYY)
const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString();
};
</script>

<template>
    <Head :title="`Invoice #${invoice.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="print:p-0 p-6">
            <div class="mb-6 flex items-center justify-between print:hidden">
                <h1 class="text-2xl font-semibold">Invoice #{{ invoice.id }}</h1>
                <div class="flex gap-3">
                    <button @click="printInvoice" class="flex items-center rounded-lg bg-gray-100 px-4 py-2 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Invoice
                    </button>

                    <button
                        v-if="invoice.status !== 'Paid'"
                        @click="markAsPaid"
                        class="flex items-center rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mark as Paid
                    </button>

                    <button
                        @click="deleteInvoice"
                        class="flex items-center rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Invoice
                    </button>
                </div>
            </div>

            <!-- Invoice Container -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-8 shadow-sm dark:bg-gray-900">
                <!-- Header -->
                <div class="flex flex-col justify-between border-b border-sidebar-border/50 pb-6 sm:flex-row">
                    <div>
                        <h2 class="text-xl font-bold">INVOICE</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Invoice #{{ invoice.id }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Date: {{ formatDate(invoice.invoice_date) }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:text-right">
                        <h3 class="text-lg font-semibold">Your Salon Name</h3>
                        <address class="mt-1 not-italic text-sm text-gray-500 dark:text-gray-400">
                            123 Salon Street<br>
                            City, State 12345<br>
                            Phone: (123) 456-7890<br>
                        </address>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="mt-6 grid gap-6 sm:grid-cols-2">
                    <div>
                        <h3 class="mb-2 font-medium">Bill To:</h3>
                        <p class="text-sm">{{ invoice.customer_name }}</p>
                        <p class="text-sm" v-if="invoice.customer_email">{{ invoice.customer_email }}</p>
                        <p class="text-sm">{{ invoice.customer_phone }}</p>
                    </div>
                    <div class="sm:text-right">
                        <h3 class="mb-2 font-medium">Payment Details:</h3>
                        <p class="text-sm">Payment Method: {{ invoice.payment_method }}</p>
                        <p class="text-sm">Status:
                            <span
                                :class="{
                                    'text-green-500': invoice.status === 'Paid',
                                    'text-yellow-500': invoice.status === 'Pending',
                                    'text-red-500': invoice.status === 'Overdue'
                                }"
                            >
                                {{ invoice.status }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Services Table -->
                <div class="mt-8 overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="border-b border-sidebar-border/50 text-left">
                                <th class="pb-3 text-sm font-medium">Service</th>
                                <th class="pb-3 text-sm font-medium">Staff</th>
                                <th class="pb-3 text-sm font-medium">Qty</th>
                                <th class="pb-3 text-sm font-medium">Price</th>
                                <th class="pb-3 text-sm font-medium">Discount</th>
                                <th class="pb-3 text-right text-sm font-medium">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in invoice.invoice_items" :key="index" class="border-b border-sidebar-border/50">
                                <td class="py-3 text-sm">{{ item.service_name }}</td>
                                <td class="py-3 text-sm">{{ item.staff_name }}</td>
                                <td class="py-3 text-sm">{{ item.quantity }}</td>
                                <td class="py-3 text-sm">₦{{ item.price.toFixed(2) }}</td>
                                <td class="py-3 text-sm">{{ item.discount }}%</td>
                                <td class="py-3 text-right text-sm">₦{{ item.total.toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div class="mt-6 flex justify-end">
                    <div class="w-full max-w-xs">
                        <div class="flex justify-between border-b border-sidebar-border/50 py-2">
                            <span class="text-sm">Subtotal:</span>
                            <span class="text-sm">₦{{ invoice.subtotal.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-sidebar-border/50 py-2">
                            <span class="text-sm">Tax (7%):</span>
                            <span class="text-sm">₦{{ invoice.tax.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="font-medium">Total:</span>
                            <span class="font-bold">₦{{ invoice.total.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div v-if="invoice.notes" class="mt-8 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                    <h3 class="mb-2 font-medium">Notes:</h3>
                    <p class="text-sm">{{ invoice.notes }}</p>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Thank you for your business!</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@media print {
    @page { margin: 0; }
    body { margin: 1cm; }
    .print\:hidden { display: none !important; }
    .print\:p-0 { padding: 0 !important; }
}
</style>
