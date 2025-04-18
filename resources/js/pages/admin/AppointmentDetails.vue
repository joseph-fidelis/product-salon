<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';


const props = defineProps({
    appointment: Object,
    availableStaff: Array,
    availableServices: Array,
});

// State for editing
const editing = ref(false);
const form = ref({
    customer_name: props.appointment.customer_name,
    customer_email: props.appointment.customer_email,
    customer_phone: props.appointment.customer_phone,
    appointment_date: props.appointment.appointment_date,
    appointment_time: props.appointment.appointment_time,
    status: props.appointment.status,
    notes: props.appointment.notes,
    services: [...props.appointment.services]
});

// Status options
const statusOptions = [
    { value: 'Pending', label: 'Pending' },
    { value: 'Approved', label: 'Approved' },
    { value: 'Completed', label: 'Completed' },
    { value: 'Cancelled', label: 'Cancelled' },
    { value: 'No-Show', label: 'No-Show' }
];

// For adding a new service
const showServiceForm = ref(false);
const newService = ref({
    id: '',
    staff_id: '',
    notes: ''
});

// Format time for display
const formatTime = (time) => {
    if (!time) return '';
    const [hours, minutes] = time.split(':');
    const formattedTime = new Date();
    formattedTime.setHours(hours, minutes);
    return formattedTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

// Format date for display
const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
};

// Get status class for styling
const getStatusClass = (status) => {
    const statusClasses = {
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Approved': 'bg-blue-100 text-blue-800',
        'Completed': 'bg-green-100 text-green-800',
        'Cancelled': 'bg-red-100 text-red-800',
        'No-Show': 'bg-gray-100 text-gray-800'
    };

    return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

// Format currency
const formatCurrency = (amount) => {
    return `₦${parseFloat(amount || 0).toFixed(2)}`;
};

// Check if all services have staff assigned
const allServicesHaveStaff = computed(() => {
    return form.value.services.every(service => service.staff_id);
});

// Calculate total amount
const totalAmount = computed(() => {
    return form.value.services.reduce((sum, service) => sum + parseFloat(service.price || 0), 0);
});

// Toggle edit mode
const toggleEdit = () => {
    if (editing.value) {
        // Reset form if cancelling
        form.value = {
            customer_name: props.appointment.customer_name,
            customer_email: props.appointment.customer_email,
            customer_phone: props.appointment.customer_phone,
            appointment_date: props.appointment.appointment_date,
            appointment_time: props.appointment.appointment_time,
            status: props.appointment.status,
            notes: props.appointment.notes,
            services: [...props.appointment.services]
        };
    }
    editing.value = !editing.value;
};

// Save changes
const saveChanges = () => {
    router.put(route('admin.appointments.update', props.appointment.id), {
        customer_name: form.value.customer_name,
        customer_email: form.value.customer_email,
        customer_phone: form.value.customer_phone,
        appointment_date: form.value.appointment_date,
        appointment_time: form.value.appointment_time,
        status: form.value.status,
        notes: form.value.notes,
        services: form.value.services.map(service => ({
            id: service.id,
            staff_id: service.staff_id,
            notes: service.notes
        }))
    });
};

// Update status only
const updateStatus = () => {
    router.put(route('admin.appointments.update.status', props.appointment.id), {
        status: form.value.status
    });
};

// Assign staff to service
const assignStaff = (serviceIndex, staffId) => {
    // Update in the form
    form.value.services[serviceIndex].staff_id = staffId;

    // If not in edit mode, also send request to update in backend
    if (!editing.value) {
        router.put(route('admin.appointments.assign.staff', props.appointment.id), {
            service_id: form.value.services[serviceIndex].id,
            staff_id: staffId
        });
    }
};

// Remove service
const removeService = (index) => {
    form.value.services.splice(index, 1);
};

// Add new service
const addService = () => {
    // Find the service details
    const selectedService = props.availableServices.find(s => s.id.toString() === newService.value.id.toString());
    if (!selectedService) return;

    // Add to services array
    form.value.services.push({
        id: selectedService.id,
        name: selectedService.name,
        price: selectedService.price,
        staff_id: newService.value.staff_id || null,
        notes: newService.value.notes || null
    });

    // Reset form
    newService.value = { id: '', staff_id: '', notes: '' };
    showServiceForm.value = false;
};

// Convert to invoice
const convertToInvoice = () => {
    if (confirm('Are you sure you want to convert this appointment to an invoice?\nThis will create an invoice with all assigned services.')) {
        router.post(route('admin.appointments.convert.invoice', props.appointment.id));
    }
};

// Confirm delete
const confirmDelete = () => {
    if (props.appointment.invoice_id) {
        alert('Cannot delete an appointment that has been converted to an invoice.');
        return;
    }

    if (confirm('Are you sure you want to delete this appointment?\nThis action cannot be undone.')) {
        router.delete(route('admin.appointments.destroy', props.appointment.id));
    }
};
</script>

<template>
    <Head :title="`Appointment #${appointment.id}`" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Appointment #{{ appointment.id }}
                </h2>
                <div class="flex gap-2">
                    <button
                        v-if="!editing"
                        @click="toggleEdit"
                        class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md font-medium"
                    >
                        Edit Appointment
                    </button>
                    <template v-else>
                        <button
                            @click="saveChanges"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium"
                        >
                            Save Changes
                        </button>
                        <button
                            @click="toggleEdit"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md font-medium"
                        >
                            Cancel
                        </button>
                    </template>

                    <button
                        v-if="appointment.can_convert_to_invoice && allServicesHaveStaff"
                        @click="convertToInvoice"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium"
                    >
                        Convert to Invoice
                    </button>

                    <Link
                        v-if="appointment.invoice_id"
                        :href="route('admin.invoice.show', appointment.invoice_id)"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium"
                    >
                        View Invoice
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Status Badge -->
                <div class="mb-6 flex items-center gap-4">
                    <span class="font-medium">Status:</span>

                    <!-- When not in edit mode, show status with quick-edit capability -->
                    <div v-if="!editing" class="flex items-center gap-3">
                        <span :class="[getStatusClass(appointment.status), 'px-3 py-1 rounded-full']">
                            {{ appointment.status }}
                        </span>

                        <!-- Quick status change dropdown -->
                        <select
                            v-model="form.status"
                            class="ml-4 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                        >
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>

                        <button
                            v-if="form.status !== appointment.status"
                            @click="updateStatus"
                            class="bg-primary hover:bg-primary-dark text-black px-3 py-1 rounded-md text-sm"
                        >
                            Update Status
                        </button>
                    </div>

                    <!-- When in edit mode, show only the status dropdown -->
                    <div v-else>
                        <select
                            v-model="form.status"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                        >
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Customer Information</h3>

                        <div v-if="!editing" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                <div class="text-gray-900 dark:text-gray-100">{{ appointment.customer_name }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <div class="text-gray-900 dark:text-gray-100">{{ appointment.customer_phone || 'N/A' }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <div class="text-gray-900 dark:text-gray-100">{{ appointment.customer_email || 'N/A' }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Appointment Date & Time</label>
                                <div class="text-gray-900 dark:text-gray-100">
                                    {{ formatDate(appointment.appointment_date) }} at {{ formatTime(appointment.appointment_time) }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                                <div class="text-gray-900 dark:text-gray-100">{{ appointment.notes || 'No notes' }}</div>
                            </div>
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                <input
                                    id="customer_name"
                                    v-model="form.customer_name"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                    required
                                />
                            </div>

                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <input
                                    id="customer_phone"
                                    v-model="form.customer_phone"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                />
                            </div>

                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input
                                    id="customer_email"
                                    v-model="form.customer_email"
                                    type="email"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                />
                            </div>

                            <div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="appointment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                        <input
                                            id="appointment_date"
                                            v-model="form.appointment_date"
                                            type="date"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label for="appointment_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                                        <input
                                            id="appointment_time"
                                            v-model="form.appointment_time"
                                            type="time"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services & Staff Assignment -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Services & Staff Assignment</h3>
                            <button
                                v-if="editing && !showServiceForm"
                                @click="showServiceForm = true"
                                class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded-md text-sm"
                            >
                                Add Service
                            </button>
                        </div>

                        <!-- Add Service Form -->
                        <div v-if="showServiceForm" class="bg-gray-50 dark:bg-gray-700 p-4 mb-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Add New Service</h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service</label>
                                    <select
                                        v-model="newService.id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">Select a service</option>
                                        <option v-for="service in availableServices" :key="service.id" :value="service.id">
                                            {{ service.name }} ({{ formatCurrency(service.price) }})
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign Staff</label>
                                    <select
                                        v-model="newService.staff_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                    >
                                        <option value="">Select staff member</option>
                                        <option v-for="staff in availableStaff" :key="staff.id" :value="staff.id">
                                            {{ staff.name }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                                    <input
                                        v-model="newService.notes"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                        placeholder="Optional notes"
                                    />
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end gap-2">
                                <button
                                    @click="addService"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-sm"
                                    :disabled="!newService.id"
                                >
                                    Add
                                </button>
                                <button
                                    @click="showServiceForm = false"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-md text-sm"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>

                        <!-- Services Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Service
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Price
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Staff Assignment
                                        </th>
                                        <th v-if="editing" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="(service, index) in form.services" :key="service.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ service.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ formatCurrency(service.price) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select
                                                v-model="service.staff_id"
                                                @change="assignStaff(index, service.staff_id)"
                                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm"
                                                :disabled="appointment.invoice_id && !editing"
                                            >
                                                <option value="">Assign staff member</option>
                                                <option v-for="staff in availableStaff" :key="staff.id" :value="staff.id">
                                                    {{ staff.name }}
                                                </option>
                                            </select>
                                        </td>
                                        <td v-if="editing" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <button
                                                @click="removeService(index)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Empty State -->
                                    <tr v-if="form.services.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No services added to this appointment
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            Total
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ formatCurrency(totalAmount) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Warning for Staff Assignment -->
                        <div v-if="!allServicesHaveStaff && !appointment.invoice_id" class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-md">
                            Please assign staff to all services to be able to convert this appointment to an invoice.
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-red-600 dark:text-red-400 mb-4">Danger Zone</h3>

                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Deleting an appointment cannot be undone. This will permanently remove all associated data.
                        </p>

                        <button
                            @click="confirmDelete"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium"
                            :disabled="appointment.invoice_id"
                        >
                            Delete Appointment
                        </button>

                        <p v-if="appointment.invoice_id" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            This appointment cannot be deleted because it has been converted to an invoice.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
