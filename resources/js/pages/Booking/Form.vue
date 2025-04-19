<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Define props for services coming from the database
const props = defineProps({
    services: {
        type: Array,
        required: true
    },
    availableDates: {
        type: Array,
        default: () => []
    }
});

// Form handling with Inertia
const form = useForm({
    name: '',
    email: '',
    phone: '',
    referral_code: '', // Added referral_code to the form data
    booking_date: '',
    booking_time: '',
    service_id: '',
    message: ''
});

// You can add validation for the referral code if needed
const validateReferralCode = (code) => {
    // Example validation - implement according to your business rules
    return code.length <= 15; // For example, ensuring the code is not too long
};

// Selected service details
const selectedService = computed(() => {
    if (!form.service_id) return null;
    return props.services.find(service => service.id.toString() === form.service_id.toString());
});

// Submit the form
const submitForm = () => {
    form.post('/book-appointment', {
        preserveScroll: true,
        onSuccess: () => {
            alert('Your booking has been received. We will contact you to confirm.');
            form.reset();
        }
    });
};

// Format currency
const formatCurrency = (amount) => {
    return `₦${parseFloat(amount).toFixed(2)}`;
};
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
                    <h1 class="text-4xl font-bold mb-6">
                        <span class="text-gray-100 dark:text-gray-300">BOOK A </span>
                        <span class="text-red-500 dark:text-red-400">SERVICE</span>
                    </h1>
                </div>
            </div>

            <!-- Booking Form Section -->
            <div class="max-w-4xl mx-auto px-6 py-12 bg-white dark:bg-gray-900 rounded-lg shadow-lg">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Name Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" id="name" v-model="form.name"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Your full name" required />
                        <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
                    </div>

                    <!-- Email Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" id="email" v-model="form.email"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Your email address" />
                        <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>
                    </div>

                    <!-- Phone Number Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="phone" class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                        <input type="tel" id="phone" v-model="form.phone"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Your phone number" required />
                        <div v-if="form.errors.phone" class="text-red-500 text-sm">{{ form.errors.phone }}</div>
                    </div>

                     <!-- Referral Code Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="referral_code" class="text-sm font-medium text-gray-700 dark:text-gray-300">Referral Code</label>
                        <input type="text" id="referral_code" v-model="form.referral_code"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Referral Code" />
                        <div v-if="form.errors.referral_code" class="text-red-500 text-sm">{{ form.errors.referral_code }}</div>
                    </div>

                    <!-- Service Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="service" class="text-sm font-medium text-gray-700 dark:text-gray-300">Service</label>
                        <select id="service" v-model="form.service_id"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            required>
                            <option value="" disabled>Select a service</option>
                            <option v-for="service in services" :key="service.id" :value="service.id">
                                {{ service.name }} ({{ formatCurrency(service.price) }})
                            </option>
                        </select>
                        <div v-if="form.errors.service_id" class="text-red-500 text-sm">{{ form.errors.service_id }}</div>
                    </div>

                    <!-- Selected service details -->
                    <div v-if="selectedService" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-2">{{ selectedService.name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ selectedService.description }}</p>
                        <div class="flex justify-between text-sm">
                            <span>Duration: {{ selectedService.timeEstimate }} minutes</span>
                            <span class="font-medium">Price: {{ formatCurrency(selectedService.price) }}</span>
                        </div>
                    </div>

                    <!-- Date and Time Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col space-y-2">
                            <label for="booking_date" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Booking Date
                            </label>
                            <input type="date" id="booking_date" v-model="form.booking_date"
                                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                                required />
                            <div v-if="form.errors.booking_date" class="text-red-500 text-sm">{{ form.errors.booking_date }}</div>
                        </div>

                        <div class="flex flex-col space-y-2">
                            <label for="booking_time" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Booking Time
                            </label>
                            <input type="time" id="booking_time" v-model="form.booking_time"
                                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                                required />
                            <div v-if="form.errors.booking_time" class="text-red-500 text-sm">{{ form.errors.booking_time }}</div>
                        </div>
                    </div>

                    <!-- Message Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="message" class="text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                        <textarea id="message" v-model="form.message" rows="4"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Additional details or requests"></textarea>
                        <div v-if="form.errors.message" class="text-red-500 text-sm">{{ form.errors.message }}</div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col">
                        <button type="submit"
                            class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="form.processing">
                            <span v-if="form.processing">Processing...</span>
                            <span v-else>Submit Booking</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </HomeLayout>
</template>
