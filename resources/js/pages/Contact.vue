<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { Link } from '@inertiajs/vue3';
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-Leaflet/vue-leaflet';

// Salon coordinates (replace with your actual coordinates)
const salonLocation = {
    lat: 5.0332,  // Uyo latitude
    lng: 7.9275,  // Uyo longitude
    zoom: 8
};

const form = {
    name: '',
    email: '',
    phone: '',
    service: '',
    message: ''
};

const services = [
    'Facebook',
    'Instagram',
    'Google Search',
    'Friends',
];
</script>

<template>
    <HomeLayout>
        <div class="w-full">
            <div class="w-full relative h-64 mb-8 -mt-6 -mx-6 lg:-mx-8">
                <!-- Background Image -->
                <img src="/images/header-background-image.png" alt="About Header"
                    class="absolute inset-0 w-full h-full object-cover" />
                <!-- Dark Overlay -->
                <div class="absolute inset-0 bg-black/60 dark:bg-black/70"></div>

                <!-- Content -->
                <div class="relative h-full flex flex-col items-center justify-center py-8">
                    <h2 class="text-3xl font-bold mb-4 text-white">
                        Contact us
                    </h2>
                    <p class="text-xl font-sm mb-4 text-white">Reach us for enquiries and support</p>
                </div>
            </div>
            <!-- Add your contact page content -->

            <!--  Content Section -->
            <div class="max-w-6xl mx-auto px-6 py-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">

                    <!-- Content Column -->
                    <div class="lg:col-span-7">
                        <div class="space-y-6">
                            <h1 class="text-4xl font-bold text-gray-900 dark:text-[#EDEDEC]">
                                <span class="text-gray-700 dark:text-gray-700">GET IN </span>
                                <span class="text-red-500 dark:text-red-400">TOUCH</span>
                            </h1>
                            <h5 class="text-lg font-medium text-gray-600 dark:text-gray-400">
                                Our expert customer service team is always available to help out and provide support
                            </h5>
                            <div class="space-y-8 text-gray-700 dark:text-gray-300">

                                <!-- Contact Form -->
                                <form class="mt-8 space-y-6">
                                    <div class="space-y-6">
                                        <!-- Name Field -->
                                        <div class="flex flex-col space-y-2">
                                            <label for="name" class="text-sm font-medium">Name</label>
                                            <input type="text" id="name" v-model="form.name"
                                                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:border-gray-700"
                                                placeholder="Your full name">
                                        </div>

                                        <!-- Email Field -->
                                        <div class="flex flex-col space-y-2">
                                            <label for="email" class="text-sm font-medium">Email</label>
                                            <input type="email" id="email" v-model="form.email"
                                                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:border-gray-700"
                                                placeholder="your@email.com">
                                        </div>

                                        <!-- Phone Field -->
                                        <div class="flex flex-col space-y-2">
                                            <label for="phone" class="text-sm font-medium">Phone Number</label>
                                            <input type="tel" id="phone" v-model="form.phone"
                                                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:border-gray-700"
                                                placeholder="Your phone number">
                                        </div>

                                        <!-- Service Dropdown -->
                                        <div class="flex flex-col space-y-2">
                                            <label for="service" class="text-sm font-medium">How did you find us ?</label>
                                            <select id="service" v-model="form.service"
                                                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:border-gray-700">
                                                <option value="">Select a service</option>
                                                <option v-for="service in services" :key="service" :value="service">
                                                    {{ service }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Message Field -->
                                        <div class="flex flex-col space-y-2">
                                            <label for="message" class="text-sm font-medium">Message</label>
                                            <textarea id="message" v-model="form.message" rows="4"
                                                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:border-gray-700"
                                                placeholder="Your message..."></textarea>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="flex flex-col">
                                        <button type="submit"
                                            class="px-6 py-2 bg-red-500 text-white rounded-sm hover:bg-red-600 transition-colors duration-300">
                                            Send Message
                                        </button>
                                    </div>
                                </form>
                                
                                <!-- Address -->
                                <div class="flex items-start space-x-2 mt-8">
                                    <i class="fas fa-location-dot text-red-500 mt-1"></i>
                                    <div class="flex flex-col">
                                        <span class="font-semibold">Averette Unisex Salon</span>
                                        <span>51 URUA EKPA ROAD BY UDO USANGA STREET,</span>
                                        <span>ALONG UNIVERSITY OF UYO, UYO,</span>
                                        <span>AKWA IBOM STATE.</span>
                                    </div>
                                </div>

                                <!-- Opening Hours -->
                                <div class="items-center gap-2">
                                    <span class="my-4 items-center justify-center"><i class="far fa-bookmark text-blue-500"></i>
                                        <b class="mx-2">Opening Hours:</b>
                                    </span>
                                    <ul class="list-disc list-inside">
                                        <li>Monday - Friday: 9:00 AM - 6:00 PM</li>
                                        <li>Saturday: 10:00 AM - 5:00 PM</li>
                                        <li>Sunday: Closed</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Column -->
                    <div class="lg:col-span-5 h-[400px] rounded-lg shadow-lg overflow-hidden">
                        <LMap v-model:zoom="salonLocation.zoom" :center="[salonLocation.lat, salonLocation.lng]"
                            class="h-full w-full">
                            <LTileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                                attribution="&copy; OpenStreetMap contributors" />
                            <LMarker :lat-lng="[salonLocation.lat, salonLocation.lng]">
                                <LPopup>
                                    Averette Salon<br>
                                    51 Urua Ekpa Road, By Udo Usanga Street<br>
                                    Along University of Uyo, Uyo<br>
                                    Akwa Ibom State
                                </LPopup>
                            </LMarker>
                        </LMap>
                    </div>


                </div>
            </div>
        </div>
    </HomeLayout>
</template>
<style>
@import "leaflet/dist/leaflet.css";
</style>