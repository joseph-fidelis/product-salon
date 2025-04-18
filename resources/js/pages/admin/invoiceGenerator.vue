<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  services: Array,
  staffMembers: Array
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
        title: 'Generate',
        href: '/admin/invoice/generate',
    },
];

interface Service {
    id: number;
    name: string;
    price: number;
    timeEstimate: number;
}

interface StaffMember {
    id: number;
    first_name: string;
    last_name: string;
    commission: number;
}

interface SelectedServiceItem {
    service_id: string;
    staff_id: string;
    quantity: number;
    price: number;
    discount: number;
}

interface InvoiceItem {
    service_id: string;
    service_name: string;
    staff_id: string;
    staff_name: string;
    quantity: number;
    price: number;
    discount: number;
    total: number;
    commission: number;
}

// Form for invoice generation
const form = useForm({
    customer_name: '',
    customer_phone: '',
    invoice_date: new Date().toISOString().substr(0, 10),
    invoice_items: [] as InvoiceItem[],
    notes: '',
    payment_method: 'Cash',
});

// Track selected services with staff assigned to each
const selectedServices = ref<SelectedServiceItem[]>([]);

// Add a new service line item
const addServiceItem = () => {
    selectedServices.value.push({
        service_id: '',
        staff_id: '',
        quantity: 1,
        price: 0,
        discount: 0,
    });
};

// Remove a service line item
const removeServiceItem = (index: number) => {
    selectedServices.value.splice(index, 1);
};

// Get service details when a service is selected
const updateServiceDetails = (index: number, serviceId: string) => {
    const service = props.services.find(s => s.id === parseInt(serviceId));
    if (service) {
        selectedServices.value[index].price = service.price;
    }
};

// Calculate subtotal for a line item
const calculateItemTotal = (item: SelectedServiceItem) => {
    const subtotal = item.price * item.quantity;
    const discount = subtotal * (item.discount / 100);
    return subtotal - discount;
};

// Calculate invoice subtotal (which is also the total since there's no tax)
const total = computed(() => {
    return selectedServices.value.reduce((sum, item) => sum + calculateItemTotal(item), 0);
});

// Calculate staff commission
const getStaffCommission = (staffId: string, amount: number) => {
    const staff = props.staffMembers.find(s => s.id === parseInt(staffId));
    return staff ? (amount * staff.commission / 100) : 0;
};

// Submit the form
// Submit the form
const submitInvoice = () => {
    // Prepare the invoice items from selected services
    form.invoice_items = selectedServices.value.map(item => {
        const service = props.services.find(s => s.id === parseInt(item.service_id));
        const staff = props.staffMembers.find(s => s.id === parseInt(item.staff_id));

        return {
            service_id: item.service_id,
            service_name: service ? service.name : '',
            staff_id: item.staff_id,
            staff_name: staff ? `${staff.first_name} ${staff.last_name}` : '',
            quantity: item.quantity,
            price: item.price,
            discount: item.discount,
            total: calculateItemTotal(item),
            commission: getStaffCommission(item.staff_id, calculateItemTotal(item))
        };
    });

    // Submit the form to your backend using the correct route
    form.post('/admin/invoice/store', {
        onSuccess: (response) => {
            // Clear the form and selected services
            selectedServices.value = [];
            form.reset();

            // Manually redirect to the show page
            const invoiceId = response?.invoice?.id;
            if (invoiceId) {
                window.location.href = `/admin/invoice/${invoiceId}`;
            } else {
                // Fallback to the invoice list if we can't get the ID
                window.location.href = '/admin/invoice';
            }
        },
        onError: (errors) => {
            console.error('Invoice submission errors:', errors);
        }
    });
};

// Initialize with one service line
addServiceItem();
</script>

<template>
    <Head title="Generate Invoice" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Create New Invoice</h1>
                <button @click="submitInvoice" class="rounded-lg bg-primary px-4 py-2 text-white hover:bg-primary/90">
                    Generate Invoice
                </button>
            </div>

            <div class="grid gap-8 md:grid-cols-2">
                <!-- Customer Information -->
                <div class="rounded-xl border border-sidebar-border/70 p-6">
                    <h2 class="mb-4 text-lg font-medium">Customer Information</h2>
                    <div class="grid gap-4">
                        <div>
                            <label for="customer_name" class="mb-1 block text-sm">Customer Name</label>
                            <input
                                id="customer_name"
                                v-model="form.customer_name"
                                type="text"
                                class="w-full rounded-lg border border-sidebar-border/70 px-3 py-2"
                                placeholder="Enter customer name"
                            />
                            <p v-if="form.errors.customer_name" class="mt-1 text-sm text-red-500">{{ form.errors.customer_name }}</p>
                        </div>

                        <div>
                            <label for="customer_phone" class="mb-1 block text-sm">Phone</label>
                            <input
                                id="customer_phone"
                                v-model="form.customer_phone"
                                type="text"
                                class="w-full rounded-lg border border-sidebar-border/70 px-3 py-2"
                                placeholder="Enter phone number"
                            />
                            <p v-if="form.errors.customer_phone" class="mt-1 text-sm text-red-500">{{ form.errors.customer_phone }}</p>
                        </div>

                        <div>
                            <label for="invoice_date" class="mb-1 block text-sm">Invoice Date</label>
                            <input
                                id="invoice_date"
                                v-model="form.invoice_date"
                                type="date"
                                class="w-full rounded-lg border border-sidebar-border/70 px-3 py-2"
                            />
                            <p v-if="form.errors.invoice_date" class="mt-1 text-sm text-red-500">{{ form.errors.invoice_date }}</p>
                        </div>

                        <div>
                            <label for="payment_method" class="mb-1 block text-sm">Payment Method</label>
                            <select
                                id="payment_method"
                                v-model="form.payment_method"
                                class="w-full rounded-lg border border-sidebar-border/70 px-3 py-2"
                            >
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Mobile Payment">Mobile Payment</option>
                            </select>
                            <p v-if="form.errors.payment_method" class="mt-1 text-sm text-red-500">{{ form.errors.payment_method }}</p>
                        </div>
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div class="rounded-xl border border-sidebar-border/70 p-6">
                    <h2 class="mb-4 text-lg font-medium">Invoice Summary</h2>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between pt-2">
                            <span class="font-medium">Total:</span>
                            <span class="text-lg font-semibold">₦{{ total.toFixed(2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="notes" class="mb-1 block text-sm">Notes</label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="4"
                            class="w-full rounded-lg border border-sidebar-border/70 px-3 py-2"
                            placeholder="Add any additional notes"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Service Selection -->
            <div class="rounded-xl border border-sidebar-border/70 p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-medium">Services</h2>
                    <button
                        @click="addServiceItem"
                        class="flex items-center rounded-lg bg-gray-100 px-3 py-1 text-sm hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Service
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="border-b text-left text-sm">
                            <tr>
                                <th class="pb-2">Service</th>
                                <th class="pb-2">Staff</th>
                                <th class="pb-2">Qty</th>
                                <th class="pb-2">Price</th>
                                <th class="pb-2">Disc (%)</th>
                                <th class="pb-2">Total</th>
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in selectedServices" :key="index" class="border-b">
                                <td class="py-3 pr-3">
                                    <select
                                        v-model="item.service_id"
                                        @change="updateServiceDetails(index, item.service_id)"
                                        class="w-full rounded-lg border border-sidebar-border/70 px-2 py-1 text-sm"
                                    >
                                        <option value="" disabled>Select service</option>
                                        <option v-for="service in services" :key="service.id" :value="service.id">
                                            {{ service.name }} (₦{{ service.price }})
                                        </option>
                                    </select>
                                </td>
                                <td class="py-3 pr-3">
                                    <select
                                        v-model="item.staff_id"
                                        class="w-full rounded-lg border border-sidebar-border/70 px-2 py-1 text-sm"
                                    >
                                        <option value="" disabled>Select staff</option>
                                        <option v-for="staff in staffMembers" :key="staff.id" :value="staff.id">
                                            {{ staff.first_name }} {{ staff.last_name }}
                                        </option>
                                    </select>
                                </td>
                                <td class="py-3 pr-3">
                                    <input
                                        type="number"
                                        v-model="item.quantity"
                                        min="1"
                                        class="w-16 rounded-lg border border-sidebar-border/70 px-2 py-1 text-sm"
                                    />
                                </td>
                                <td class="py-3 pr-3">
                                    <input
                                        type="number"
                                        v-model="item.price"
                                        min="0"
                                        step="0.01"
                                        class="w-24 rounded-lg border border-sidebar-border/70 px-2 py-1 text-sm"
                                    />
                                </td>
                                <td class="py-3 pr-3">
                                    <input
                                        type="number"
                                        v-model="item.discount"
                                        min="0"
                                        max="100"
                                        class="w-16 rounded-lg border border-sidebar-border/70 px-2 py-1 text-sm"
                                    />
                                </td>
                                <td class="py-3 pr-3">₦{{ calculateItemTotal(item).toFixed(2) }}</td>
                                <td class="py-3">
                                    <button
                                        @click="removeServiceItem(index)"
                                        class="rounded-full p-1 hover:bg-gray-100 dark:hover:bg-gray-800"
                                        :disabled="selectedServices.length === 1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
