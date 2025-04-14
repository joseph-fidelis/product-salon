<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';

const form = {
    name: '',
    email: '',
    phone: '',
    bookingDate: '',
    service: '',
    message: ''
};

// List of services
const services = [
    'Haircut',
    'Braiding',
    'Lash Extensions',
    'Manicure & Pedicure',
    'Tattoo Art'
];


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
                        <span class="text-gray-700 dark:text-gray-300">BOOK A </span>
                        <span class="text-red-500 dark:text-red-400">SERVICE</span>
                    </h1>
                </div>
            </div>

            <!-- Booking Form Section -->
            <div class="max-w-4xl mx-auto px-6 py-12 bg-white dark:bg-gray-900 rounded-lg shadow-lg">
                <form class="space-y-6">
                    <!-- Name Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" id="name" v-model="form.name"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Your full name" />
                    </div>

                    <!-- Email Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" id="email" v-model="form.email"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Your email address" />
                    </div>

                    <!-- Phone Number Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="phone" class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone
                            Number</label>
                        <input type="tel" id="phone" v-model="form.phone"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Your phone number" />
                    </div>

                    <!-- Booking Date Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="bookingDate" class="text-sm font-medium text-gray-700 dark:text-gray-300">Booking
                            Date</label>
                        <input type="date" id="bookingDate" v-model="form.bookingDate"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600" />
                    </div>

                    <!-- Service Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="service"
                            class="text-sm font-medium text-gray-700 dark:text-gray-300">Service</label>
                        <select id="service" v-model="form.service"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600">
                            <option value="" disabled>Select a service</option>
                            <option v-for="service in services" :key="service" :value="service">
                                {{ service }}
                            </option>
                        </select>
                    </div>

                    <!-- Message Field -->
                    <div class="flex flex-col space-y-2">
                        <label for="message"
                            class="text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                        <textarea id="message" v-model="form.message" rows="4"
                            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600"
                            placeholder="Additional details or requests"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col">
                        <button type="submit"
                            class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                            Submit Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </HomeLayout>
</template>